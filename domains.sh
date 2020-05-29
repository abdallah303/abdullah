#!/bin/bash
#Author : Abdullah ilyas

echo "Requesting WHOIS info...."
while read domain; do whois $domain; 
done < domain.txt  | grep -e "Domain Name" -e "Expiry" -e "Name Server"




