#!/bin/bash
tail -n 0 -F /var/log/dnsmasq.log | \
while read LINE
do
echo "$LINE" | grep " reply " | grep " is "
if [ $? = 0 ]
then
arrLN=(${LINE})
php process.php ${arrLN[-1]}
fi
done
