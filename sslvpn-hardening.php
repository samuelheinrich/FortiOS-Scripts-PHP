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
    <h1>SSLVPN Hardening with loopback</h1>

</head>
<body>

<form action="sslvpn-hardening.php" method="post" class="form-container">
  <div>
    <label for="wan_interface">WAN Interface:</label>
    <input type="text" name="wan_interface" id="wan_interface" placeholder="vlxxxx" value="<?php echo isset($_POST['wan_interface']) ? $_POST['wan_interface'] : ''; ?>">
  </div>
  <div>
    <label for="loopback_name">Loopback Name:</label>
    <input type="text" name="loopback_name" id="loopback_name" placeholder="lo99" value="<?php echo isset($_POST['loopback_name']) ? $_POST['loopback_name'] : ''; ?>">
  </div>
  <div>
    <label for="public_ip">External SSLVPN IP:</label>
    <input type="text" name="public_ip" id="public_ip" placeholder="x.x.x.x" value="<?php echo isset($_POST['public_ip']) ? $_POST['public_ip'] : ''; ?>">
  </div>
  <div>
    <label for="public_port">External SSLVPN Port:</label>
    <input type="number" name="public_port" id="public_port" min="1" max="65535" placeholder="min=1 max=65535" value="<?php echo isset($_POST['public_port']) ? $_POST['public_port'] : ''; ?>">
  </div>
  <div>
    <label for="loopback_ip">Loopback IP:</label>
    <input type="text" name="loopback_ip" id="loopback_ip"  placeholder="y.y.y.y" value="<?php echo isset($_POST['loopback_ip']) ? $_POST['loopback_ip'] : ''; ?>">
  </div>
  <div>
    <label for="private_port">Internal SSLVPN Port:</label>
    <input type="number" name="private_port" id="private_port" min="1" max="65535" placeholder="min=1 max=65535" value="<?php echo isset($_POST['private_port']) ? $_POST['private_port'] : ''; ?>">
  </div>
  <div class="full-width">
    <label for="beschreibung">Comment:</label>
    <input type="text" name="beschreibung" id="beschreibung" placeholder="SSLVPNd on Loobpack" value="<?php echo isset($_POST['beschreibung']) ? $_POST['beschreibung'] : ''; ?>">
  </div>
  <div class="full-width">
    <label for="color">Object Color:</label>
    <select name="color" id="color">
      <option value="1">black</option>
      <option value="2">blue</option>
      <option value="3">green</option>
      <option value="4">dark red</option>
      <option value="5">pink</option>
      <option value="6">red</option>
    </select>
  </div>
  <div class="full-width">
    <input type="submit" value="Generate">
  </div>
</form>



<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $wan_interface = $_POST['wan_interface'];
    $public_ip = $_POST['public_ip'];
    $public_port = $_POST['public_port'];
    $loopback_ip = $_POST['loopback_ip'];
    $private_port = $_POST['private_port'];
    $beschreibung = $_POST['beschreibung'];
    $loopback_name = $_POST['loopback_name'];
    $color = $_POST['color'];

    // Erzeugt den Anfang des Konfigurationscodes
    $config_code = "
config firewall service custom
    edit \"sg-vip-$public_ip:$public_port\"
        set comment \"DNAT $public_ip:$public_port to $loopback_ip:$private_port $beschreibung\"
        set tcp-portrange $public_port
    next
end

config firewall address
    edit \"geo_Russian_Federation\"
        set type geography
        set country \"RU\"
    next
end

config firewall vip
    edit \"vip-$public_ip:$public_port\"
        set comment \"DNAT $public_ip:$public_port to $loopback_ip:$private_port $beschreibung\"
        set service \"sg-vip-$public_ip:$public_port\"
        set extip \"$public_ip\"
        set mappedip \"$loopback_ip\"
        set extintf \"$wan_interface\"
        set color \"$color\"";

    // Fügt Portforwarding hinzu, falls private_port und public_port nicht übereinstimmen
    if ($private_port != $public_port) {
        $config_code .= "
        set portforward enable
        set mappedport $private_port";
    }

    // Fügt den restlichen Teil des Konfigurationscodes hinzu
    $config_code .= "
    next
end

config firewall policy
    edit 0
        set name \"vip-sslvpnd block malicous\"
        set srcintf \"virtual-wan-link\"
        set dstintf \"$loopback_name\"
        set dstaddr \"vip-$public_ip:$public_port\"
        set internet-service-src enable
        set internet-service-src-name \"Botnet-C&C.Server\", \"Malicious-Malicious.Server\", \"Phishing-Phishing.Server\", \"Spam-Spamming.Server\", \"Tor-Exit.Node\", \"Tor-Relay.Node\", \"VPN-Anonymous.VPN\"
        set schedule \"always\"
        set service \"ALL\"
        set logtraffic all
        set match-vip enable
        set comments \"block malicous traffic to sslvpnd\"
    next
end

config firewall policy
    edit 0
        set name \"vip-sslvpnd block country\"
        set srcintf \"virtual-wan-link\"
        set dstintf \"$loopback_name\"
        set srcaddr \"geo_Russian_Federation\"
        set dstaddr \"vip-$public_ip:$public_port\"
        set schedule \"always\"
        set service \"ALL\"
        set logtraffic all
        set match-vip enable
        set comments \"block countrys traffic to sslvpn\"
    next
end

config firewall policy
    edit 0
        set name \"vip-$public_ip:$public_port\"
        set srcintf \"virtual-wan-link\"
        set dstintf \"$loopback_name\"
        set action accept
        set srcaddr \"INTERNET\"
        set dstaddr \"vip-$public_ip:$public_port\"
        set schedule \"always\"
        set service \"sg-vip-$public_ip:$public_port\"
        set utm-status enable
        set logtraffic all
        set comments \"DNAT $public_ip:$public_port to $loopback_ip:$private_port $beschreibung\"
    next
end

config vpn ssl settings
    set login-attempt-limit 5
    set login-block-time 3600
    set source-interface $loopback_name
    end
end";

    // Zeigt den generierten Konfigurationscode an (ohne htmlspecialchars)
    echo "<pre id='configCode'>" . htmlspecialchars($config_code) . "</pre>";
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




<br><br><br> <br>
    <a href="index.php">back to index</a>
</div>
</body>
</html>
