<!DOCTYPE html>
<html>
<head>
<title>Address Objekt Generator</title>
<style>
    form {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    textarea {
        width: 50%; /* Ändere diesen Wert, um die Breite des Textfelds anzupassen, z.B. 500px oder 100% */
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

<h1>FSOS Address Objekt generator</h1>
<br>
<h2>Beispiel CSV Datei</h2>
<a href="address-gen.csv" download>Download address-gen.csv</a>


<form action="addr-gen.php" method="post">
<!-- <label for="csv_file">CSV-Datei hochladen:</label>
<input type="file" name="csv_file" id="csv_file" accept=".csv"> -->
<label for="name_subnet_comment">oder Textfeld verwenden (name,subnet,comment):</label>
    <textarea name="name_subnet_comment" id="name_subnet_comment" rows="8">
h-192.168.10.11,192.168.10.1/32,z-int vl10 Server 11
n-192.168.10.0/24,192.168.10.0/24,z-int vl10 subnet
hr-192.168.20.11,192.168.10.11/32,z-vpn branch Server
nr-192.168.20.0/24,192.168.20.0/24,z-vpn branch subnet    
    </textarea>
    <input type="submit" value="Generien">
    <label for="group_name">Gruppenname: zB. "hg- (HostGroup), ng- (NetworkGroup)</label>
    <input type="text" name="group_name" id="group_name" placeholder="ng-xxx">
    <label for="group_description">Gruppenbeschreibung: (zone, vlan, name)"</label>
    <input type="text" name="group_description" id="group_description" placeholder="z-int vl0012 host..."> 

    <input type="submit" name="with_group" value="Generien mit Gruppe">
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_lines = $_POST['name_subnet_comment'];

    if (empty($input_lines) && !empty($_FILES['csv_file']['tmp_name'])) {
        $input_lines = file_get_contents($_FILES['csv_file']['tmp_name']);
    }

    $lines = explode("\n", $input_lines);
    $output = '';
    $names = [];

    foreach ($lines as $line) {
        $parts = explode(",", $line);
        $name = trim($parts[0]);
        $subnet = trim($parts[1]);
        $comment = trim($parts[2]);
        $names[] = $name;

        $config_code = <<<EOT
config firewall address
    edit "$name"
        set comment "$comment"
        set subnet $subnet
    next
end
EOT;

        $output .= $config_code . "\n";
    }

    if (isset($_POST['with_group'])) {
        $group_name = $_POST['group_name'];
        $group_description = $_POST['group_description'];
        $member_list = implode('" "', $names);

        $config_group = <<<EOT
config firewall addrgrp
    edit "$group_name"
        set member "$member_list"
        set comment "$group_description"
    next
end
EOT;

        $output .= $config_group . "\n";
    }

    echo "<textarea id='output' rows='40' readonly>" . htmlspecialchars($output) . "</textarea>";
    echo "<button onclick='copyToClipboard()' style='display: block;'>In die Zwischenablage kopieren</button>";
}
?>


<br><br><br>
<a href="index.php">Zurück zum Index</a><br>
</body>
</html>