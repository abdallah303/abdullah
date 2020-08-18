#!/bin/bash
#Author: Abdullah ilyas


CUR=`who --ips | awk {'print $1 "\t" $5'}`
TOTAL=`who --ips | awk {'print $1 "\t" $5'} | wc -l`


if [[ $TOTAL -eq 1 ]];
then
	  echo OK! $CUR is Logged in.
  elif [[ $CUR -gt 1 ]];
  then
	  echo WARNING! $TOTAL Users are Logged in.
  elif [[ $CUR -gt 5 ]]
  then
	  echo CRITICAL! $TOTAL Users are Logged in.
  fi

