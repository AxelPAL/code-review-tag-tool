#!/bin/bash

export TERM=xterm
./artisan config:cache --env=testing
./artisan test
if [ $? -ne 0 ]; then
  echo "Fix your code before running tests and commit!"
  echo "Run following command to show in which files you've got problems:"
  echo "./artisan config:cache --env=testing && ./artisan test && ./artisan config:cache"
  exit 1;
fi
./artisan config:cache
./vendor/bin/phpstan analyze .
if [ $? -ne 0 ]; then
  echo "Fix your code before running commit!"
  echo "Run following command to show in which files you've got problems:"
  echo "./vendor/bin/phpstan analyze ."
  exit 1;
fi