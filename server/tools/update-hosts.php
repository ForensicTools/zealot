<?php
$hostIP = str_replace('\n', '', exec('ip addr show eth0 | grep "inet\b" | awk \'{print $2}\' | cut -d/ -f1'));
exec('echo "' . $hostIP . ' api.zealot" > /etc/zealot/self.list'); 

echo "wrote /etc/zealot/self.list" . PHP_EOL;
?>
