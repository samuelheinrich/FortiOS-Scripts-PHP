<!DOCTYPE html>
<html>
<head>
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
function copyToClipboard(text) {
  var temp = document.createElement("textarea");
  temp.value = text;
  document.body.appendChild(temp);
  temp.select();
  document.execCommand("copy");
  document.body.removeChild(temp);
  alert("Copied to clipboard");
}
</script>

<div class="container">
    <h1>VIP object Generator</h1>

</head>
<body>

<form action="vip-gen.php" method="post" class="form-container">
  <div>
    <label for="wan_interface">WAN Interface:</label>
    <input type="text" name="wan_interface" id="wan_interface" placeholder="vlxxxx" value="<?php echo isset($_POST['wan_interface']) ? $_POST['wan_interface'] : ''; ?>">
  </div>
  <div>
    <label for="destination_zone">Destination Zone:</label>
    <input type="text" name="destination_zone" id="destination_zone" placeholder="z-xxx" value="<?php echo isset($_POST['destination_zone']) ? $_POST['destination_zone'] : ''; ?>">
  </div>
  <div>
    <label for="public_ip">Public IP:</label>
    <input type="text" name="public_ip" id="public_ip" placeholder="x.x.x.x" value="<?php echo isset($_POST['public_ip']) ? $_POST['public_ip'] : ''; ?>">
  </div>
  <div>
    <label for="public_port">Public Port:</label>
    <input type="number" name="public_port" id="public_port" min="1" max="65535" placeholder="min=1 max=65535" value="<?php echo isset($_POST['public_port']) ? $_POST['public_port'] : ''; ?>">
  </div>
  <div>
    <label for="private_ip">Private IP:</label>
    <input type="text" name="private_ip" id="private_ip"  placeholder="y.y.y.y" value="<?php echo isset($_POST['private_ip']) ? $_POST['private_ip'] : ''; ?>">
  </div>
  <div>
    <label for="private_port">Private Port:</label>
    <input type="number" name="private_port" id="private_port" min="1" max="65535" placeholder="min=1 max=65535" value="<?php echo isset($_POST['private_port']) ? $_POST['private_port'] : ''; ?>">
  </div>
  <div class="full-width">
    <label for="beschreibung">comment:</label>
    <input type="text" name="beschreibung" id="beschreibung" placeholder="xxx.domain.com / server XYZ" value="<?php echo isset($_POST['beschreibung']) ? $_POST['beschreibung'] : ''; ?>">
  </div>
  <div class="full-width">
    <input type="submit" value="generate">
  </div>
</form>


<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $wan_interface = $_POST['wan_interface'];
    $public_ip = $_POST['public_ip'];
    $public_port = $_POST['public_port'];
    $private_ip = $_POST['private_ip'];
    $private_port = $_POST['private_port'];
    $beschreibung = $_POST['beschreibung'];
    $destination_zone = $_POST['destination_zone'];
    $config_code = <<<EOT
config firewall service custom
    edit "sg-vip-$public_ip:$public_port"
        set comment "DNAT $public_ip:$public_port to $private_ip:$private_port $beschreibung"
        set tcp-portrange $public_port
    next
end

config firewall vip
    edit "vip-$public_ip:$public_port"
        set comment "DNAT $public_ip:$public_port to $private_ip:$private_port $beschreibung"
        set service "sg-vip-$public_ip:$public_port"
        set extip $public_ip
        set mappedip "$private_ip"
        set extintf "$wan_interface"
        set color 6
    next
end

config firewall policy
    edit 0
        set name "vip-$public_ip:$public_port"
        set srcintf "virtual-wan-link"
        set dstintf "$destination_zone"
        set action accept
        set srcaddr "INTERNET"
        set dstaddr "vip-$public_ip:$public_port"
        set schedule "always"
        set service "sg-vip-$public_ip:$public_port"
        set utm-status enable
        set logtraffic all
        set comments "DNAT $public_ip:$public_port to $private_ip:$private_port $beschreibung"
    next
end

EOT;

    echo "<pre>" . htmlspecialchars($config_code) . "</pre>";
}
?>




<br><br><br> <br>
    <a href="index.php">back to index</a>
</div>
</body>
</html>
