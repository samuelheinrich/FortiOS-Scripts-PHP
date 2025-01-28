<!DOCTYPE html>
<html>
<head>
    <title>Service Object Generator *new*</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: auto;
            font-size: 16px; /* Schriftgröße angepasst */
        }

        h1 {
            text-align: center;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        h1 a {
            text-decoration: none;
            background-color: #007bff;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            font-size: 14px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 4px;
            margin-bottom: 10px;
        }

        input[type="submit"] {
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .full-width {
            width: 100%;
        }

        .back-link {
            margin-top: 20px;
            text-align: center;
        }

        .back-link a {
            text-decoration: none;
            background-color: #28a745;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            font-size: 14px;
        }

        .back-link a:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>
            Service Object Generator *new*
            <a href="service-gen-old.php">Old Version</a>
        </h1>
        <form action="" method="post">
            <label for="tcp_ports">TCP Ports (Format: port,optional description):</label>
            <textarea id="tcp_ports" name="tcp_ports" rows="6" placeholder="443,https port"><?php echo htmlspecialchars($_POST['tcp_ports'] ?? ''); ?></textarea>

            <label for="udp_ports">UDP Ports (Format: port,optional description):</label>
            <textarea id="udp_ports" name="udp_ports" rows="6" placeholder="53,dns"><?php echo htmlspecialchars($_POST['udp_ports'] ?? ''); ?></textarea>

            <label for="group_name">Groupname (e.g., sg-xxx):</label>
            <input type="text" id="group_name" name="group_name" placeholder="sg-xxx" value="<?php echo htmlspecialchars($_POST['group_name'] ?? ''); ?>">

            <label for="group_description">Group Comment:</label>
            <input type="text" id="group_description" name="group_description" placeholder="servicegroup description..." value="<?php echo htmlspecialchars($_POST['group_description'] ?? ''); ?>">

            <input type="submit" name="generate" value="Generate Configuration">
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tcp_ports = htmlspecialchars($_POST['tcp_ports'] ?? '');
            $udp_ports = htmlspecialchars($_POST['udp_ports'] ?? '');
            $group_name = htmlspecialchars($_POST['group_name'] ?? '');
            $group_description = htmlspecialchars($_POST['group_description'] ?? '');
            $output = '';
            $names = [];

            function processPorts($ports, $prefix) {
                global $output, $names;
                $lines = array_filter(array_map('trim', explode("\n", $ports)));

                foreach ($lines as $line) {
                    $parts = explode(",", $line);
                    $port = trim($parts[0]);
                    $description = trim($parts[1] ?? '');
                    $name = "$prefix/$port";

                    $names[] = $name;

                    $output .= "config firewall service custom\n";
                    $output .= "    edit \"$name\"\n";
                    if (!empty($description)) {
                        $output .= "        set comment \"$description\"\n";
                    }
                    if ($prefix === "s-tcp") {
                        $output .= "        set tcp-portrange $port\n";
                    } elseif ($prefix === "s-udp") {
                        $output .= "        set udp-portrange $port\n";
                    }
                    $output .= "    next\n";
                    $output .= "end\n\n";
                }
            }

            if (!empty($tcp_ports)) {
                processPorts($tcp_ports, "s-tcp");
            }

            if (!empty($udp_ports)) {
                processPorts($udp_ports, "s-udp");
            }

            if (!empty($group_name)) {
                $member_list = implode('" "', $names);

                $output .= "config firewall service group\n";
                $output .= "    edit \"$group_name\"\n";
                $output .= "        set member \"$member_list\"\n";
                if (!empty($group_description)) {
                    $output .= "        set comment \"$group_description\"\n";
                }
                $output .= "    next\n";
                $output .= "end\n\n";
            }

            echo "<pre id='configCode'>" . htmlspecialchars($output) . "</pre>";
            echo '<button onclick="copyToClipboard()">Copy to Clipboard</button>';
        }
        ?>

        <div class="back-link">
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
    </div>
</body>
</html>
