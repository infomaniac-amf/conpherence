#!/bin/bash
echo "Generating entities"
php artisan entities:generate ./schema/conpherence.mwb ./src/Conpherence/Entities/Base --generate-derived=true --tables=$1
composer dumpautoload -o