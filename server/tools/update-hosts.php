<?php
$hostIP = str_replace('\n', '', exec('ip addr show eth0 | grep "inet\b" | awk \'{print $2}\' | cut -d/ -f1'));

exec('echo "api.zealot,' . $hostIP . ',::1" > /etc/zealot/self.list'); 
?>
