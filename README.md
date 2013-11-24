php-json-geoip
==============

REST API for MaxMind GeoLite2 using PHP and JSON.

The MaxMind PHP reader class can be found at https://github.com/maxmind/MaxMind-DB-Reader-php

Note that the reader class requires [bcmath](http://www.php.net/manual/en/intro.bc.php), something I have commented out in my pull due to lack of bcmath support on my server. There are also some functions, related to JSON encoding, one could do without if the PHP version is 5.4 or greater.

The database itself can be found at http://dev.maxmind.com/geoip/geoip2/geolite2/

### Online API

Online API access can be found at 

    http://api.kacper.se/geoip/{ip}

{ip} should be replaced with the actual IP address to geolocate. Any valid IPv4 or IPv6 address will work. RFC 1918 and RFC 3927 addresses will return no match found.

No match found returns http status code 404.
Invalid IP address returns http status code 400.

Example

    http://api.kacper.se/geoip/8.8.8.8
	or
	http://api.kacper.se/geoip/2001:4860:4860::8888
	
At this time there is no request limit in place, please don't abuse this service.