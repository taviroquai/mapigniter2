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
TARGET="mapigniter2"

# Set database name. This must match database name in file .env
DBNAME="mapigniter2"

# Reset commands (only for testing purposes)
#sudo rm -Rf "$TARGET"
#sudo -u postgres dropdb "$DBNAME"
#exit 0

# Check requirements
echo "Checking requirements..."
which apache2
if [ $? -ne 0 ]; then { echo "Aborting: Apache2 not found" ; exit 1; } fi
which php
if [ $? -ne 0 ]; then { echo "Aborting: PHP not found" ; exit 1; } fi
which git
if [ $? -ne 0 ]; then { echo "Aborting: Git not found" ; exit 1; } fi
which psql
if [ $? -ne 0 ]; then { echo "Aborting: PostgreSQL not found" ; exit 1; } fi

# Create PostgreSQL database (PostgreSQL by default in .env.example)
echo "Checking PostgreSQL database..."
if [ "`sudo -u postgres psql -l | grep $DBNAME | wc -l`" -eq 0 ]
then
    echo "Creating PostgreSQL database..."
    sudo -u postgres createdb "$DBNAME"
    if [ $? -ne 0 ]
    then
        echo "Aborting: failed create database" ;
        exit 1;
    fi
fi

# Clone mapigniter repository (If does not exists)
echo "Download repository with Git..."
if [ ! -d "./$TARGET" ];
then
    git clone http://github.com/taviroquai/mapigniter2 "$TARGET"
fi

# Change to directory
cd "$TARGET"
echo $(pwd)

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
echo "Creating storage folder..." ;
if [ ! -d "./$TARGET/storage" ];
then
    php artisan db:seed
fi

# Give write permissions to Apache to the following folders
chmod -R 777 storage bootstrap/cache/ public/storage resources/views/pages

# Inform user that is complete
echo "Finished! Open in web browser."
exit 0