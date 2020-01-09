#!/bin/bash
#Author: Abdullah ilyas

echo "
 ____  _   _    _    ____   _____        __
/ ___|| | | |  / \  |  _ \ / _ \ \      / /
\___ \| |_| | / _ \ | | | | | | \ \ /\ / / 
 ___) |  _  |/ ___ \| |_| | |_| |\ V  V /  
|____/|_| |_/_/   \_\____/ \___/  \_/\_/   
"



echo  "Checking the Distribution"

ver=`uname -a | awk '{print $9}'` 

echo $ver;


if [ "$ver" ==  "x86_64" ]; then

echo "System Verified. Updating System & Installing NRPE"


apt-get update

apt-get install -y autoconf automake gcc libc6 libmcrypt-dev make libssl-dev wget

sleep 3

echo "Downloading NRPE.Please wait..."

sleep 2

cd /tmp

wget --no-check-certificate -O nrpe.tar.gz https://github.com/NagiosEnterprises/nrpe/archive/nrpe-3.2.1.tar.gz

sleep 3

echo "Extracting the Package......"

sleep 2

tar xzf nrpe.tar.gz

sleep 2

echo "Changing directory and installing nrpe-v3.2.1"

cd /tmp/nrpe-nrpe-3.2.1/

sleep 2

./configure --enable-command-args

sleep 2

make all

echo "Creating Users and Groups..."

sleep 2

make install-groups-users

echo "Installing binaries..."

make install

sleep 3

echo "Installing configuration files"

sleep 3

make install-config

sleep 3

echo "Opening Port 5666 for communication"

iptables -I INPUT -p tcp -m tcp --dport 5666 -j ACCEPT

echo "Port opened!"

sleep 3

make install-init

sleep 3

echo "Downloading Plugins for x86_64 System"

wget https://github.com/abdallah303/abdullah/archive/master.zip

mv master.zip /usr/local/nagios/libexec/

cd /usr/local/nagios/libexec/ && unzip master.zip

tar -xzf plugins.tar.gz


echo "Update Configuration File"

cd /usr/local/nagios/etc/


sed -i '/^allowed_hosts=/s/$/,10.28.82.5/' /usr/local/nagios/etc/nrpe.cfg

sed -i 's/^dont_blame_nrpe=.*/dont_blame_nrpe=1/g' /usr/local/nagios/etc/nrpe.cfg

sleep 3

echo "Enabling Service........."

sleep 3

systemctl enable nrpe.service

echo "Starting Service........."

systemctl start nrpe.service
systemctl status nrpe.service

else echo "For 32-bit Systems, please see the documentation.";
fi
