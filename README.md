# Download contacts from AVM FRITZ!Box and convert them to Fritz!adr format

Purpose of the software is the (automatic) downloading of contact data with FAX numbers from your Fritzbox and transfer them into the AVM Fritz!adr database format (dBase).

This is a spin-off from https://github.com/BlackSenator/carddav2fb. If you´re using the AVM FAX solution fax4box and want the adressbook FAX numbers synchron with them in your phonebook(s) of your Fritz!Box this is a way.

## Requirements

  * PHP >=7.0 (`apt-get install php php-curl php-mbstring php-xml`)
  * Composer (follow the installation guide at https://getcomposer.org/download/)

## Installation

Install box2dbf:

    cd /
    git clone https://github.com/blacksenator/box2dbf.git
    cd box2dbf

Install composer (see https://getcomposer.org/download/ for newer instructions):

    composer install --no-dev --no-suggest

Edit `config.example.php` and save as `config.php` or use an other name of your choice (but than keep in mind to use the -c option to define your renamed file)

## Usage

List all commands:

    php box2dbf -h

Complete processing:

    php box2dbf run

## License
This script is released under Public Domain, some parts under GNU AGPL or MIT license. Make sure you understand which parts are which.

## Authors
Copyright (c) 2018 -2019 Volker Püschel and maybe others