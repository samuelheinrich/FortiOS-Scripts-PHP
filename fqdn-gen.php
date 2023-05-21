<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>FQDN-Objekt-Generator</title>
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
    <h1>FQDN-Object-Generator</h1>
    <form action="fqdn-gen.php" method="post">
        <label for="fqdn_addresses">FQDN-Address (one url per line):</label>
        <textarea name="fqdn_addresses" id="fqdn_addresses" rows="16" placeholder="example.com"><?php echo isset($_POST['fqdn_addresses']) ? htmlspecialchars($_POST['fqdn_addresses']) : ''; ?></textarea>

        <input type="submit" value="generate">
        <label for="group_name">Group name (optional):</label>
        <input type="text" name="group_name" id="group_name" placeholder="z.B. fqdn-group" value="<?php echo isset($_POST['group_name']) ? htmlspecialchars($_POST['group_name']) : ''; ?>">

        <input type="submit" name="with_group" value="generate with group"><br>
    </form>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input_lines = $_POST['fqdn_addresses'];
        $group_name = trim($_POST['group_name']);
        $with_group = isset($_POST['with_group']);

        $lines = array_filter(array_map('trim', explode("\n", $input_lines)));
        $output = '';
        $fqdn_names = [];

        foreach ($lines as $line) {
            $fqdn_address = trim($line);
            $fqdn_name = 'fqdn-' . preg_replace('/[^a-z0-9.]+/i', '-', $fqdn_address);
            $fqdn_names[] = $fqdn_name;

            $config_code = "config firewall address\n";
            $config_code .= "    edit \"$fqdn_name\"\n";
            $config_code .= "        set type fqdn\n";
            $config_code .= "        set fqdn \"$fqdn_address\"\n";
            $config_code .= "    next\n";
            $config_code .= "end\n\n";

            $output .= $config_code;
        }

        if ($with_group && !empty($group_name)) {
            $group_config_code = "config firewall addrgrp\n";
            $group_config_code .= "    edit \"$group_name\"\n";
            // Schachteln Sie die Werte in Anführungszeichen ein
            $group_config_code .= "        set member \"" . implode("\" \"", $fqdn_names) . "\"\n";
            $group_config_code .= "    next\n";
            $group_config_code .= "end\n\n";
        
            $output .= $group_config_code;
        }
        

        echo "<pre>$output</pre>";
    }
    ?>

<br><br><br>
        <a href="index.php">back to Index</a><br>

        </div>

</body>

</html>
