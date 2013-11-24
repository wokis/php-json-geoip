php-json-geoip
==============

REST API for MaxMind GeoLite2 using PHP and JSON

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