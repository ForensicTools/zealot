#!/bin/bash
cd /root/zealot/server/tools
curl http://geolite.maxmind.com/download/geoip/database/GeoLite2-Country.tar.gz > gl2c.tar.gz
tar xvzf gl2c.tar.gz
mv GeoLite2* gl2x
mv -f ./gl2x/GeoLite2-Country.mmdb ../bg/GeoLite2-Country.mmdb
rm -r gl2x
rm gl2c.tar.gz
