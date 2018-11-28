<?php
$configDbPass = trim(file_get_contents("/etc/zealot/sql"));
$mysqli = new mysqli("localhost", "zealot", $configDbPass, "zealot");

if($mysqli->connect_error) {
    echo "ERROR";
    exit;
}

$showtime = false; // NOW YOU'VE DONE IT https://www.youtube.com/watch?v=kX1ytyCG09g

$dbBlacklist = $mysqli->query("SELECT * FROM `blacklist`");
$blacklist = [];
while($getBlacklist = mysqli_fetch_assoc($dbBlacklist)) {
    $blacklist[] = $getBlacklist["CountryISO"];
}

$dbLive = $mysqli->query("SELECT * FROM `live_stats`");
$liveData = [];
$liveSum = 0;
while($getLive = mysqli_fetch_assoc($dbLive)) {
    if(in_array($getLive["CountryISO"], $blacklist)) {
        $liveData[$getLive["CountryISO"]] = 2 * $getLive["Count"]; // double impact of blacklisted countries
    } else {
        $liveData[$getLive["CountryISO"]] = $getLive["Count"];
    }
    $liveSum = $liveSum + $getLive["Count"];
}

if($liveSum > 1000) { // must have adequate sample size to run analysis
    $dbMeta = $mysqli->query("SELECT * FROM `meta_stats`");
    $metaData = [];
    $metaSum = 0;
    while($getMeta = mysqli_fetch_assoc($dbMeta)) {
        $metaData[$getMeta["CountryISO"]] = $getMeta["Count"];
        $metaSum = $metaSum + $getMeta["Count"];
    }

    $varSum = 0;
    $unAckLive = 0; // $ackLive found later
    $ackMeta = 0; // $unAckMeta found later
    $ackCount = 0;

    foreach($liveData as $liveCode => $liveCount) {
        $livePercent = $liveCount / ($liveSum + 0.000000001); // buffer against NaN if meta_stats not populated
        if(!array_key_exists($liveCode, $metaData)) { // no known metadata
            // echo "No key for " . $liveCode . PHP_EOL; // debug
            $unAckLive = $unAckLive + $liveCount;
        } else {
            $ackCount++;
            $metaPercent = $metaData[$liveCode] / ($metaSum + 0.000000001); // buffer against NaN if meta_stats not populated
            $ackMeta = $ackMeta + $metaData[$liveCode];
            // echo $liveCode . ": " . $liveCount . "/" . $liveSum . " (" . $livePercent . "), "; // debug
            // echo $metaData[$liveCode] . "/" . $metaSum . " (" . $metaPercent . "), "; // debug
            $thisPow = pow(($livePercent - $metaPercent), 2);
            $varSum = $varSum + $thisPow;
            // echo $thisPow . PHP_EOL; // debug
        }
    }

    $ackLive = $liveSum - $unAckLive;
    $unAckMeta = $metaSum - $ackMeta;
    $unAckLivePercent = $unAckLive / ($liveSum + 0.000000001);
    $unAckMetaPercent = $unAckMeta / ($metaSum + 0.000000001); // buffer against NaN if meta_stats not populated

    // echo "UnAck: (L, " . $unAckLivePercent . "), (M, " . $unAckMetaPercent . ") -> "; // debug
    $thisPow = pow(($unAckLivePercent - $unAckMetaPercent), 2);
    $varSum = $varSum + $thisPow;
    // echo $thisPow . PHP_EOL; // debug

    $variance = $varSum / ($ackCount + 0.000000001); // didn't bother to add unacks
    $stdDev = pow($variance, 0.5);
    // echo "Var = " . $variance . ", StdDev = " . $stdDev . PHP_EOL; // debug

    if($variance > 0.02) {
        $showtime = true;
    }
}

if($showtime) { // triggering run condition, push live stats to meta, truncate
    foreach($liveData as $saveKey => $saveCount) {
        $mysqli->query("INSERT INTO `meta_stats` (`CountryISO`, `Count`)
                        VALUES ('" . $saveKey . "', " . $saveCount . ")
                        ON DUPLICATE KEY UPDATE `Count` = `Count` + " . $saveCount . "");
        $mysqli->query("TRUNCATE `live_stats`");
    }
    echo "RUN";
} else {
    echo "WAIT";
}

$mysqli->close();
?>
