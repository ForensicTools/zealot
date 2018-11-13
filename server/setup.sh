#!/bin/bash
echo ""
echo "ZEALOT: Server setup initialized."
echo "Authors: Chris Partridge, Geoff Kanteles"
echo ""
echo "This script should be run on a dedicated system (VM or SBC), Debian preferred."
echo "The current IP of this machine must be, and is assumed to be, static."
echo "Additionally, this script must be run as root."
echo "If any of the above is not true, please hit Ctrl+C now, fix, and rerun later."
echo ""
printf "You have 10s to cancel: "
sleep 1
printf "10 "
sleep 1
printf "9 "
sleep 1
printf "8 "
sleep 1
printf "7 "
sleep 1
printf "6 "
sleep 1
printf "5 "
sleep 1
printf "4 "
sleep 1
printf "3 "
sleep 1
printf "2 "
sleep 1
printf "1 "
sleep 1
echo "OK, ZEALOT SETTING UP"

echo "ZEALOT: Updating repos and installing prerequisites..."
apt-get update
apt-get upgrade -y
apt-get install -y dnsmasq git curl apache2 libapache2-mod-php mysql-server mysql-client php-mysql

echo "ZEALOT: Setting configuration files..."
mkdir /etc/zealot
mv -f /etc/dnsmasq.conf /etc/dnsmasq.conf.prev
cp ./install/dnsmasq.conf /etc/dnsmasq.conf

SQL_PASS=$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 32 | head -n 1)
echo $SQL_PASS > /etc/zealot/sql

echo "ZEALOT: Setting up database..."
mysql -u root -e "DROP DATABASE IF EXISTS zealot"
mysql -u root -e "CREATE DATABASE zealot"
mysql -u root -e "DROP USER IF EXISTS zealot"
mysql -u root -e "CREATE USER 'zealot'@'%' IDENTIFIED BY '$SQL_PASS'"
mysql -u root -e "GRANT ALL PRIVILEGES ON zealot.* TO 'zealot'@'%'"
mysql -u root -e "FLUSH PRIVILEGES"

mysql -u root zealot < ./install/zealot.sql

echo "ZEALOT: Running scripts to prepare system..."
php ./tools/update-hosts.php

echo "ZEALOT: Populating web directory..."
rm -r /var/www/html/*
cp -r ./web/* /var/www/html

echo "ZEALOT: Flushing and filling crontab..."

echo "ZEALOT: Setup finished. Rebooting for sanity."
reboot
