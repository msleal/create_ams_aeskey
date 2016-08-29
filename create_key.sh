#!/bin/bash
#Copyright (c) 2016, Marcelo Leal
#Description: Simple Wrapper to call the Azure Media Services AES Protection key Creation Script
#License: MIT (see LICENSE.txt file for details)

if [[ -z "$1" ]]; then
   echo "You need to inform the Content Key 'Name' to be created..."
   exit 1;
else
   php -d display_errors=1 createcontentkey_aes.php "$1"
   exit $?;
fi
