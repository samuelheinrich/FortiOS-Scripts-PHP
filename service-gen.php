<!DOCTYPE html>
<html>
<head>
<title>Service Objekt Generator</title>
<style>
    form {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    textarea {
        width: 50%;
        padding: 4px;
    }

    input[type="text"], input[type="number"] {
        width: 50%;
        padding: 4px;
    }
    label {
        display: block;
        margin-bottom: 4px;
    }
</style>
<script>
        function copyToClipboard() {
            const outputArea = document.getElementById('output');
            outputArea.select();
            document.execCommand('copy');
        }
        function clearTextarea() {
             document.getElementById("name_tcp_udp_comment").value = "";
        }
    </script>

</head>
<body>

<h1>FSOS Service Objekt generator</h1>
<br>
<h2>Beispiel CSV Datei</h2>
<a href="service-gen.csv" download>Download service-gen.csv</a>


<form action="service-gen.php" method="post" enctype="multipart/form-data">
    <label for="csv_file">CSV-Datei hochladen:</label>
    <input type="file" name="csv_file" id="csv_file" accept=".csv" onchange="clearTextarea()">
    <label for="name_tcp_udp_comment">oder Textfeld verwenden (name,tcp,udp,comment):</label>
    <textarea name="name_tcp_udp_comment" id="name_tcp_udp_comment" rows="4">s-tcp/8953,8953,,Beispiel TCP Objekt
s-udp/8953,,8953,Beispiel UDP Objekt
s-udp/1000-2000,,1000-2000,Beispiel UDP Objekt mit Range 1000-2000</textarea>
    <input type="submit" value="Generien">
    <label for="group_name">Gruppenname: zB. "sg-(service gruppen name)</label>
    <input type="text" name="group_name" id="group_name" placeholder="sg-xxx">
    <label for="group_description">Gruppenbeschreibung: </label>
    <input type="text" name="group_description" id="group_description" placeholder="servicegroupe mit ports fuer..">
    <input type="submit" name="with_group" value="Generien mit Gruppe">
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_lines = $_POST['name_tcp_udp_comment'];

    if (empty($input_lines) && !empty($_FILES['csv_file']['tmp_name'])) {
        $input_lines = file_get_contents($_FILES['csv_file']['tmp_name']);
    }

    $lines = explode("\n", $input_lines);
    $output = '';
    $names = [];

    foreach ($lines as $line) {
        $parts = explode(",", $line);
        $name = trim($parts[0]);
        $tcp = trim($parts[1]);
        $udp = trim($parts[2]);
        $comment = trim(implode(",", array_slice($parts, 3))); // Combine the remaining parts for the comment
        $names[] = $name;

        $config_code = "config firewall service custom\n";
        $config_code .= "    edit \"$name\"\n";
        $config_code .= "        set comment \"$comment\"\n";
        if (!empty($tcp)) {
            $config_code .= "        set tcp-portrange $tcp\n";
        }
        if (!empty($udp)) {
            $config_code .= "        set udp-portrange $udp\n";
        }
        $config_code .= "    next\n";
        $config_code .= "end\n\n";
        $output .= $config_code;
    }

    if (isset($_POST['with_group'])) {
        $group_name = $_POST['group_name'];
        $group_description = $_POST['group_description'];
        $member_list = implode('" "', $names);

        $config_group = "config firewall service group\n";
        $config_group .= "    edit \"$group_name\"\n";
        $config_group .= "        set member \"$member_list\"\n";
        $config_group .= "        set comment \"$group_description\"\n";
        $config_group .= "    next\n";
        $config_group .= "end\n\n";
        $output .= $config_group;
    }

    echo "<textarea id='output' rows='40' style='width: 100%;' readonly>" . htmlspecialchars($output) . "</textarea>";
    echo "<button onclick='copyToClipboard()' style='display: block;'>In die Zwischenablage kopieren</button>";
}
?>


<br><br><br>
<a href="index.php">Zurück zum Index</a><br>
</body>
</html>