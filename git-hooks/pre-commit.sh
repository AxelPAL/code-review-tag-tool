#!/bin/bash

echo 'Checking PHPCS...';

commitFiles=`git diff-index --cached --name-only --diff-filter=d HEAD`
args="-s --standard=phpcs.xml -q"

phpFiles="";
phpFilesCount=0;
for f in $commitFiles; do
	if [[ $f =~ \.(php)$ ]]; then
		phpFilesCount=$phpFilesCount+1
		phpFiles="$phpFiles $f"
	fi
done;
if [[ $phpFilesCount = 0 ]]; then
  echo 'There are no php files to process';
	exit 0;
fi

TERM=xterm ./vendor/bin/phpcs $args $commitFiles

if [ $? -ne 0 ]; then
  echo "Fix the style problems in your code before commit!"
  echo "Run following command to show in which files you've got problems:"
  echo "make hook-pre-commit"
  exit 1;
fi

echo 'No style problems have been found';