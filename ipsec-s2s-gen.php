<!DOCTYPE html>
<html>
<head>
    <title>IPSec Site-to-Site Generator</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f0f0f0;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh; /* Hier wurde die Änderung vorgenommen */
        margin: 0;
    }

        .container {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            width: 80%;
        }

        h1 {
            text-align: center;
        }

        form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        label {
            display: block;
            font-weight: bold;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 0.5rem;
            border-radius: 5px;
            border: 1px solid #d1d1d1;
            resize: none;
        }

        textarea {
            height: 120px;
        }

        input[type="submit"] {
            display: block;
            margin-top: 1rem;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            border: none;
            background-color: #007bff;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        pre {
            font-size: 14px;
            text-align: center;
        }

        .back-link {
            display: block;
            margin-top: 2rem;
            text-align: center;
        }

        .back-link a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
        pre {
        text-align: left;
    }
    .red {
    color: red;
    }
    .blue {
    color: blue;
    }
    .black {
    color: black;
}

    </style>
</head>
<body>
<div class="container">
    <h1>IPSec Site-to-Site Generator</h1>
    <form action="ipsec-s2s-gen.php" method="post">
    <div>
    <label for="site_a_name">Site A Name:</label>
    <input type="text" name="site_a_name" id="site_a_name" value="<?php echo isset($_POST['site_a_name']) ? htmlspecialchars($_POST['site_a_name']) : 's2s-siteA-1' ?>" required>
    <label for="site_a_peer_ip">Site A Peer IP:</label>
    <input type="text" name="site_a_peer_ip" id="site_a_peer_ip" value="<?php echo isset($_POST['site_a_peer_ip']) ? htmlspecialchars($_POST['site_a_peer_ip']) : '1.1.1.1' ?>" required>
    <label for="site_a_wan_interface">Site A WAN Interface:</label>
    <input type="text" name="site_a_wan_interface" id="site_a_wan_interface" value="<?php echo isset($_POST['site_a_wan_interface']) ? htmlspecialchars($_POST['site_a_wan_interface']) : 'wan1' ?>" required>
    <label for="site_a_local_network">Site A Local Network:</label>
    <textarea name="site_a_local_network" id="site_a_local_network" required><?php echo isset($_POST['site_a_local_network']) ? htmlspecialchars($_POST['site_a_local_network']) : '192.168.10.0/24' ?></textarea>
</div>
<div>
    <label for="site_b_name">Site B Name:</label>
    <input type="text" name="site_b_name" id="site_b_name" value="<?php echo isset($_POST['site_b_name']) ? htmlspecialchars($_POST['site_b_name']) : 's2s-siteB-1' ?>" required>
    <label for="site_b_peer_ip">Site B Peer IP:</label>
    <input type="text" name="site_b_peer_ip" id="site_b_peer_ip" value="<?php echo isset($_POST['site_b_peer_ip']) ? htmlspecialchars($_POST['site_b_peer_ip']) : '2.2.2.2' ?>" required>
    <label for="site_b_wan_interface">Site B WAN Interface:</label>
    <input type="text" name="site_b_wan_interface" id="site_b_wan_interface" value="<?php echo isset($_POST['site_b_wan_interface']) ? htmlspecialchars($_POST['site_b_wan_interface']) : 'wan1' ?>" required>
    <label for="site_b_local_network">Site B Local Network:</label>
    <textarea name="site_b_local_network" id="site_b_local_network" required><?php echo isset($_POST['site_b_local_network']) ? htmlspecialchars($_POST['site_b_local_network']) : '192.168.20.0/24' ?></textarea>
</div>
<div>
    <label for="preshared_key">Pre-shared Key:</label>
    <input type="text" name="preshared_key" id="preshared_key" value="<?php echo isset($_POST['preshared_key']) ? htmlspecialchars($_POST['preshared_key']) : generateKey() ?>" required>
    <label for="comment">Comment:</label>
    <input type="text" name="comment" id="comment" value="<?php echo isset($_POST['comment']) ? htmlspecialchars($_POST['comment']) : 'VPN Tunnel xyz' ?>" required>
</div>
<input type="submit" value="Generate">

    </form>

    <?php

function generateKey() {
    // Ersetzen Sie die Liste durch Ihre eigene oder eine umfangreichere Wörterliste
    $words = ['Algorithmus', 'Bug', 'Cookie', 'Debugger', 'Einhorn', 'Firewall', 'Git', 'Hashtag', 'Internet', 'Java', 'Kabelsalat', 'Linux', 'Malware', 'Nanosekunde', 'Overflow', 'Pixel', 'Quantencomputer', 'Router', 'Screencast', 'Turing', 'Unicode', 'VirtuelleRealität', 'Webhook', 'XSS', 'Yottabyte', 'Zeroday', 'Applet', 'Bit', 'Cloud', 'Datenmüll', 'Emoticon', 'Firmware', 'Gigahertz', 'Hyperlink', 'Inkognito', 'Jumbotron', 'Kryptografie', 'Login', 'Metadaten', 'Netzneutralität', 'OpenSource', 'Ping', 'Quantenverschränkung', 'Roboter', 'Spam', 'Trojaner', 'URL', 'Virus', 'Wireframe', 'XOR', 'Y2K', 'Zugriffszeit'];

    // Zufällige Wörter aus der Liste auswählen
    $selectedWords = [];
    for ($i = 0; $i < 3; $i++) {
        $index = array_rand($words);
        $selectedWords[] = $words[$index];
        
        // Entfernen Sie das ausgewählte Wort aus der Liste, damit es nicht wieder ausgewählt wird
        unset($words[$index]);
    }

    // Fügen Sie die ausgewählten Wörter und eine zufällige Nummer zusammen
    return implode('-', $selectedWords) . '-' . rand(0, 9);
}

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $site_a_name = $_POST['site_a_name'];
        $site_b_name = $_POST['site_b_name'];
        $site_a_peer_ip = $_POST['site_a_peer_ip'];
        $site_b_peer_ip = $_POST['site_b_peer_ip'];
        $site_a_wan_interface = $_POST['site_a_wan_interface'];
        $site_b_wan_interface = $_POST['site_b_wan_interface'];
        $site_a_local_network = $_POST['site_a_local_network'];
        $site_b_local_network = $_POST['site_b_local_network'];
        $preshared_key = $_POST['preshared_key'];
        $comment = $_POST['comment'];

        $output = "config vpn ipsec phase1-interface\n";
        $output .= "    edit \"$site_a_name\"\n";
        $output .= "        set interface \"$site_a_wan_interface\"\n";
        $output .= "        set ike-version 2\n";
        $output .= "        set peertype any\n";
        $output .= "        set net-device disable\n";
        $output .= "        set proposal chacha20poly1305-prfsha512\n";
        $output .= "        set dpd on-idle\n";
        $output .= "        set comment \"$site_a_name to $site_b_name $comment\"\n";
        $output .= "        set dhgrp 31\n";
        $output .= "        set nattraversal disable\n";
        $output .= "        set remote-gw $site_b_peer_ip\n";
        $output .= "        set psksecret $preshared_key\n";
        $output .= "        set dpd-retryinterval 5\n";
        $output .= "    next\n";
        $output .= "end\n";

        $output .= "\nconfig vpn ipsec phase2-interface\n";
        $output .= "    edit \"$site_a_name\"\n";
        $output .= "        set phase1name \"$site_a_name\"\n";
        $output .= "        set proposal chacha20poly1305\n";
        $output .= "        set src-subnet $site_a_local_network\n";
        $output .= "        set dst-subnet $site_b_local_network\n";
        $output .= "        set auto-negotiate enable\n";
        $output .= "    next\n";
        $output .= "end\n";

        $output .= "config router static\n";
        $output .= "    edit 0\n";
        $output .= "        set dst $site_b_local_network\n";
        $output .= "        set priority 5\n";
        $output .= "        set device \"$site_a_wan_interface\"\n";
        $output .= "        set comment \"$site_a_name to $site_b_name $comment\"\n";
        $output .= "    next\n";
        $output .= "end\n";

        $outputB = "config vpn ipsec phase1-interface\n";
        $outputB .= "    edit \"$site_b_name\"\n";
        $outputB .= "        set interface \"$site_b_wan_interface\"\n";
        $outputB .= "        set ike-version 2\n";
        $outputB .= "        set peertype any\n";
        $outputB .= "        set net-device disable\n";
        $outputB .= "        set proposal chacha20poly1305-prfsha512\n";
        $outputB .= "        set dpd on-idle\n";
        $outputB .= "        set comment \"$site_b_name to $site_a_name $comment\"\n";
        $outputB .= "        set dhgrp 31\n";
        $outputB .= "        set nattraversal disable\n";
        $outputB .= "        set remote-gw $site_a_peer_ip\n";
        $outputB .= "        set psksecret $preshared_key\n";
        $outputB .= "        set dpd-retryinterval 5\n";
        $outputB .= "    next\n";
        $outputB .= "end\n";

        $outputB .= "\nconfig vpn ipsec phase2-interface\n";
        $outputB .= "    edit \"$site_b_name\"\n";
        $outputB .= "        set phase1name \"$site_b_name\"\n";
        $outputB .= "        set proposal chacha20poly1305\n";
        $outputB .= "        set src-subnet $site_b_local_network\n";
        $outputB .= "        set dst-subnet $site_a_local_network\n";
        $outputB .= "        set auto-negotiate enable\n";
        $outputB .= "    next\n";
        $outputB .= "end\n";

        $outputB .= "config router static\n";
        $outputB .= "    edit 0\n";
        $outputB .= "        set dst $site_a_local_network\n";
        $outputB .= "        set priority 5\n";
        $outputB .= "        set device \"$site_b_wan_interface\"\n";
        $outputB .= "        set comment \"$site_b_name to $site_a_name $comment\"\n";
        $outputB .= "    next\n";
        $outputB .= "end\n";


   
        echo "<pre id='configCodeA' style='color:red;'>" . htmlspecialchars($output) . "</pre>";
        echo "<pre id='configCodeB' style='color:blue;'>" . htmlspecialchars($outputB) . "</pre>";
    
    }
    ?>
    
    <br><br>
    
    <button onclick="copyToClipboard()">Copy to Clipboard</button>
    
    <script>
    function copyToClipboard() {
      /* Get the text content from the configCodeA and configCodeB element */
      const configCodeA = document.getElementById('configCodeA').textContent.trim();
      const configCodeB = document.getElementById('configCodeB').textContent.trim();

      /* Create a temporary textarea element to copy the text to the clipboard */
      const tempTextarea = document.createElement('textarea');
      tempTextarea.value = configCodeA + '\n' + configCodeB;
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

    
    
    
    
    <br><br><br> <br>
    <a href="index.php">back to index</a>

    <br>caveats<br>
    - only 1 subnet per site<br>
    - no parameter modification<br>
    - requires manuel acl creation<br>

</div>
</body>
</html>