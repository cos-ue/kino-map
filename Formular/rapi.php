<?php
/**
 * API endpoint for COSP-Reverse-API functions
 *
 * @package default
 */

/**
 * @const enables loading of other files without dying to improve security
 */
define('NICE_PROJECT', true);
require_once "../bin/inc-sub.php";
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] != 'POST' && $_SERVER['REQUEST_METHOD'] != 'PUT') {
    permissionDenied();
}
if (key_exists("CONTENT_TYPE", $_SERVER) === false) {
    permissionDenied();
}
if ($_SERVER["CONTENT_TYPE"] !== "application/json" && strpos("multipart/form-data",$_SERVER["CONTENT_TYPE"]) ) { //multipart/form-data
    permissionDenied();
}
$input = file_get_contents('php://input');
$json = decode_json($input);
if ($json === null)
{
    $json = array();
    foreach (array_keys($_POST) as $key)
    {
        $json[$key] = filter_input(INPUT_POST, $key);
    }
}
if (key_exists('token', $json) == false) {
    permissionDenied();
}
if (checkApiToken($json['token']) === false) {
    permissionDenied();
}
switch ($json['type']) {
    case 'dau':  //deaktivate user
        if (key_exists('username', $json)) {
            generateJson(AktivateUserRapi($json, false));
        }
        break;
    case 'rau': //reaktivate User
        if (key_exists('username', $json)) {
            generateJson(AktivateUserRapi($json, true));
        }
        break;
    case 'rpt': //remove Picture Token
        $result = removePictureTokenRevApi($json);
        generateJson($result);
        break;
    case 'dus': //delete User Story
        $result = deleteStoryReference($json);
        generateJson($result);
        break;
    case 'rst': //restore Story Links
        $result = restoreStoryRapi($json);
        generateJson($result);
        break;
    case 'rpc': //restore Picture Links
        $result = restorePictureRapi($json);
        generateJson($result);
        break;
}