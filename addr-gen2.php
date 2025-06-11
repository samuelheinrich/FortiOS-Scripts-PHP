<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Address Objekt Generator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 2rem;
            margin: 0;
        }
        .container {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 960px;
            width: 100%;
        }
        h1 {
            text-align: center;
        }
        form {
            display: grid;
            gap: 1rem;
        }
        label {
            font-weight: bold;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 4px;
        }
        input[type="submit"], button, a {
            display: inline-block;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover, button:hover, a:hover {
            background-color: #0056b3;
        }
        pre {
            background: #000;
            color: #fff;
            padding: 1rem;
            border-radius: 5px;
            overflow-x: auto;
            margin-top: 1rem;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Address Generator</h1>

        <form action="addr-gen2.php" method="post">
            <label>
                <input type="checkbox" name="remote" value="1" <?php echo isset($_POST['remote']) ? 'checked' : ''; ?>>
                Remote (verwende hr-/nr- statt h-/n-)
            </label>

            <label for="comment_prefix">Comment Prefix (optional):</label>
            <input type="text" name="comment_prefix" id="comment_prefix" placeholder="z. B. z-int" value="<?php echo isset($_POST['comment_prefix']) ? htmlspecialchars($_POST['comment_prefix']) : ''; ?>">

            <label for="name_subnet_comment">Eingabe (IP oder Netz, optional mit Kommentar):</label>
            <textarea name="name_subnet_comment" id="name_subnet_comment" rows="8"><?php
echo isset($_POST['name_subnet_comment']) ? htmlspecialchars($_POST['name_subnet_comment']) : <<<EOD
192.168.10.11
192.168.10.11,z-int vl10 Server 11
192.168.10.0/24
192.168.10.0/24,z-int vl10 subnet
EOD;
?></textarea>

            <input type="submit" value="generate">

            <label for="group_name">Groupname: z. B. "hg-" (HostGroup), "ng-" (NetworkGroup)</label>
            <input type="text" name="group_name" id="group_name" placeholder="ng-xxx" value="<?php echo isset($_POST['group_name']) ? htmlspecialchars($_POST['group_name']) : ''; ?>">

            <label for="group_description">Group comment (z. B. Zone, VLAN, Name)</label>
            <input type="text" name="group_description" id="group_description" placeholder="z-int vl0012 host..." value="<?php echo isset($_POST['group_description']) ? htmlspecialchars($_POST['group_description']) : ''; ?>">

            <input type="submit" name="with_group" value="generate with group">
        </form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_lines = htmlspecialchars($_POST['name_subnet_comment']);
    $lines = array_filter(array_map('trim', explode("\n", $input_lines)));

    $comment_prefix = isset($_POST['comment_prefix']) ? trim($_POST['comment_prefix']) : '';
    $is_remote = isset($_POST['remote']) && $_POST['remote'] == '1';

    $output = '';
    $names = [];

    foreach ($lines as $line) {
        $parts = explode(",", $line);
        $address = trim($parts[0]);
        $comment = isset($parts[1]) ? trim($parts[1]) : '';

        if (!empty($comment_prefix) && !empty($comment)) {
            $comment = $comment_prefix . ' ' . $comment;
        } elseif (!empty($comment_prefix)) {
            $comment = $comment_prefix;
        }

        // IP vs Subnetz mit Remote-Logik
        if (preg_match('/\/\d+$/', $address)) {
            $name = ($is_remote ? "nr-" : "n-") . $address;
            $subnet = $address;
        } else {
            $name = ($is_remote ? "hr-" : "h-") . $address;
            $subnet = $address . "/32";
        }

        $names[] = $name;

        $config_code = <<<EOT
config firewall address
    edit "$name"
EOT;
        if (!empty($comment)) {
            $config_code .= "\n        set comment \"$comment\"";
        }
        $config_code .= "\n        set subnet $subnet\n    next\nend";

        $output .= $config_code . "\n";
    }

    // Gruppe (optional)
    if (isset($_POST['with_group'])) {
        $group_name = htmlspecialchars($_POST['group_name']);
        $group_description = htmlspecialchars($_POST['group_description']);
        $member_list = implode('" "', $names);

        $config_group = <<<EOT
config firewall addrgrp
    edit "$group_name"
        set member "$member_list"
        set comment "$group_description"
    next
end
EOT;

        $output .= $config_group . "\n";
    }

    echo "<pre id='configCode'>" . htmlspecialchars($output) . "</pre>";
}
?>

        <button onclick="copyToClipboard()">Copy to Clipboard</button>
        <br><br>
        <a href="index.php">zurück zur Übersicht</a>

        <script>
        function copyToClipboard() {
            const configCode = document.getElementById('configCode').textContent.trim();
            const tempTextarea = document.createElement('textarea');
            tempTextarea.value = configCode;
            document.body.appendChild(tempTextarea);
            tempTextarea.select();
            document.execCommand('copy');
            document.body.removeChild(tempTextarea);
            alert('Configuration code copied to clipboard!');
        }
        </script>
    </div>
</body>
</html>
