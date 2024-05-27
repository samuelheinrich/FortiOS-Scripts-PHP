<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flow Trace Generator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-size: 1em; /* Normale Schriftgröße */
        }
        .container {
            background-color: white;
            padding: 4rem; /* Doppelte Polsterung */
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 80%; /* Breitere Tabelle */
            margin-top: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 20px; /* Doppelte Lücke */
        }
        label {
            display: flex;
            align-items: center;
            margin-bottom: 10px; /* Doppelte Margin */
            width: 100%;
        }
        input[type="text"], input[type="number"], select {
            padding: 10px; /* Doppelte Polsterung */
            margin-left: 20px; /* Abstand zwischen Label und Input */
            width: 70%; /* Breitere Eingabefelder */
        }
        .example {
            font-size: 0.8em;
            color: gray;
            margin-left: 10px;
            visibility: hidden;
        }
        label:hover .example {
            visibility: visible;
        }
        #output {
            margin-top: 40px; /* Doppelte Margin */
            white-space: pre-wrap;
            background-color: #f4f4f4;
            padding: 20px; /* Doppelte Polsterung */
            border-radius: 5px;
        }
        input[type="submit"], a {
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
        h1 {
            text-align: center;
            margin-bottom: 40px; /* Abstand unter der Überschrift */
        }
    </style>
</head>
<body>
    <main>
        <div class="container">
            <h1>Flow Trace Generator</h1>
            <form method="post">
                <label for="proto">Protocol number:
                    <select id="proto" name="proto">
                        <option value="" <?php echo !isset($_POST['proto']) || $_POST['proto'] == '' ? 'selected' : ''; ?>>keine Angabe</option>
                        <option value="1" <?php echo (isset($_POST['proto']) && $_POST['proto'] == '1') ? 'selected' : ''; ?>>1 - ICMP</option>
                        <option value="2" <?php echo (isset($_POST['proto']) && $_POST['proto'] == '2') ? 'selected' : ''; ?>>2 - IGMP</option>
                        <option value="6" <?php echo (isset($_POST['proto']) && $_POST['proto'] == '6') ? 'selected' : ''; ?>>6 - TCP</option>
                        <option value="17" <?php echo (isset($_POST['proto']) && $_POST['proto'] == '17') ? 'selected' : ''; ?>>17 - UDP</option>
                    </select>
                </label>
                
                <label for="addr">IP address:
                    <input type="text" id="addr" name="addr" value="<?php echo isset($_POST['addr']) ? $_POST['addr'] : ''; ?>">
                    <span class="example">Beispiel: 1.1.1.255</span>
                </label>

                <label for="saddr">Src IP address:
                    <input type="text" id="saddr" name="saddr" value="<?php echo isset($_POST['saddr']) ? $_POST['saddr'] : ''; ?>">
                    <span class="example">Beispiel: 1.1.1.1</span>
                </label>

                <label for="daddr">Dst IP address:
                    <input type="text" id="daddr" name="daddr" value="<?php echo isset($_POST['daddr']) ? $_POST['daddr'] : ''; ?>">
                    <span class="example">Beispiel: 2.2.2.2</span>
                </label>

                <label for="port">Port:
                    <input type="number" id="port" name="port" value="<?php echo isset($_POST['port']) ? $_POST['port'] : ''; ?>">
                    <span class="example">Beispiel: 443</span>
                </label>

                <label for="sport">Src port:
                    <input type="number" id="sport" name="sport" value="<?php echo isset($_POST['sport']) ? $_POST['sport'] : ''; ?>">
                    <span class="example">Beispiel: 4000</span>
                </label>

                <label for="dport">Dst port:
                    <input type="number" id="dport" name="dport" value="<?php echo isset($_POST['dport']) ? $_POST['dport'] : ''; ?>">
                    <span class="example">Beispiel: 443</span>
                </label>

                <button type="submit">Generate Output</button>
            </form>
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $output = '';

                if (!empty($_POST['proto'])) {
                    $proto = htmlspecialchars($_POST['proto']);
                    $output .= "diagnose debug flow filter proto $proto\n";
                }

                if (!empty($_POST['addr'])) {
                    $addr = htmlspecialchars($_POST['addr']);
                    $output .= "diagnose debug flow filter addr $addr\n";
                }

                if (!empty($_POST['saddr'])) {
                    $saddr = htmlspecialchars($_POST['saddr']);
                    $output .= "diagnose debug flow filter saddr $saddr\n";
                }

                if (!empty($_POST['daddr'])) {
                    $daddr = htmlspecialchars($_POST['daddr']);
                    $output .= "diagnose debug flow filter daddr $daddr\n";
                }

                if (!empty($_POST['port'])) {
                    $port = htmlspecialchars($_POST['port']);
                    $output .= "diagnose debug flow filter port $port\n";
                }

                if (!empty($_POST['sport'])) {
                    $sport = htmlspecialchars($_POST['sport']);
                    $output .= "diagnose debug flow filter sport $sport\n";
                }

                if (!empty($_POST['dport'])) {
                    $dport = htmlspecialchars($_POST['dport']);
                    $output .= "diagnose debug flow filter dport $dport\n";
                }

                if ($output) {
                    $output .= "\ndiagnose debug flow show function-name enable\n";
                    $output .= "diagnose debug flow show iprope enable\n";
                    $output .= "diagnose debug enable\n";
                    $output .= "diagnose debug console timestamp enable\n";
                    $output .= "diagnose debug flow trace start 10\n";

                    echo "<div id='output'><pre>$output</pre></div>";
                }
            }
            ?>
        </div><br><br>
        <a href="index.php">back to index</a>
    </main>
</body>
</html>
