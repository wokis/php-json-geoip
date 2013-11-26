php-json-geoip
==============

REST API for MaxMind GeoLite2 using PHP and JSON.

The MaxMind PHP reader class can be found at https://github.com/maxmind/MaxMind-DB-Reader-php

The database itself can be found at http://dev.maxmind.com/geoip/geoip2/geolite2/

## Online API

Online API access can be found at 

    http://api.kacper.se/geoip/{ip}

{ip} should be replaced with the actual IP address to geolocate. Any valid IPv4 or IPv6 address will work. RFC 1918 and RFC 3927 addresses will return no match found. If no IP address is given, the connecting host´s IP address will be used.

No match found returns http status code 404.

Invalid IP address returns http status code 400.

Example

    http://api.kacper.se/geoip/8.8.8.8
	or
	http://api.kacper.se/geoip/2001:4860:4860::8888
	
At this time there is no request limit in place, please don't abuse the service.

## Requirements
This code requires PHP 5.4 or greater. Older versions of PHP are not supported.

[bcmath](http://www.php.net/manual/en/intro.bc.php) is required for the MaxMind reader class.

## Dependencies
[MaxMind DB Reader PHP API](https://github.com/maxmind/MaxMind-DB-Reader-php)