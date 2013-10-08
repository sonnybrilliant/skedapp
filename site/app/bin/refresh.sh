#!/bin/bash
echo "Starting with refresh"
echo "==========================================="

echo "Composer Self Update"
echo "==========================================="
sudo composer self-update

echo "Composer Update"
echo "==========================================="
sudo composer update

DIR=`php -r "echo dirname(dirname(realpath('$0')));"`
# cd "$DIR"
# this does not work - Need something that works

echo "Clearing cache on app/cache app/logs"
echo "==========================================="
sudo rm -Rf app/cache/* app/logs/* app/spool/*

sudo php app/console doctrine:schema:update --force

echo "Perfoming Cache warmup"
echo "==========================================="
sudo php app/console cache:warmup

echo "Setting Permissions on app/cache app/logs "
echo "==========================================="
sudo chmod 0777 -R app/cache/ app/logs/ web/uploads/ app/spool/
sudo chmod 0777 -R app/cache/* app/logs/* web/uploads/* app/spool/*

echo "Installing assets "
sudo php app/console assets:install --symlink web

echo "Done, Thank you for your patiance!!!"
