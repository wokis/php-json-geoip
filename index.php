<?php
require 'vendor/autoload.php';

use MaxMind\Db\Reader;

/**
 * This product uses GeoLite2 data created by MaxMind, available from
 * http://www.maxmind.com
 */

const MAX_DB_FILENAME = 'GeoLite2-City.mmdb';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

/**
 * Options for json_encode
 *
 * JSON_UNESCAPED_UNICODE, JSON_PRETTY_PRINT 
 *  and JSON_UNESCAPED_SLASHES are not available 
 *  in PHP < 5.4
 */
define('JSON_OPTIONS', JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

/**
 * Helper for formating error messages and http codes
 *
 * @param int $httpCode Http status code
 * @param string $msg Error message to return
 * @return string JSON formated error message
 */
function jsonError($httpCode, $msg)
{
    // http_response_code is not available in PHP < 5.4
    http_response_code($httpCode);
    
    $error = array('success' => false, 'message' => $msg);
    echo json_encode($error, JSON_OPTIONS);
    return;
}

/**
 * Main application code
 *
 * Get IP, validate, open the GeoLite database and return the results as JSON
 */
$ip = (isset($_GET['ip'])) ? $_GET['ip'] : $_SERVER['REMOTE_ADDR'];

if (!filter_var($ip, FILTER_VALIDATE_IP)) {
    jsonError(400, 'Invalid IP address. The request could not be understood by the server due to malformed syntax.');
    die();
}

try {
    $reader = new Reader(MAX_DB_FILENAME);
    $results = $reader->get($ip);

    if (empty($results)) {
        jsonError(404, "No match found for \"$ip\". The server has not found anything matching the Request-URI.");
        die();
    }
    
    echo json_encode($results, JSON_OPTIONS);
    
} catch (Exception $e) {
    jsonError(500, $e->getMessage());
}
