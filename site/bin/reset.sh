#!/bin/bash
echo "Starting with reset"
echo "==========================================="

echo "Composer Update"
echo "==========================================="
sudo composer update

DIR=`php -r "echo dirname(dirname(realpath('$0')));"`
cd "$DIR"
echo "Clearing cache on app/cache app/logs"
echo "==========================================="
sudo rm -Rf app/cache/* app/logs/* 

sudo php app/console doctrine:database:drop --force
sudo php app/console doctrine:database:create
sudo php app/console doctrine:schema:update --force
sudo php app/console doctrine:fixtures:load

echo "Perfoming Cache warmup"
echo "==========================================="
sudo php app/console cache:warmup

echo "Setting Permissions on app/cache app/logs "
echo "==========================================="
sudo chmod 0777 -R app/cache/ app/logs/ 
sudo chmod 0777 -R app/cache/* app/logs/* 

echo "Done, Thank you for your patiance!!!"
