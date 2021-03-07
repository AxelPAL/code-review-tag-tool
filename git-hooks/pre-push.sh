#!/bin/bash

export TERM=xterm
./vendor/bin/phpunit
if [ $? -ne 0 ]; then
  echo "Fix your code before running tests and commit!"
  echo "Run following command to show in which files you've got problems:"
  echo "./vendor/bin/phpunit"
  exit 1;
fi
./vendor/bin/phpstan analyze .
if [ $? -ne 0 ]; then
  echo "Fix your code before running commit!"
  echo "Run following command to show in which files you've got problems:"
  echo "./vendor/bin/phpstan analyze ."
  exit 1;
fi