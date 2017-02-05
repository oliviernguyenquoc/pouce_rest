#!/bin/bash
echo "-------------------------------"
echo "--------- DEPLOYEMENT ---------"
echo "-------------------------------"
echo ""

# Tests
echo "Run all tests"
phpUnit
echo "Done !"

# Clear prod cache
echo "Clear cache - Prod environnement"
php bin/console cache:clear --env=prod
echo "Clear cache - Prod environnement: Done !"

# Clear dev cache
echo "Clear cache - Dev environnement"
php bin/console cache:clear --env=dev
echo "Clear cache - Dev environnement: Done !"

echo "------- DEPLOYEMENT DONE -------"