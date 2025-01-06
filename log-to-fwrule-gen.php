<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Firewall Rule Generator</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; padding: 20px; }
        textarea, input, button { width: 100%; margin-bottom: 20px; }
        textarea { height: 150px; }
        button { padding: 10px; cursor: pointer; }
        pre { background-color: #f4f4f4; padding: 10px; margin-top: 20px; }
        h3 { border-bottom: 2px solid #000; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table th, table td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        table th { background-color: #ddd; }
    </style>
</head>
<body>
    <h1>Firewall Rule Generator</h1>
    <form method="post">
        <label for="logdata">Enter Raw Log Data:</label>
        <textarea id="logdata" name="logdata" placeholder="Paste raw log data here..."><?= htmlspecialchars($_POST['logdata'] ?? '') ?></textarea>
        <label for="srcintf">Source Interface (required):</label>
        <input type="text" id="srcintf" name="srcintf" placeholder="z-vrf-untrust" value="<?= htmlspecialchars($_POST['srcintf'] ?? '') ?>" required>
        <label for="dstintf">Destination Interface (optional, default: virtual-wan-link):</label>
        <input type="text" id="dstintf" name="dstintf" placeholder="virtual-wan-link" value="<?= htmlspecialchars($_POST['dstintf'] ?? 'virtual-wan-link') ?>">
        <button type="submit">Generate</button>
    </form>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
<?php
$logData = trim($_POST['logdata']);
$srcIntf = trim($_POST['srcintf']);
$dstIntf = trim($_POST['dstintf']) ?: 'virtual-wan-link';

if (empty($logData) || empty($srcIntf)) {
    echo "<p style='color:red;'>Source Interface is required and log data cannot be empty.</p>";
    exit;
}

// Function to generate a random hex value
function generateRandomHex($length = 6) {
    return strtoupper(bin2hex(random_bytes($length / 2)));
}

// Initialize data structures
$services = [];
$hosts = [];
$firewallRules = [];
$dstGroups = [];
$uniqueRules = [];

// Split log data into lines
$lines = explode("\n", $logData);

foreach ($lines as $line) {
    $line = trim($line);

    // Parse key-value pairs from the line
    preg_match_all('/(\w+)=(".*?"|\S+)/', $line, $matches, PREG_SET_ORDER);

    $parsedData = [];
    foreach ($matches as $match) {
        $key = $match[1];
        $value = trim($match[2], '"');
        $parsedData[$key] = $value;
    }

    // Skip if required fields are missing
    if (
        empty($parsedData['srcip']) ||
        empty($parsedData['dstip']) ||
        empty($parsedData['dstport']) ||
        empty($parsedData['proto']) ||
        empty($parsedData['service'])
    ) {
        continue;
    }

    // Extract relevant data
    $source = $parsedData['srcip'];
    $destination = $parsedData['dstip'];
    $dstPort = $parsedData['dstport'];
    $protocol = $parsedData['proto'];
    $service = $parsedData['service'];

    // Generate service object
    $protocolName = $protocol == 6 ? 'tcp' : 'udp';
    $serviceKey = "$protocolName/$dstPort";
    if (!isset($services[$serviceKey])) {
        $services[$serviceKey] = [
            'protocol' => $protocolName,
            'port' => $dstPort,
            'comment' => $service
        ];
    }

    // Generate host objects
    $srcKey = "h-$source";
    $dstKey = "hr-$destination";

    if (!isset($hosts[$srcKey])) {
        $hosts[$srcKey] = [
            'comment' => "Source Host",
            'subnet' => "$source/32"
        ];
    }

    if (!isset($hosts[$dstKey])) {
        $hosts[$dstKey] = [
            'comment' => $service,
            'subnet' => "$destination/32"
        ];
    }

    // Consolidate rules with the same service and multiple destinations
    $ruleHash = md5($source . $dstPort . $protocol);
    if (!isset($uniqueRules[$ruleHash])) {
        $uniqueRules[$ruleHash] = [
            'srcKey' => $srcKey,
            'serviceKey' => "s-$serviceKey",
            'dstKeys' => []
        ];
    }

    if (!in_array($dstKey, $uniqueRules[$ruleHash]['dstKeys'])) {
        $uniqueRules[$ruleHash]['dstKeys'][] = $dstKey;
    }
}

// Create firewall rules and network groups
foreach ($uniqueRules as $ruleHash => $data) {
    $srcKey = $data['srcKey'];
    $serviceKey = $data['serviceKey'];
    $dstKeys = $data['dstKeys'];

    // If more than one destination, create a network group
    if (count($dstKeys) > 1) {
        $groupName = sprintf("ng-IA_h-%s_%s", str_replace('h-', '', $srcKey), generateRandomHex());
        $dstGroups[$groupName] = $dstKeys;
        $dstAddr = $groupName;
    } else {
        $dstAddr = $dstKeys[0];
    }

    // Add the rule
    $firewallRules[] = [
        'name' => sprintf("IA_h-%s(%s)", str_replace('h-', '', $srcKey), generateRandomHex()),
        'srcintf' => $srcIntf,
        'dstintf' => $dstIntf,
        'srcaddr' => $srcKey,
        'dstaddr' => $dstAddr,
        'service' => $serviceKey,
        'schedule' => 'always'
    ];
}
?>

        <h3># Service Objects</h3>
        <pre>
config firewall service custom
<?php foreach ($services as $key => $service): ?>
    edit "s-<?= $service['protocol'] ?>/<?= $service['port'] ?>"
        set comment "<?= htmlspecialchars($service['comment']) ?>"
        set <?= $service['protocol'] ?>-portrange <?= $service['port'] ?>    
        next
<?php endforeach; ?>
end
        </pre>

        <h3># Host Objects</h3>
        <pre>
config firewall address
<?php foreach ($hosts as $key => $host): ?>
    edit <?= $key ?>        
        set comment "<?= htmlspecialchars($host['comment']) ?>"
        set subnet "<?= htmlspecialchars($host['subnet']) ?>"
    next
<?php endforeach; ?>
end
        </pre>

<h3># Network Groups</h3>
<pre>
config firewall addrgrp
<?php foreach ($dstGroups as $groupName => $groupMembers): ?>
    edit <?= htmlspecialchars($groupName) ?>

        set member <?= implode(' ', array_map('htmlspecialchars', $groupMembers)) ?>

        set comment "<?= htmlspecialchars($groupName) ?>"
    next
<?php endforeach; ?>
end
</pre>

<h3># Firewall Rules</h3>
<pre>
config firewall policy
<?php foreach ($firewallRules as $rule): ?>
    edit 0
        set name <?= htmlspecialchars($rule['name']) ?>        
        set srcintf <?= htmlspecialchars($rule['srcintf']) ?>        
        set dstintf <?= htmlspecialchars($rule['dstintf']) ?>        
        set action accept
        set srcaddr "<?= htmlspecialchars($rule['srcaddr']) ?>"
        set dstaddr "<?= htmlspecialchars($rule['dstaddr']) ?>"
        set service "<?= htmlspecialchars($rule['service']) ?>"
        set logtraffic all
        set nat enable
        set schedule <?= htmlspecialchars($rule['schedule']) ?>

    next
<?php endforeach; ?>
end

<h3>Firewall Rules Summary</h3>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Rule Name</th>
            <th>Source Interface</th>
            <th>Destination Interface</th>
            <th>Source Address</th>
            <th>Destination Address</th>
            <th>Service</th>
            <th>Schedule</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $ruleCounter = 1;
        foreach ($firewallRules as $rule): ?>
            <tr>
                <td><?= $ruleCounter++ ?></td>
                <td><?= htmlspecialchars($rule['name']) ?></td>
                <td><?= htmlspecialchars($rule['srcintf']) ?></td>
                <td><?= htmlspecialchars($rule['dstintf']) ?></td>
                <td><?= htmlspecialchars($rule['srcaddr']) ?></td>
                <td><?= htmlspecialchars($rule['dstaddr']) ?></td>
                <td><?= htmlspecialchars($rule['service']) ?></td>
                <td><?= htmlspecialchars($rule['schedule']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</pre>

        <button onclick="location.href='index.php'">Back to Index</button>
    <?php endif; ?>
</body>
</html>
