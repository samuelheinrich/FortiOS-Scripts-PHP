<!DOCTYPE html>
<html>
<head>
<title>Interface Generator</title>
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
    </script>

</head>
<body>
    <div class="container">
        <h1>Interface Generator</h1>
        <br>
        <a href="interface-gen.csv" download>Download sample CSV</a><br>
        <form action="interface-gen.php" method="post" enctype="multipart/form-data"><br>
            <label for="csv_file">Generate using CSV:</label>
            <input type="file" name="csv_file" id="csv_file" accept=".csv"><br>
            <label for="interface_data">or use textfield <span style="color:red;"><br>
            (vlanname,vdom,ip,allowaccess,description,alias,role,interface,vlanid,status):</span></label>
            <textarea name="interface_data" id="interface_data" rows="4">v10003,root,192.168.1.1/24,ping,internal web interface,int-web,lan,lacp-trunk1,disabled</textarea>
            <input type="submit" value="generate">
        </form>

        <!-- PHP-Teil und Rest des HTML-Codes bleiben unverändert -->
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $interface_data = $_POST["interface_data"];

            if (empty($interface_data) && !empty($_FILES['csv_file']['tmp_name'])) {
                $interface_data = file_get_contents($_FILES['csv_file']['tmp_name']);
            }

            //$lines = explode("\n", $interface_data);
            $lines = array_filter(array_map('trim', explode("\n", $interface_data)));
            $output = "";

            foreach ($lines as $line) {
                $exploded_line = explode(",", $line);
                $padded_line = array_pad($exploded_line, 10, null);
                list($vlanname, $vdom, $ip, $allowaccess, $description, $alias, $role, $interface, $vlanid, $status) = $padded_line;
            
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

/*
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
*/
        ?>

<?php if (!empty($output)): ?>
    <label for="output">output:</label><br>
    <pre id="output"><?php echo htmlspecialchars($output); ?></pre><br>

<?php endif; ?>


        <br><br><br>
        <a href="index.php">back to Index</a><br>
    </div>
</body>
</html>
