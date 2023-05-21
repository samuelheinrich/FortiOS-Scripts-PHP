<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Route Generator</title>
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
<script>

function validateForm() {
    const subnetInput = document.getElementById('subnet');
    const subnet = subnetInput.value;
    const subnetRegex = /^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\/\d{1,2})|(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\s+\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})$/;

    if (!subnetRegex.test(subnet)) {
        alert('Bitte geben Sie ein gültiges Subnet ein.');
        return false;
    }

    return true;
}
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
<div class="container">
<h1>Route Generator</h1>
<br>
<a href="route-gen.csv" download>download sample CSV</a>
<br>


<form action="route-gen.php" method="post"><br>
    <label for="subnet_gateway_device_comment">Input: Subnet, Gateway, device, comment):</label>
    <textarea name="subnet_gateway_device_comment" id="subnet_gateway_device_comment" rows="4" style="width: 100%;">10.10.10.0/24,1.1.1.1,vl0227,xxx vl10 route to xxxx
10.10.20.0/24,1.1.1.1,vl0227,xxx vl20 route to xxxx</textarea><br>
    <input type="submit" value="generate">
</form>


<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_lines = $_POST['subnet_gateway_device_comment'];
    //$lines = explode("\n", $input_lines);
    $lines = array_filter(array_map('trim', explode("\n", $input_lines)));
    $output = '';

    foreach ($lines as $line) {
        $parts = explode(",", $line);
        $subnet = trim($parts[0]);
        $gateway = trim($parts[1]);
        $device = trim($parts[2]);
        $comment = trim($parts[3]);

        $config_code = "config router static\n";
        $config_code .= "    edit 0\n";
        $config_code .= "        set dst $subnet\n";
        $config_code .= "        set gateway $gateway\n";
        $config_code .= "        set device $device\n";
        $config_code .= "        set comment \"$comment\"\n";
        $config_code .= "    next\n";
        $config_code .= "end\n\n";

        $output .= $config_code;
    }

    echo "<pre>" . htmlspecialchars($output) . "</pre>";
}
?>


<br>
<br>
        <a href="index.php">back to Index</a><br>

        </div>
</body>
</html>