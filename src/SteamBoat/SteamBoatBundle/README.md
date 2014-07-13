SteamBoatBundle
===============

Welcome to SteamBoat...
A tool to help you find out more about your friends on Steam.

1) Installation
---------------

Create a new database and add settings to app/config/parameters.yml.

### Use Composer

Create the database schema in a terminal, from the webroot:

    php app/console doctrine:schema:update --force

Set your webserver to use the "web" folder as the web root.

2) Use
------

E.g. http://steamboat.localhost/steam/{nickname}