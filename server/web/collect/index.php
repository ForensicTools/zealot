<?php
$configDbPass = trim(file_get_contents("/etc/zealot/sql"));
$mysqli = new mysqli("localhost", "zealot", $configDbPass, "zealot");

if($mysqli->connect_error) {
    echo "ERROR";
    exit;
}

$dbBlacklist = $mysqli->query("SELECT * FROM `blacklist`");
$blacklist = [];
while($getBlacklist = mysqli_fetch_assoc($dbBlacklist)) {
    $blacklist[] = $getBlacklist["CountryISO"];
}

$dbLive = $mysqli->query("SELECT * FROM `live_stats`");
$liveData = [];
$showtime = false;
while($getLive = mysqli_fetch_assoc($dbLive)) {
    $liveData[] = $getLive;
    if(in_array($getLive["CountryISO"], $blacklist)) {
        $showtime = true;
    }
}

if($showtime) { // blacklisted, push live stats to meta
    foreach($liveData as $toSave) {
        $mysqli->query("INSERT INTO `meta_stats` (`CountryISO`, `Count`)
                        VALUES ('" . $toSave["CountryISO"] . "', " . $toSave["Count"] . ")
                        ON DUPLICATE KEY UPDATE `Count` = `Count` + " . $toSave["Count"] . "");
        $mysqli->query("TRUNCATE `live_stats`");
    }
}

if($showtime) {
    echo "RUN";
} else {
    echo "WAIT";
}
$mysqli->close();
?>
