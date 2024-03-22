<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>FQDN-Objekt-Generator</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            margin: 0;
            padding: 20px;
            font-family: Arial, sans-serif;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
            width: 80%;
            max-width: 600px;
        }

        textarea {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
            height: 300px; /* Vergrößerte Eingabefelder */
        }

        input[type="submit"], .back-button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }

        input[type="submit"]:hover, .back-button:hover {
            background-color: #0056b3;
        }

        .output {
            background-color: #f8f9fa;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 10px;
            width: 100%;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Alcatel Description Maker</h1>
    <form method="post">
        <label for="input1">Show LLDP remote-system</label>
        <textarea id="input1" name="input1"><?php echo isset($_POST['input1']) ? htmlspecialchars($_POST['input1']) : ''; ?></textarea>

        <label for="input2">show configuration snapshot linkagg</label>
        <textarea id="input2" name="input2"><?php echo isset($_POST['input2']) ? htmlspecialchars($_POST['input2']) : ''; ?></textarea>

        <input type="submit" value="Generate Output">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $input1 = $_POST["input1"];
        $input2 = $_POST["input2"];

        preg_match_all('/Remote LLDP nearest-bridge Agents on Local Port (\S+):[\s\S]+?System Name\s+=\s+(\S+),[\s\S]+?System Description\s+=\s+([\S\s]+?),\s*(?:Version|[\r\n])/', $input1, $matches1, PREG_SET_ORDER);

        preg_match_all('/(\d+\/\d+\/\d+)\s+Dynamic\s+\d+\s+ATTACHED\s+\d+\s+UP\s+UP/', $input2, $matches2, PREG_SET_ORDER);
        $aggInfo = [];
        foreach ($matches2 as $match) {
            $aggInfo[$match[1]] = "agg";
        }

         echo '<div class="output">';
        foreach ($matches1 as $match) {
            $rport = $match[1];
            $systemname = $match[2];
            $desc = $match[3];
            
            // Anpassung für die Entfernung gesperrter Wörter
            $removePatterns = [
                '/-Lucent/i', 
                '/\bEnterprise\b/i', 
                '/\bAlcatel\b/i', 
                '/\bAruba\b/i', 
                '/\bVersion\b/i',
                '/\b(?:Jan(?:uary)?|Feb(?:ruary)?|Mar(?:ch)?|Apr(?:il)?|May|Jun(?:e)?|Jul(?:y)?|Aug(?:ust)?|Sep(?:tember)?|Oct(?:ober)?|Nov(?:ember)?|Dec(?:ember)?)\s+\d{1,2},\s+\d{4}\b/i'
            ];
            foreach ($removePatterns as $pattern) {
                $desc = preg_replace($pattern, '', $desc);
            }
            $desc = preg_replace('/\s+/', ' ', $desc);
            $desc = trim($desc, " ,."); // Verbesserte Trim-Funktion


            // Extrahiere Agg Nummer
            $aggNum = "";
            foreach ($matches2 as $aggMatch) {
                if ($aggMatch[1] === $rport) {
                    $aggNum = $aggMatch[4]; // Korrigiert, Index ist 4, nicht 5
                    break;
                }
            }

            $aggPrefix = !empty($aggNum) ? "agg $aggNum " : "";

            $aggPrefix = isset($aggInfo[$rport]) ? "agg $aggNum " : "";
            echo "<p>interfaces port $rport alias \"$aggPrefix$desc $systemname\"</p>";
            //debug array
            //echo '<pre>'; print_r($matches2); echo '</pre>';
        }
        echo '</div>';
    }
    ?>
    <a href="index.php" class="back-button">Back to Index</a>
</div>

</body>
</html>
