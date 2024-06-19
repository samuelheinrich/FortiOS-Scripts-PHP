<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Filter Generator</title>
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
                    <h1>Session Filter Generator</h1>
        <form method="post">
            <?php
            function retain_value($field_name) {
                return isset($_POST[$field_name]) ? htmlspecialchars($_POST[$field_name]) : '';
            }
            function retain_checked($field_name) {
                return isset($_POST[$field_name]) ? 'checked' : '';
            }
            ?>
            <label for="sintf">Source Interface: <input type="text" name="sintf" id="sintf" value="<?= retain_value('sintf') ?>"></label>
            <label for="dintf">Destination Interface: <input type="text" name="dintf" id="dintf" value="<?= retain_value('dintf') ?>"></label>
            <label for="src">Source IP Address: <input type="text" name="src" id="src" value="<?= retain_value('src') ?>"></label>
            <label for="nsrc">NAT'd Source IP Address: <input type="text" name="nsrc" id="nsrc" value="<?= retain_value('nsrc') ?>"></label>
            <label for="dst">Destination IP Address: <input type="text" name="dst" id="dst" value="<?= retain_value('dst') ?>"></label>
            <label for="proto">Protocol Number: <input type="text" name="proto" id="proto" value="<?= retain_value('proto') ?>"></label>
            <label for="sport">Source Port: <input type="text" name="sport" id="sport" value="<?= retain_value('sport') ?>"></label>
            <label for="nport">NAT'd Source Port: <input type="text" name="nport" id="nport" value="<?= retain_value('nport') ?>"></label>
            <label for="dport">Destination Port: <input type="text" name="dport" id="dport" value="<?= retain_value('dport') ?>"></label>
            <label for="policy">Policy ID: <input type="text" name="policy" id="policy" value="<?= retain_value('policy') ?>"></label>
            <label for="expire">Expire: <input type="text" name="expire" id="expire" value="<?= retain_value('expire') ?>"></label>
            <label for="duration">Duration: <input type="text" name="duration" id="duration" value="<?= retain_value('duration') ?>"></label>
            <label for="ext-src">Extended Source: <input type="text" name="ext-src" id="ext-src" value="<?= retain_value('ext-src') ?>"></label>
            <label for="ext-dst">Extended Destination: <input type="text" name="ext-dst" id="ext-dst" value="<?= retain_value('ext-dst') ?>"></label>
            <label><input type="checkbox" name="clear" id="clear" <?= retain_checked('clear') ?>> Clear</label>
            <label><input type="checkbox" name="list" id="list" <?= retain_checked('list') ?>> List</label>
            <input type="submit" value="Generate Command">
        </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $output = "";
            $filter_commands = [
                'sintf' => 'sintf',
                'dintf' => 'dintf',
                'src' => 'src',
                'nsrc' => 'nsrc',
                'dst' => 'dst',
                'proto' => 'proto',
                'sport' => 'sport',
                'nport' => 'nport',
                'dport' => 'dport',
                'policy' => 'policy',
                'expire' => 'expire',
                'duration' => 'duration',
                'ext-src' => 'ext-src',
                'ext-dst' => 'ext-dst',
            ];
            
            foreach ($filter_commands as $key => $command) {
                if (!empty($_POST[$key])) {
                    $value = htmlspecialchars($_POST[$key]);
                    $output .= "diagnose sys session filter $command $value\n";
                }
            }
            
            $clear = isset($_POST['clear']);
            $list = isset($_POST['list']);
            
            if (!$clear && !$list) {
                echo "<div id='output'>Bitte wählen Sie eine der Optionen 'Clear' oder 'List'.</div>";
            } else {
                $output .= "diagnose sys session filter\n";

                if ($clear) {
                    $output .= "diagnose sys session filter clear\n";
                } elseif ($list) {
                    $output .= "diagnose sys session list\n";
                }

                echo "<div id='output'><pre>$output</pre></div>";
            }
        }
        ?>
        </div><br><br>
        <a href="index.php">back to index</a>
    </main>
</body>
</html>
