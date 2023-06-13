<!DOCTYPE html>
<html>
<head>
<title>Address Objekt Generator</title>
<style>
    /* Style-Anweisungen für das Formular-Element */
form {
    display: flex; /* Legt das Display-Modell auf Flexbox fest */
    flex-direction: column; /* Stellt die Flex-Elemente in einer Spalte an */
    align-items: flex-start; /* Ausrichten der Flex-Elemente am Anfang der flex container */
    gap: 5px; /* Abstand zwischen den Flex-Elementen */
}

/* Style-Anweisungen für Text- und Zahlen-Eingabeelemente */
input[type="text"], input[type="number"] {
    width: 40%; /* Legt die Breite der Elemente auf 40% fest */
    padding: 4px; /* Fügt Polsterung innerhalb des Elements hinzu */
}

/* Style-Anweisungen für Label-Elemente */
label {
    display: block; /* Legt das Display-Modell auf Block fest */
    margin-bottom: 4px; /* Fügt einen unteren Rand hinzu */
}

/* Style-Anweisungen für die Formular-Container-Klasse */
.form-container {
    display: grid; /* Legt das Display-Modell auf Grid fest */
    grid-template-columns: 1fr 1fr; /* Definiert zwei gleich große Spalten */
    grid-template-rows: auto auto auto; /* Definiert drei Zeilen mit automatischer Größe */
    grid-gap: 10px; /* Abstand zwischen den Grid-Elementen */
}

/* Style-Anweisungen für die vollständige Breite der Grid-Elemente */
.full-width {
    grid-column: 1 / span 2; /* Lässt das Element zwei Spalten einnehmen */
}

/* Stilisiert den Body-Tag */
body {
    font-family: Arial, sans-serif; /* Setzt die Schriftart */
    background-color: #f0f0f0; /* Setzt die Hintergrundfarbe */
    display: flex; /* Setzt das Display-Modell auf Flexbox */
    justify-content: center; /* Zentriert den Inhalt horizontal */
    align-items: center; /* Zentriert den Inhalt vertikal */
    height: 100vh; /* Setzt die Höhe auf 100% des Viewports */
    margin: 0; /* Entfernt den Standardabstand */
}

/* Stilisiert den Container */
.container {
    background-color: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    max-width: 960px;
    width: 80%;
    margin: auto;
    min-height: 100vh;
}

/* Stilisiert die h1-Überschrift */
h1 {
    text-align: center; /* Zentriert den Text */
}

/* Stilisiert das Formular */
form {
    display: grid; /* Setzt das Display-Modell auf Grid */
    gap: 1rem; /* Abstand zwischen den Grid-Elementen */
}

/* Stilisiert das Label */
label {
    font-weight: bold; /* Setzt die Schriftart auf fett */
}

/* Stilisiert den Submit-Button und den Link */
input[type="submit"], a {
    /* (Die gemeinsamen Eigenschaften für den Button und den Link sind hier aufgelistet) */
    display: inline-block;
    text-align: center;
    background-color: #007bff;
    color: white;
    text-decoration: none;
    padding: 0.5rem 1rem;
    border-radius: 5px;
    font-weight: bold;
    font-size: 18px;
    border: none;
    cursor: pointer;
}

input[type="submit"]:hover, a:hover {
    background-color: #0056b3;
}

input[type="text"], input[type="number"] {
    width: 100%;
    padding: 4px;
}




</style>
</head>
<body>
    <div class="container">
        <h1>Address Generator</h1>
        <form action="addr-gen.php" method="post">
            <label for="name_subnet_comment">use textfield <span class="red">(name,subnet,comment):</span></label>
            <textarea name="name_subnet_comment" id="name_subnet_comment" rows="8">
h-192.168.10.11,192.168.10.1/32,z-int vl10 Server 11
n-192.168.10.0/24,192.168.10.0/24,z-int vl10 subnet
hr-192.168.20.11,192.168.10.11/32,z-vpn branch Server
nr-192.168.20.0/24,192.168.20.0/24,z-vpn branch subnet
            </textarea>
            <input type="submit" value="generate">
            <label for="group_name">Groupname: zB. "hg- (HostGroup), ng- (NetworkGroup)</label>
            <input type="text" name="group_name" id="group_name" placeholder="ng-xxx">
            <label for="group_description">Group comment: (eg. zone, vlan, name)</label>
            <input type="text" name="group_description" id="group_description" placeholder="z-int vl0012 host...">
            <input type="submit" name="with_group" value="generate with group">
        </form>




<?php
/*
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_lines = $_POST['name_subnet_comment'];

    if (empty($input_lines) && !empty($_FILES['csv_file']['tmp_name'])) {
        $input_lines = file_get_contents($_FILES['csv_file']['tmp_name']);
    }
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_lines = htmlspecialchars($_POST['name_subnet_comment']);

    if (empty($input_lines) && !empty($_FILES['csv_file']['tmp_name'])) {
        $input_lines = htmlspecialchars(file_get_contents($_FILES['csv_file']['tmp_name']));
    }

   // $lines = explode("\n", $input_lines);
   $lines = array_filter(array_map('trim', explode("\n", $input_lines)));
    $output = '';
    $names = [];

    foreach ($lines as $line) {
        $parts = explode(",", $line);
        $name = trim($parts[0]);
        //$subnet = trim($parts[1]);
        //$comment = trim($parts[2]);
        $subnet = isset($parts[1]) && $parts[1] !== null ? trim($parts[1]) : '';
        $comment = isset($parts[2]) && $parts[2] !== null ? trim($parts[2]) : '';

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

/*
    if (isset($_POST['with_group'])) {
        $group_name = $_POST['group_name'];
        $group_description = $_POST['group_description'];
*/
if (isset($_POST['with_group'])) {
    $group_name = htmlspecialchars($_POST['group_name']);
    $group_description = htmlspecialchars($_POST['group_description']);
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

//echo "<textarea id='output' rows='40' readonly>" . htmlspecialchars($output) . "</textarea>";
//echo "<pre>" . htmlspecialchars($output) . "</pre>";

// Zeigt den generierten Konfigurationscode an (ohne htmlspecialchars)
    echo "<pre id='configCode'>" . htmlspecialchars($output) . "</pre>";

}
?>


<br><br>

<button onclick="copyToClipboard()">Copy to Clipboard</button>

<script>
function copyToClipboard() {
  /* Get the text content from the configCode element */
  const configCode = document.getElementById('configCode').textContent.trim();

  /* Create a temporary textarea element to copy the text to the clipboard */
  const tempTextarea = document.createElement('textarea');
  tempTextarea.value = configCode;
  document.body.appendChild(tempTextarea);

  /* Select the text and copy it to the clipboard */
  tempTextarea.select();
  document.execCommand('copy');

  /* Remove the temporary textarea */
  document.body.removeChild(tempTextarea);

  /* Provide visual feedback */
  alert('Configuration code copied to clipboard!');
}
</script>


<br><br><br>
<br>
        <a href="index.php">back to Index</a><br>

        </div>
</body>
</html>