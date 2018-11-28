<?php
$configDbPass = trim(file_get_contents("/etc/zealot/sql"));
$mysqli = new mysqli("localhost", "zealot", $configDbPass, "zealot");

if($mysqli->connect_error) {
    echo "ERROR";
    exit;
}

echo "RUN";
$mysqli->close();
?>
