#!/usr/bin/env bash

DELETE_OTHERS=yes
BACKUPS_PATH=/root/backups
THRESHOLD=$(date -d "3 days ago" +%Y%m%d%H%M)

## Find all files in $BACKUPS_PATH. The -type f means only files
## and the -maxdepth 1 ensures that any files in subdirectories are
## not included. Combined with -print0 (separate file names with \0),
## IFS= (don't break on whitespace), "-d ''" (records end on '\0') , it can
## deal with all file names.
find ${BACKUPS_PATH} -maxdepth 1 -type f -print0  | while IFS= read -d '' -r file
do
	    ## Does this file name match the pattern (13 digits, then .zip)?
	        if [[ "$(basename "$file")" =~ ^[0-9]{12}.zip$ ]]
			    then
				            ## Delete the file if it's older than the $THR
					            [ "$(basename "$file" .zip)" -le "$THRESHOLD" ] && rm -v -- "$file"
						        else
								        ## If the file does not match the pattern, delete if 
									        ## DELETE_OTHERS is set to "yes"
										        [ $DELETE_OTHERS == "no" ] &&  echo  "$file" is left.
											    fi
										    done
