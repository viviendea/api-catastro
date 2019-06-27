Catastro API
=============================

Library to get cadastre information from the official source
(made with awesome SOAP) by the spanish government.


Installation
------------

This project assumes you have composer installed.

Add the library to your project simply executing: 


    composer require ibonkonesa/catastro-es 


Usage
-----


    use IbonKonesa\Catastro\Client
    
    ----
    
    $client = new Client();
    
    


There are many methods exposed:


#### getRegions

Returns all spanish regions

#### getTowns

Returns towns by a given region. You can also query in order to get more accurated results

#### getStreets

Return streets by a given region and town.  You can also query in order to get more accurated results with a street type or by name

#### checkNumber

Check if a number exists by a given region, town and street. If the
number does not exist, an array with near numbers will be returned


#### getDataByLocation

Returns cadastre information by a given complete address. If there is
not a exact match, an array with near cadastral references will be
returned


#### getDataByReference

Returns cadastre information by a given reference. If there is not a
exact match, an array with near cadastral references will be returned

Tests
-----

Execute phpunit to test all previous methods
