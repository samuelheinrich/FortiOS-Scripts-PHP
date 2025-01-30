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

        /* Styling bleibt gleich, mit einer Ergänzung für das deaktivierte Eingabefeld */
        input[disabled] {
            background-color: #e9ecef;
            cursor: not-allowed;
        }
    </style>
    <script>
        function togglePublicPortInput(checkbox) {
            const publicPortInput = document.getElementById('public_port');
            const privatePortInput = document.getElementById('private_port');

            if (checkbox.checked) {
                // "ALL" aktiviert: Beide Felder deaktivieren
                publicPortInput.disabled = true;
                privatePortInput.disabled = true;
                publicPortInput.value = "1-65535";
                privatePortInput.value = "1-65535";
            } else {
                publicPortInput.disabled = false;
                privatePortInput.disabled = false;
                publicPortInput.value = "";
                privatePortInput.value = "";
            }
        }

        function checkPublicPorts() {
            const publicPortInput = document.getElementById('public_port');
            const privatePortInput = document.getElementById('private_port');
            const publicPortValue = publicPortInput.value;

            if (publicPortValue.includes(",") || publicPortValue.includes("-")) {
                // Mehrere Ports erkannt: Private Port deaktivieren und synchronisieren
                privatePortInput.disabled = true;
                privatePortInput.value = publicPortValue;
            } else {
                // Einzelner Port: Private Port aktivieren
                privatePortInput.disabled = false;
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>VIP Object Generator</h1>
        <form action="" method="post" class="form-container">
            <div>
                <label for="wan_interface">WAN Interface:</label>
                <input type="text" name="wan_interface" id="wan_interface" placeholder="vlxxxx" value="<?php echo htmlspecialchars($_POST['wan_interface'] ?? ''); ?>" required>
            </div>
            <div>
                <label for="destination_zone">Destination Zone:</label>
                <input type="text" name="destination_zone" id="destination_zone" placeholder="dmz" value="<?php echo htmlspecialchars($_POST['destination_zone'] ?? ''); ?>" required>
            </div>
            <div>
                <label for="public_ip">Public IP:</label>
                <input type="text" name="public_ip" id="public_ip" placeholder="1.1.1.1" pattern="\d{1,3}(\.\d{1,3}){3}" value="<?php echo htmlspecialchars($_POST['public_ip'] ?? ''); ?>" required>
            </div>
            <div>
                <label for="public_port">Public Port(s):</label>
                <input type="text" name="public_port" id="public_port" placeholder="443,500,344-388" pattern="(\d+(-\d+)?)(,(\d+(-\d+)?))*" value="<?php echo htmlspecialchars($_POST['public_port'] ?? ''); ?>" oninput="checkPublicPorts()">
                <label>
                    <input type="checkbox" id="all_ports" name="all_ports" <?php echo isset($_POST['all_ports']) ? 'checked' : ''; ?> onclick="togglePublicPortInput(this)"> All Ports
                </label>
            </div>
            <div>
                <label for="private_ip">Private IP:</label>
                <input type="text" name="private_ip" id="private_ip" placeholder="2.2.2.2" pattern="\d{1,3}(\.\d{1,3}){3}" value="<?php echo htmlspecialchars($_POST['private_ip'] ?? ''); ?>" required>
            </div>
            <div>
                <label for="private_port">Private Port:</label>
                <input type="text" name="private_port" id="private_port" placeholder="443" value="<?php echo htmlspecialchars($_POST['private_port'] ?? ''); ?>" required>
            </div>
            <div class="full-width">
                <label for="beschreibung">Comment:</label>
                <input type="text" name="beschreibung" id="beschreibung" placeholder="this is a comment" value="<?php echo htmlspecialchars($_POST['beschreibung'] ?? ''); ?>">
            </div>
            <div class="full-width">
                <label for="color">Object Color:</label>
                <select name="color" id="color" required>
                    <option value="1" <?php echo (isset($_POST['color']) && $_POST['color'] == '1') ? 'selected' : ''; ?>>black</option>
                    <option value="2" <?php echo (isset($_POST['color']) && $_POST['color'] == '2') ? 'selected' : ''; ?>>blue</option>
                    <option value="3" <?php echo (isset($_POST['color']) && $_POST['color'] == '3') ? 'selected' : ''; ?>>green</option>
                    <option value="4" <?php echo (isset($_POST['color']) && $_POST['color'] == '4') ? 'selected' : ''; ?>>dark red</option>
                    <option value="5" <?php echo (isset($_POST['color']) && $_POST['color'] == '5') ? 'selected' : ''; ?>>pink</option>
                    <option value="6" <?php echo (isset($_POST['color']) && $_POST['color'] == '6') ? 'selected' : ''; ?>>red</option>
                </select>
            </div>
            <div class="full-width">
                <input type="submit" value="Generate">
            </div>
        </form>

        <?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $wan_interface = htmlspecialchars($_POST['wan_interface'] ?? '');
    $public_ip = htmlspecialchars($_POST['public_ip'] ?? '');
    $public_port = htmlspecialchars($_POST['public_port'] ?? '');
    $private_ip = htmlspecialchars($_POST['private_ip'] ?? '');
    $private_port = htmlspecialchars($_POST['private_port'] ?? '');
    $beschreibung = htmlspecialchars($_POST['beschreibung'] ?? 'No Description');
    $destination_zone = htmlspecialchars($_POST['destination_zone'] ?? '');
    $color = htmlspecialchars($_POST['color'] ?? '');
    $all_ports = isset($_POST['all_ports']) ? true : false;

    if ($all_ports) {
        $tcp_portrange = "1-65535";
        $port_label = "ALL";
    } else {
        // Ersetzen von Kommas durch Leerzeichen
        $tcp_portrange = str_replace(',', ' ', $public_port);

        // Prüfen, ob mehrere Ports vorliegen
        if (strpos($public_port, ',') !== false || strpos($public_port, '-') !== false) {
            $port_label = "many"; // Name des Serviceobjekts endet mit "many"
        } else {
            $port_label = $public_port; // Einzelner Port wird verwendet
        }
    }

    $service_config = "
config firewall service custom
    edit \"sg-vip-$public_ip:$port_label\"
        set comment \"DNAT $public_ip:$public_port to $private_ip:$private_port $beschreibung\"
        set tcp-portrange $tcp_portrange
    next
end";

    $vip_config = "
config firewall vip
    edit \"vip-$public_ip:$port_label\"
        set comment \"DNAT $public_ip:$public_port to $private_ip:$private_port $beschreibung\"
        set service \"sg-vip-$public_ip:$port_label\"
        set extip \"$public_ip\"
        set mappedip \"$private_ip\"
        set extintf \"$wan_interface\"
        set color \"$color\"
    next
end";

    $policy_config = "
config firewall policy
    edit 0
        set name \"vip-$public_ip:$port_label\"
        set srcintf \"virtual-wan-link\"
        set dstintf \"$destination_zone\"
        set action accept
        set srcaddr \"all\"
        set dstaddr \"vip-$public_ip:$port_label\"
        set schedule \"always\"
        set service \"sg-vip-$public_ip:$port_label\"
        set utm-status enable
        set logtraffic all
        set comments \"DNAT $public_ip:$public_port to $private_ip:$private_port $beschreibung\"
    next
end";

    echo "<pre id='configCode'>" . htmlspecialchars($service_config . $vip_config . $policy_config) . "</pre>";
    echo "<button onclick='copyToClipboard()'>Copy to Clipboard</button>";
}
?>

        <br><br>
        <a href="index.php">Back to Index</a>
    </div>
    <script>
        function copyToClipboard() {
            const configCode = document.getElementById('configCode')?.textContent.trim();
            if (!configCode) {
                alert('No configuration code to copy!');
                return;
            }

            const tempTextarea = document.createElement('textarea');
            tempTextarea.value = configCode;
            document.body.appendChild(tempTextarea);
            tempTextarea.select();
            document.execCommand('copy');
            document.body.removeChild(tempTextarea);

            alert('Configuration code copied to clipboard!');
        }
    </script>
</body>
</html>
