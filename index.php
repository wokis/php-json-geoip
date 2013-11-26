<?php
/**
 * This product uses GeoLite2 data created by MaxMind, available from
 * http://www.maxmind.com
 */

header("Content-Type: application/json; charset=utf-8");

/**
 * Options for json_encode
 *
 * JSON_UNESCAPED_UNICODE and JSON_PRETTY_PRINT 
 *  are not available in PHP < 5.4
 */
define("JSON_OPTIONS", JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

/**
 * Load the MaxMind reader class for their proprietary database format
 */
const MAX_DB_FILENAME = 'GeoLite2-City.mmdb';

require_once 'MaxMind/Db/Reader.php';
require_once 'MaxMind/Db/Reader/Decoder.php';
require_once 'MaxMind/Db/Reader/InvalidDatabaseException.php';
require_once 'MaxMind/Db/Reader/Metadata.php';
use MaxMind\Db\Reader;

/**
 * Helper for formating error messages and http codes
 *
 * @param int $httpCode Http status code
 * @param string $msg Error message to return
 * @return string JSON formated error message
 */
function jsonError($httpCode, $msg) {
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
if (isset($_GET['ip'])) $ip = $_GET['ip'];
else $ip = $_SERVER['REMOTE_ADDR'];

if(!filter_var($ip, FILTER_VALIDATE_IP)) {
    jsonError(400, 'Invalid IP address. The request could not be understood by the server due to malformed syntax.');
    die();
}

try {
    $reader = new Reader(MAX_DB_FILENAME);
    $results = $reader->get($ip);

    if (empty($results)) {
        jsonError(404, 'No match found for "' .$ip. '". The server has not found anything matching the Request-URI.');
        die();
    }
    
    echo json_encode($results, JSON_OPTIONS);
    
} catch(Exception $e) {
    jsonError(500, $e->getMessage());
}
