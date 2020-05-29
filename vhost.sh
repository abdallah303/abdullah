

#!/bin/bash
################################################################################################
# Description: This script is created to create reverse proxy vhost.
#
#              This script is to be run as root only.
#	       $1 is the FQDN.
#              $2 is the IP address of the system.
################################################################################################

# Variable definitions
VHOST=/etc/nginx/sites-available/$1

# Check if the script is run as root
if [ "$(id -u)" != "0" ]; then
	  printf "This script can only be executed by root.\nYou are not authorized to use the script.\nThis incident will be reported to NOC.\n" 1>&2
	    exit 1
    fi

    # Create staging vhost
    touch $VHOST
    echo "server {" >> $VHOST
    echo "  listen 80;" >> $VHOST
    echo "  server_name $1;" >> $VHOST
    echo "  access_log /var/log/nginx/$1-access.log main;" >> $VHOST
    echo "  error_log /var/log/nginx/$1-error.log;" >> $VHOST
    echo "  location / { proxy_pass http://$2:80; }" >> $VHOST
    echo "}" >> $VHOST

    # Enable vhost and restart NGINX web server
    /usr/sbin/ngxensite $1
    /etc/init.d/nginx restart
