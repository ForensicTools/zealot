<?php
require_once 'vendor/autoload.php';
use MaxMind\Db\Reader;
$ipAddress = trim($argv[1]);

echo "running ";

if(filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) || filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
    $reader = new Reader('GeoLite2-Country.mmdb');
    $allData = $reader->get($ipAddress);

    echo "validIP ";

    if($allData) {
        //var_dump($allData);
        echo "validISO ";

        $countryISO = $allData["country"]["iso_code"];

        $configDbPass = trim(file_get_contents("/etc/zealot/sql"));
        $mysqli = new mysqli("localhost", "zealot", $configDbPass, "zealot");

        $mysqli->query("INSERT INTO `meta_stats` (`CountryISO`, `Count`)
                        VALUES ('" . $countryISO . "', 1)
                        ON DUPLICATE KEY UPDATE `Count` = `Count` + 1");

        echo mysql_error($mysqli);
        $mysqli->close();
    }

    $reader->close();
} // else die/do nothing

echo PHP_EOL;
?>
