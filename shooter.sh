#!/bin/bash
#Author: Abdullah ilyas

echo " This script Require root Privileges."

ssh-keygen
for hosts in `cat /root/hosts.txt`; do
	ssh-copy-id -i ~/.ssh/id_rsa.pub $hosts
done
echo "done"

