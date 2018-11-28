<?php
$configDbPass = trim(file_get_contents("/etc/zealot/sql"));
$mysqli = new mysqli("localhost", "zealot", $configDbPass, "zealot");

if($mysqli->connect_error) {
    echo "Something's gone wrong, we can't access the database.";
    exit;
}

echo "<h1>Zealot</h1>";

$dbBlacklist = $mysqli->query("SELECT * FROM `blacklist`");
$blacklist = [];
echo "<p>Blacklist: ";
while($getBlacklist = mysqli_fetch_assoc($dbBlacklist)) {
    $blacklist[] = $getBlacklist["CountryISO"];
    echo $getBlacklist["CountryISO"] . " ";
}
echo "</p>";

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

echo "<h3>Country Statistics</h3>";
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
        echo "<p>" . $liveCode . ": (live " . $liveCount . "/" . $liveSum . "), "; // debug
        if(!array_key_exists($liveCode, $metaData)) { // no known metadata
            $unAckLive = $unAckLive + $liveCount;
            echo "(meta not available; country unseen)</p>";
        } else {
            $ackCount++;
            $metaPercent = $metaData[$liveCode] / ($metaSum + 0.000000001); // buffer against NaN if meta_stats not populated
            $ackMeta = $ackMeta + $metaData[$liveCode];
            echo "(meta " . $metaData[$liveCode] . "/" . $metaSum . ") -> ";
            $thisPow = pow(($livePercent - $metaPercent), 2);
            $varSum = $varSum + $thisPow;
            echo number_format(round($thisPow, 6), 6) . "</p>";
        }
    }

    echo "<h3>Data Statistics</h3>";
    $ackLive = $liveSum - $unAckLive;
    $unAckMeta = $metaSum - $ackMeta;
    $unAckLivePercent = $unAckLive / ($liveSum + 0.000000001);
    $unAckMetaPercent = $unAckMeta / ($metaSum + 0.000000001); // buffer against NaN if meta_stats not populated

    echo "Unseen %: (live " . number_format(round($unAckLivePercent, 4), 4) . "), ";
    echo "(meta " . number_format(round($unAckMetaPercent, 4), 4) . ") -> ";
    $thisPow = pow(($unAckLivePercent - $unAckMetaPercent), 2);
    $varSum = $varSum + $thisPow;
    echo number_format(round($thisPow, 4), 4) . "</p>";

    $variance = $varSum / ($ackCount + 0.000000001); // didn't bother to add unacks
    $stdDev = pow($variance, 0.5);
    echo "<h3>Final Values</h3>";

    if($variance > 0.02) {
        echo "<p><b>Var = " . $variance . ", StdDev = " . $stdDev . "</b></p>";
    } else {
        echo "<p>Var = " . $variance . ", StdDev = " . $stdDev . "</p>";
    }
}

$mysqli->close();
?>
