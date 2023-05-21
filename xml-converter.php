<!DOCTYPE html>
<html>
<head>
	<title>PFSense XML Inteface Converter</title>
	<style>
		#xml-input, #output, #forti-cfg {
			width: 70%;
			height: 18em;
		}
	</style>
<script>
	// Kopieren des Texts in der Ausgabe in die Zwischenablage
	function copyToClipboard() {
		var output = document.getElementById("output");
		output.select();
		document.execCommand("copy");
		alert("Copied to clipboard!");
	}
	
	// Generieren des FortiGate-Konfigurationscodes
    function generateFortiCFG() {
		var output = document.getElementById("output").value;
		
		// Aufteilen der Ausgabe in Zeilen
		var lines = output.split("\n");
		
		// Initialisierung der FortiGate-Konfigurationszeichenkette
		var cfg = "config system interface\n";
		
		// Iteration über jede Zeile der Ausgabe
		for (var i = 0; i < lines.length; i++) {
			// Aufteilen der Zeile in Interface, IP-Adresse und Beschreibung
			var fields = lines[i].split(",");
			var ifname = fields[0];
			var ip = fields[1];
			var desc = fields[2];
			
			// Extrahieren der VLAN-ID aus dem Interface-Namen
			var vlanid = ("0000" + ifname.split(".")[1]).slice(-4);
			
			// Generieren der FortiGate-Konfigurationszeilen für die aktuelle Zeile
			cfg += "    edit \"vl" + vlanid + "\"\n";
			cfg += "        set vdom \"root\"\n";
			cfg += "        set ip " + ip + "\n";
			cfg += "        set allowaccess ping\n";
			cfg += "        set description \"vl" + vlanid + " " + desc.replace(/\+/g, " ") + "\"\n";
			cfg += "        set alias \"" + desc.replace(/\+/g, " ") + "\"\n";
			cfg += "        set role lan\n";
			cfg += "        set interface \"lacp-trunk1\"\n";
			cfg += "        set vlanid " + vlanid + "\n";
			cfg += "    next\n";
		}
		
		// Hinzufügen der Schlusszeile zur FortiGate-Konfiguration
        cfg += "end\n";
		
		// Aktualisieren der FortiGate-Konfigurations-Textarea
		document.getElementById("forti-cfg").value = cfg;

	}
</script>

</head>
<body>
<h1>PFSense XML Interface converter</h1>
<br>
<h2>Beispiel Input..</h2>
<a href="pfsense-interface-example.xml">pfsense-interface-example.xml</a><br>
<br>
	<form method="post">
		<label for="xml-input">XML Input:</label>
		<br>
		<textarea name="xml-input" id="xml-input"><?php if (isset($_POST['xml-input'])) echo $_POST['xml-input']; ?></textarea>
		<br>
		<button type="submit" name="convert-btn">Convert</button>
	</form>
	<br>

<?php
if (isset($_POST['convert-btn'])) {
    // Formular wurde abgeschickt, Daten verarbeiten
    $xml_input = $_POST['xml-input'];
    // XML-Dokument in eine SimpleXMLElement-Instanz umwandeln
    $xml = simplexml_load_string($xml_input);
    $output = '';
    // Über alle XML-Knoten iterieren
    foreach ($xml->children() as $i => $interface) {
        // Schnittstellendaten extrahieren
        $if = (string) $interface->if;
        $ipaddr = (string) $interface->ipaddr;
        $subnet = (string) $interface->subnet;
        $descr = (string) $interface->descr;
        // VLAN-ID generieren
        $vlanid = str_pad(substr($if, strpos($if, '.')+1), 4, "0", STR_PAD_LEFT);
        // Zeile für die Ausgabe generieren
        $line = $if . ',' . $ipaddr . '/' . $subnet . ',' . $descr;
        // Prüfen, ob ein Zeilenumbruch hinzugefügt werden muss
        if ($i > 0) {
            $output .= PHP_EOL;
        }
        // Zeile zur Ausgabe hinzufügen
        $output .= $line;
    }
    // Ergebnis-Code ausgeben
    echo '<br>';
    echo '<textarea id="output">' . $output . '</textarea>';
    echo '<br>';
    echo '<button onclick="copyToClipboard()">Copy to Clipboard</button>';
    echo ' ';
    echo '<input type="button" value="Generate Forti CFG" onclick="generateFortiCFG()">';
    echo '<br><br>';
    echo '<textarea id="forti-cfg" readonly style="width: 80%; height: 16em;"></textarea>';
} else {
    // Formular anzeigen
    echo '<form method="post">';
    echo '<label for="xml-input">XML Input:</label>';
    echo '<br>';
    echo '<textarea name="xml-input" id="xml-input" style="width: 80%; height: 16em;"></textarea>';
    echo '<br>';
    echo '<button type="submit" name="convert-btn">Convert</button>';
    echo '</form>';
}
?>

<script>
	function copyToClipboard() {
		var output = document.getElementById("output");
		output.select();
		document.execCommand("copy");
		alert("Copied to clipboard!");
	}

	function generateFortiCFG() {
		var output = document.getElementById("output").value;
		var lines = output.split("\n");
		var cfg = "config system interface\n";
		for (var i = 0; i < lines.length; i++) {
			var fields = lines[i].split(",");
			var ifname = fields[0];
			var ip = fields[1];
			var desc = fields[2];
			var vlanid = ("0000" + ifname.split(".")[1]).slice(-4);
			cfg += "    edit \"vl" + vlanid + "\"\n";
			cfg += "        set vdom \"root\"\n";
			cfg += "        set ip " + ip + "\n";
			cfg += "        set allowaccess ping\n";
			cfg += "        set description \"vl" + vlanid + " " + desc.replace(/\+/g, " ") + "\"\n";
			cfg += "        set alias \"" + desc.replace(/\+/g, " ") + "\"\n";
			cfg += "        set role lan\n";
			cfg += "        set interface \"lacp-trunk1\"\n";
			cfg += "        set vlanid " + vlanid + "\n";
			cfg += "    next\n";
		}
		cfg += "end\n";
		document.getElementById("forti-cfg").value = cfg;
	}
</script>
<a href="index.php">Zurück zum Index</a><br>

