<!DOCTYPE html>
<html>
<head>
    <title>Forti Script generator</title>
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
            max-width: 500px;
        }

        h1 {
            text-align: center;
        }

        pre {
            font-size: 14px;
            text-align: center;
        }

        .links {
            display: grid;
            gap: 1rem;
        }

        a.button {
            display: inline-block;
            text-align: center;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-weight: bold;
            font-size: 18px;
            position: relative;
        }

        a.button2 {
            display: inline-block;
            text-align: center;
            background-color: #fc03be;
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-weight: bold;
            font-size: 18px;
            position: relative;
        }

        a.button3 {
            display: inline-block;
            text-align: center;
            background-color: #FF0000;
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-weight: bold;
            font-size: 18px;
            position: relative;
        }
        a.button:hover {
            background-color: #0056b3;
        }

        a.button::after {
            content: attr(data-tooltip);
            position: absolute;
            display: none;
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 5px;
            font-size: 12px;
            white-space: nowrap;
            bottom: calc(100% + 10px);
            left: 50%;
            transform: translateX(-50%);
        }



        a.button:hover::after {
            display: block;
        }

    </style>
</head>
<body>
    <div class="container">
        <pre>
  _____              __  .__               
_/ ____\____________/  |_|__|              
\   __\/  _ \_  __ \   __\  |              
 |  | (  <_> )  | \/|  | |  |              
 |__|  \____/|__|   |__| |__|              
                                           
                    .__        __          
  ______ ___________|__|______/  |_  ______
 /  ___// ___\_  __ \  \____ \   __\/  ___/
 \___ \\  \___|  | \/  |  |_> >  |  \___ \ 
/____  >\___  >__|  |__|   __/|__| /____  >
     \/     \/         |__|             \/ 
        </pre>
        <div class="links">
            <div class="category">FSOS CLI syntax generators</div>
            <a href="vip-gen.php" class="button" data-tooltip="This script generates VIP objects for Fortigate devices.">Fortigate VIP Objekt Generator</a>
            <a href="addr-gen2.php" class="button" data-tooltip="This script generates Address objects for Fortigate devices.">Fortigate Address Objekt Generator</a>
            <a href="interface-gen.php" class="button" data-tooltip="This script generates Interface objects for Fortigate devices.">Fortigate Interface Objekt Generator</a>
            <a href="service-gen.php" class="button" data-tooltip="This script generates Service objects for Fortigate devices.">Fortigate Service Objekt Generator</a>
            <a href="route-gen.php" class="button" data-tooltip="This script generates Static Route configurations for Fortigate devices.">Static Route Generator</a>
            <a href="fqdn-gen.php" class="button" data-tooltip="This script generates FQDN objects for Fortigate devices.">FQDN Generator</a>
            <a href="flow-trace-gen.php" class="button" data-tooltip="This script generates debug flow trace output.">flow trace Generator</a>
            <a href="diag-session-gen.php" class="button3" data-tooltip="This script generates diag system session output.">diag sys session Generator</a>
            <a href="sslvpn-hardening.php" class="button" data-tooltip="Generates VIP Config to DNAT SSLVPN to Loopback.">SSLVPN on Loopback</a>
            <a href="log-to-fwrule-gen.php" class="button2" data-tooltip="Generates VIP Config to DNAT SSLVPN to Loopback.">*NEW* Log to FW Rule generator</a>
            <div class="category">Additional Tools (experimental)</div>            
            <a href="fwrule-gen.php" class="button" data-tooltip="This script generates firewall rules for Fortigate devices.">legacy fwrule Generator</a>
            <a href="ipsec-s2s-gen.php" class="button" data-tooltip="This script generates ipsec p1/p2 code Fortigate devices.">legacy ipsec s2s Generator</a>
            <a href="address-object-renamer.php" class="button" data-tooltip="This script renames Address objects for Fortigate devices.">legacy Address Object Renamer</a>
            <div class="category">3rd party tools</div>    
            <a href="xml-converter.php" class="button" data-tooltip="This script converts Fortigate configurations to XML format.">Sophos XML to CSV Converter</a>
            <a href="alcatel-description-maker.php" class="button" data-tooltip="Generates Alcatel Description with lldp input">Alcatel LLDP description maker</a>
            <a href="cui-checker.php" class="button" data-tooltip="Cisco Unsed inteface checker">Cisco Unsed inteface checker</a>
</div>
</div>
</body>
</html>
