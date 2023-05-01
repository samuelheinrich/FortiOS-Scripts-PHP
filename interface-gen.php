<!DOCTYPE html>
<html>
<head>
<title>Interface Generator</title>
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
    </script>

</head>
<body>
<h1>FSOS Interface generator</h1>
<br>
<h2>Beispiel CSV Datei</h2>
<a href="interface-gen.csv" download>Download interface-gen.csv</a>
<form action="interface-gen.php" method="post" enctype="multipart/form-data">
    <label for="csv_file">CSV-Datei hochladen:</label>
    <input type="file" name="csv_file" id="csv_file" accept=".csv"><br>
    <label for="interface_data">Oder Textfeld verwenden (vlanname,vdom,ip,allowaccess,description,alias,role,interface,vlanid,status):</label>
    <textarea name="interface_data" id="interface_data" rows="4">v10003,root,192.168.1.1/24,ping,internal web interface,int-web,lan,lacp-trunk1,disabled</textarea>
    <input type="submit" value="Absenden">
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $interface_data = $_POST["interface_data"];

    if (empty($interface_data) && !empty($_FILES['csv_file']['tmp_name'])) {
        $interface_data = file_get_contents($_FILES['csv_file']['tmp_name']);
    }

    $lines = explode("\n", $interface_data);
    $output = "";


    foreach ($lines as $line) {
        list($vlanname, $vdom, $ip, $allowaccess, $description, $alias, $role, $interface, $vlanid, $status) = explode(",", $line);

        $output .= "config system interface\n";
        $output .= "    edit \"$vlanname\"\n";
        $output .= "        set vdom \"$vdom\"\n";
        $output .= "        set ip $ip\n";
        $output .= "        set allowaccess $allowaccess\n";
        $output .= "        set description \"$vlanname $description\"\n";
        $output .= "        set alias \"$alias\"\n";
        $output .= "        set role $role\n";
        $output .= "        set interface \"$interface\"\n";
        $output .= "        set vlanid $vlanid\n";
        $output .= "    next\n";
        $output .= "end\n";
    }
}
?>

<?php if (!empty($output)): ?>
    <label for="output">Ausgabe:</label>
    <textarea id="output" rows="16" readonly><?php echo htmlspecialchars($output); ?></textarea>
    <button onclick="copyToClipboard()">In Zwischenablage kopieren</button>
<?php endif; ?>

<br><br><br>
<a href="index.php">Zurück zum Index</a><br>
</body>
</html>