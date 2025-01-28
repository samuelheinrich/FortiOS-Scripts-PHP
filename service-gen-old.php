<!DOCTYPE html>
<html>
<head>
<title>Service object Generator</title>
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
<h1>Service object Generator</h1>
<br>
<a href="service-gen.csv" download>download sample CSV</a>

<form action="service-gen.php" method="post" enctype="multipart/form-data"><br>
    <label for="csv_file">upload CSV:</label>
    <input type="file" name="csv_file" id="csv_file" accept=".csv" onchange="clearTextarea()">
    <label for="name_tcp_udp_comment">oder Textfeld verwenden <span style="color:red;">(name,tcp,udp,comment):</span></label>
    <textarea name="name_tcp_udp_comment" id="name_tcp_udp_comment" rows="6">s-tcp/8953,8953,,sample TCP Object
s-udp/8953,,8953,sample UDP object
s-udp/1000-2000,,1000-2000,sample UDP Object Range 1000-2000</textarea>
    <input type="submit" value="generate">
    <label for="group_name">Groupname: eg. "sg-(service group name)</label>
    <input type="text" name="group_name" id="group_name" placeholder="sg-xxx">
    <label for="group_description">Group Comment: </label>
    <input type="text" name="group_description" id="group_description" placeholder="servicegroupe....">
    <input type="submit" name="with_group" value="generate with group">
</form>


<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_lines = htmlspecialchars($_POST['name_tcp_udp_comment']);
    if (empty($input_lines) && !empty($_FILES['csv_file']['tmp_name'])) {
        $input_lines = file_get_contents($_FILES['csv_file']['tmp_name']);
    }


    //$lines = explode("\n", $input_lines);
    $lines = array_filter(array_map('trim', explode("\n", $input_lines)));
    $output = '';
    $names = [];

    foreach ($lines as $line) {
        $parts = explode(",", $line);
        $name = trim($parts[0]);
        $tcp = trim($parts[1]);
        $udp = trim($parts[2]);
        $comment = trim(implode(",", array_slice($parts, 3)));
        $names[] = $name;

        $config_code = "config firewall service custom\n";
        $config_code .= "    edit \"$name\"\n";
        $config_code .= "        set comment \"$comment\"\n";
        if (!empty($tcp)) {
            $config_code .= "        set tcp-portrange $tcp\n";
        }
        if (!empty($udp)) {
            $config_code .= "        set udp-portrange $udp\n";
        }
        $config_code .= "    next\n";
        $config_code .= "end\n\n";
        $output .= $config_code;
    }


    if (isset($_POST['with_group'])) {
        $group_name = htmlspecialchars($_POST['group_name']);
        $group_description = htmlspecialchars($_POST['group_description']);
        $member_list = implode('" "', $names);

        $config_group = "config firewall service group\n";
        $config_group .= "    edit \"$group_name\"\n";
        $config_group .= "        set member \"$member_list\"\n";
        $config_group .= "        set comment \"$group_description\"\n";
        $config_group .= "    next\n";
        $config_group .= "end\n\n";
        $output .= $config_group;
    }


//    echo "<pre>" . htmlspecialchars($output) . "</pre>";

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



<br><br><br><br>
        <a href="index.php">back to Index</a><br>

        </div>
</body>
</html>