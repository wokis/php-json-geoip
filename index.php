<?php
/**
 * This product uses GeoLite2 data created by MaxMind, available from
 * http://www.maxmind.com
 */

header("Content-Type: application/json; charset=utf-8");

/**
 * Load the MaxMind reader class for their proprietary database format
 *
 */
const MAX_DB_FILENAME = 'GeoLite2-City.mmdb';

require_once 'MaxMind/Db/Reader.php';
require_once 'MaxMind/Db/Reader/Decoder.php';
require_once 'MaxMind/Db/Reader/InvalidDatabaseException.php';
require_once 'MaxMind/Db/Reader/Metadata.php';
use MaxMind\Db\Reader;

/**
 * Indents a flat JSON string to make it more human-readable.
 *
 * @param string $json The original JSON string to process.
 * @return string Indented version of the original JSON string.
 */
function indentJson($json) {

    $result      = '';
    $pos         = 0;
    $strLen      = strlen($json);
    $indentStr   = '  ';
    $newLine     = "\n";
    $prevChar    = '';
    $outOfQuotes = true;

    for ($i=0; $i<=$strLen; $i++) {

        // Grab the next character in the string.
        $char = substr($json, $i, 1);

        // Are we inside a quoted string?
        if ($char == '"' && $prevChar != '\\') {
            $outOfQuotes = !$outOfQuotes;

        // If this character is the end of an element,
        // output a new line and indent the next line.
        } else if(($char == '}' || $char == ']') && $outOfQuotes) {
            $result .= $newLine;
            $pos --;
            for ($j=0; $j<$pos; $j++) {
                $result .= $indentStr;
            }
        }

        // Add the character to the result string.
        $result .= $char;

        // If the last character was the beginning of an element,
        // output a new line and indent the next line.
        if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
            $result .= $newLine;
            if ($char == '{' || $char == '[') {
                $pos ++;
            }

            for ($j = 0; $j < $pos; $j++) {
                $result .= $indentStr;
            }
        }

        $prevChar = $char;
    }

    return $result;
}

/**
 * Helper for replacing unicode sequences
 *
 * \u00e1 will become á and so on
 *
 * @param string $json Raw JSON encoded string
 * @return string JSON with removed unicode sequences
 */
function jsonRemoveUnicodeSequences($json) {
   return preg_replace("/\\\\u([a-f0-9]{4})/e", "iconv('UCS-4LE','UTF-8',pack('V', hexdec('U$1')))", $json);
}


/**
 * Helper for backporting some JSON functions to PHP < 5.4
 *
 * @param array $json Array to encode into JSON
 * @return string JSON formated string
 */
function jsonBackportEncode($json) {
    if (version_compare(phpversion(), '5.4', '>=')) {
        return json_encode($json, JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
    else {
        return indentJson(jsonRemoveUnicodeSequences(json_encode($json, JSON_NUMERIC_CHECK)));
    }
}

/**
 * Helper for formating error messages and http codes
 *
 * @param int $httpCode Http status code
 * @param string $msg Error message to return
 * @return string JSON formated error message
 */
function jsonError($httpCode, $msg) {
    // http_response_code is not available in PHP < 5.4
    header(' ', true, $httpCode);
    
    $error = array('success' => false, 'message' => $msg);
    echo jsonBackportEncode($error);
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
    
    echo jsonBackportEncode($results);
    
} catch(Exception $e) {
    jsonError(500, $e->getMessage());
}
