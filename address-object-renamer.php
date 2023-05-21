<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Address Object Renamer   ---- BAUSTELLE ---- </title>
</head>
<body>
    <h1>Address Object Renamer</h1>
    <form action="address-object-renamer.php" method="post">
        <label for="config_code">Fortigate Configuration Code:</label>
        <textarea name="config_code" id="config_code" rows="10" style="width: 100%;"></textarea>
        <input type="submit" value="Rename Address Objects">
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $config_code = $_POST['config_code'];
        $output = '';

        preg_match_all('/config firewall address.*?next/s', $config_code, $matches);

        foreach ($matches[0] as $match) {
            preg_match('/edit "(.*?)"/', $match, $edit_match);
            preg_match('/set subnet (.*?)\n/', $match, $subnet_match);

            if (count($edit_match) < 2 || count($subnet_match) < 2) {
                continue;
            }

            $old_name = $edit_match[1];
            $subnet_str = $subnet_match[1];
            $parts = explode(' ', $subnet_str);
            $ip = $parts[0];
            $mask = $parts[1];

            if ($mask === '255.255.255.255') {
                $new_name = "h-$ip";
            } else {
                $prefix = 32 - log((ip2long($mask) ^ ip2long('255.255.255.255')) + 1, 2);
                $new_name = "n-$ip/$prefix";
            }

            $output .= "config firewall address\n";
            $output .= "    rename \"$old_name\" to \"$new_name\"\n";
            $output .= "end\n\n";
        }

        echo "<textarea id='output' rows='10' style='width: 100%;' readonly>" . htmlspecialchars($output) . "</textarea>";
        echo "<button onclick='copyToClipboard()' style='display: block;'>In die Zwischenablage kopieren</button>";
    }
    ?>

    <script>
        function copyToClipboard() {
            const outputTextarea = document.getElementById('output');
            outputTextarea.select();
            document.execCommand('copy');
        }
    </script>
<br><br><br>
<a href="index.php">Zurück zum Index</a><br>
</body>
</html>
