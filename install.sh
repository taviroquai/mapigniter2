#!/bin/bash

####################################################################
#                                                                  #
# MapIgniter2 Install Script                                       #
# https://github.com/taviroquai/mapigniter2                        #
# License: MIT                                                     #
#                                                                  #
# Requirements:                                                    #
#                                                                  #
# 1. Running on Ubuntu 14.04                                       #
# 2. Apache and PHP are installed                                  #
# 3. PostgreSQL is installed (will be used by default)             #
# 4. Git is installed                                              #
# 5. Run in the apache folder (will create a mapigniter2 folder)   #
#                                                                  #
####################################################################

# Set target install directory
TARGET="."

# Set database name. This must match database name in file .env
DBUSER="mapigniter2"
DBNAME="mapigniter2"

# Reset commands (only for testing purposes)
#su - postgres -c "dropdb $DBNAME"
#rm -f .env
#rm -Rf ./public/storage
#exit 0

# Check requirements
echo "Checking requirements..."
which php
if [ $? -ne 0 ]; then { echo "Aborting: PHP not found" ; exit 1; } fi
which psql
if [ $? -ne 0 ]; then { echo "Aborting: PostgreSQL not found" ; exit 1; } fi
if [ "`php -m | grep pdo_pgsql | wc -l`" -eq 0 ]; then { echo "Aborting: PHP5 Pgsql module not found" ; exit 1; } fi

# Create PostgreSQL database (PostgreSQL by default in .env.example)
echo "Checking PostgreSQL database..."
if [ "`su - postgres -c \"psql -l\" | grep $DBNAME | wc -l`" -eq 0 ]
then
    echo "Creating PostgreSQL user"
    su - postgres -c "psql -c \"CREATE USER mapigniter2 WITH password 'postgres'\""
    echo "Creating PostgreSQL database..."
    su - postgres -c "psql -c \"CREATE DATABASE $DBNAME OWNER $DBUSER\""
    if [ $? -ne 0 ]
    then
        echo "Aborting: failed create database" ;
        exit 1;
    fi
fi

# Change to directory
cd "$TARGET"
echo "Installing at $(pwd)..."

# Creating configuration file from .env.example
echo "Creating .env configuration file..."
if [ ! -f "./$TARGET/.env" ];
then
    cp .env.example .env
fi

# Install dependencies with composer
echo "Installing Laravel dependencies..."
if [ ! -d "./$TARGET/vendor" ];
then
    ./composer.phar install --prefer-dist
fi

# Generate new application key
php artisan key:generate
if [ $? -ne 0 ]
then
    echo "Aborting: failed create database" ;
    exit 1; 
fi

# Create database schema
php artisan migrate

# Insert default database data (this creates the storage folder)
echo "Install demo data if does not exists..." ;
if [ ! -d "./$TARGET/public/storage" ];
then
    php artisan db:seed
fi

# Give write permissions to Apache to the following folders
echo "Giving write permissions to folders..."
chmod -R 777 storage bootstrap/cache/ public/storage resources/views/pages

# Inform user that is complete
echo "Finished! Open in web browser."
exit 0