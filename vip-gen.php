<!DOCTYPE html>
<html>
<head>
<style>
    form {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
    input[type="text"], input[type="number"] {
        width: 40%;
        padding: 4px;
    }
    label {
        display: block;
        margin-bottom: 4px;
    }
    .form-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-template-rows: auto auto auto;
    grid-gap: 10px;
  }

  .full-width {
    grid-column: 1 / span 2;
  }
</style>
    <title>Fortigate VIP Objekt Generator</title>
</head>
<body>

<form action="vip-gen.php" method="post" class="form-container">
  <div>
    <label for="wan_interface">WAN Interface:</label>
    <input type="text" name="wan_interface" id="wan_interface" placeholder="vlxxxx">
  </div>
  <div>
    <label for="destination_zone">Destination Zone:</label>
    <input type="text" name="destination_zone" id="destination_zone" placeholder="z-xxx">
  </div>
  <div>
    <label for="public_ip">Public IP:</label>
    <input type="text" name="public_ip" id="public_ip" placeholder="x.x.x.x">
  </div>
  <div>
    <label for="public_port">Public Port:</label>
    <input type="number" name="public_port" id="public_port" min="1" max="65535" placeholder="min=1 max=65535">
  </div>
  <div>
    <label for="private_ip">Private IP:</label>
    <input type="text" name="private_ip" id="private_ip"  placeholder="y.y.y.y">
  </div>
  <div>
    <label for="private_port">Private Port:</label>
    <input type="number" name="private_port" id="private_port" min="1" max="65535" placeholder="min=1 max=65535">
  </div>
  <div class="full-width">
    <label for="beschreibung">Beschreibung:</label>
    <input type="text" name="beschreibung" id="beschreibung" placeholder="xxx.domain.com / server XYZ">
  </div>
  <div class="full-width">
    <input type="submit" value="Absenden">
  </div>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $wan_interface = $_POST['wan_interface'];
    $public_ip = $_POST['public_ip'];
    $public_port = $_POST['public_port'];
    $private_ip = $_POST['private_ip'];
    $private_port = $_POST['private_port'];
    $beschreibung = $_POST['beschreibung'];
    $destination_zone = $_POST['destination_zone'];

    $config_code = <<<EOT
config firewall service custom
    edit "sg-vip-$public_ip:$public_port"
        set comment "DNAT $public_ip:$public_port to $private_ip:$private_port $beschreibung"
        set tcp-portrange $public_port
    next
end

config firewall vip
    edit "vip-$public_ip:$public_port"
        set comment "DNAT $public_ip:$public_port to $private_ip:$private_port $beschreibung"
        set service "sg-vip-$public_ip:$public_port"
        set extip $public_ip
        set mappedip "$private_ip"
        set extintf "$wan_interface"
        set color 6
    next
end

config firewall policy
    edit 0
        set name "vip-$public_ip:$public_port ($beschreibung)"
        set srcintf "virtual-wan-link"
        set dstintf "$destination_zone"
        set action accept
        set srcaddr "INTERNET"
        set dstaddr "vip-$public_ip:$public_port"
        set schedule "always"
        set service "sg-vip-$public_ip:$public_port"
        set utm-status enable
        set logtraffic all
        set comments "DNAT $public_ip:$public_port to $private_ip:$private_port $beschreibung"
    next
end

EOT;

    echo "<pre>" . htmlspecialchars($config_code) . "</pre>";
}
?>


<br><br><br>
<a href="index.php">Zurück zum Index</a><br>
</body>
</html>
