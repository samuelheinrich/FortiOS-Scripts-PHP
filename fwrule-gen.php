<!DOCTYPE html>
<html>
<head>
<title>Firewall Rule Generator</title>

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

.red {
    color: red;
}
.blue {
    color: blue;
}
.black {
    color: black;
}

</style>
</head>



<body>
    <div class="container">
        <h1>Firewall Rule Generator</h1>
        <br>
<a href="service-gen.csv" download>download sample CSV</a>
<br>

        <form action="fwrule-gen.php" method="post">
        <label for="rules">Enter the rules <span class="black">(status, name, srcintf, dstintf, action, <span class="blue">srcaddr</span>, <span class="red">dstaddr</span>, <span class="black">service</span>, nat, comments, label):</span></label>
            <textarea name="rules" id="rules" rows="8">
enable,ia-vl0040,z-int,virtual-wan-link,accept,n-172.0.40.0/24,INTERNET,sg-internet-basic,disable,Internet Access vl0040_vrf-TRUST,some_labelz-int/wan</textarea>
            <input type="submit" value="generate">
        </form>


        <?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['rules'])) {
        $input_lines = htmlspecialchars($_POST['rules']);
    } else {
        $input_lines = '';
    }

    $lines = array_filter(array_map('trim', explode("\n", $input_lines)));
    $output = '';
    $output_srcaddr = '';
    $output_dstaddr = '';

    foreach ($lines as $line) {
        $parts = explode(",", $line);
        $status = trim($parts[0]);
        $name = trim($parts[1]);
        $srcintf = trim($parts[2]);
        $dstintf = trim($parts[3]);
        $action = trim($parts[4]);
        $srcaddr = trim($parts[5]);
        $dstaddr = trim($parts[6]);
        $service = trim($parts[7]);
        $nat = trim($parts[8]);
        $comments = trim($parts[9]);
        $label = trim($parts[10]);

        $config_code = <<<EOT
config firewall policy
    edit 0
        set status "$status"
        set name "$name"
        set srcintf "$srcintf"
        set dstintf "$dstintf"
        set action "$action"
        set srcaddr "$srcaddr"
        set dstaddr "$dstaddr"
        set service "$service"
        set nat "$nat"
        set comments "$comments"
        set label "$label"
    next
end
EOT;

        $config_srcaddr = <<<EOT
config firewall address
    edit "$srcaddr"
        set comment "$srcintf $comments"
        set subnet $srcaddr
    next
end
EOT;

        $config_dstaddr = <<<EOT
config firewall address
    edit "$dstaddr"
        set comment "$dstintf $comments"
        set subnet $dstaddr
    next
end
EOT;

        $output .= $config_code . "\n";
        $output_srcaddr .= $config_srcaddr . "\n";
        $output_dstaddr .= $config_dstaddr . "\n";
    }

    echo "<pre id='configCode'>" . htmlspecialchars($output) . "</pre>";
    echo "<pre id='configSrcAddr' style='color:blue;'>" . htmlspecialchars($output_srcaddr) . "</pre>";
    echo "<pre id='configDstAddr' style='color:red;'>" . htmlspecialchars($output_dstaddr) . "</pre>";
}
?>
<br>Achtung: Firewall Address Objekte nur kopieren, wenn noch nicht vorhanden auf Forti!<br><br>

<button onclick="copyToClipboard()">Copy to Clipboard</button>

<script>
function copyToClipboard() {
  const configCode = document.getElementById('configCode').textContent.trim();
  const configSrcAddr = document.getElementById('configSrcAddr').textContent.trim();
  const configDstAddr = document.getElementById('configDstAddr').textContent.trim();

  const tempTextarea = document.createElement('textarea');
  tempTextarea.value = configCode + '\n' + configSrcAddr + '\n' + configDstAddr;
  document.body.appendChild(tempTextarea);

  tempTextarea.select();
  document.execCommand('copy');

  document.body.removeChild(tempTextarea);

  alert('Configuration code copied to clipboard!');
}
</script>
<br><br><br>
<br>
        <a href="index.php">back to Index</a><br>

    </div>
</body>
</html>