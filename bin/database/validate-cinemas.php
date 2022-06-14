<?php
/**
 * This File includes all needed functions for cinemas_validate-table
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * Selects validation sum over all performed validation requests for a given cinemas dataset
 * @param int $cinemas_id id of given cinemas dataset
 * @return int total validation sum
 */
function getValidateSumCinemas($cinemas_id)
{
    $stmt = 'SELECT SUM(value) FROM  `' . config::$SQL_PREFIX . 'cinemas_validate` where cinemas_id = :cinemas_id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $cinemas_id;
    $params[0]['nam'] = ":cinemas_id";
    $result = ExecuteStatementWR($stmt, $params);
    return $result[0]['SUM(value)'] == null ? 0 : $result[0]['SUM(value)'];
}

/**
 * Gets data to all performed validation requests of cinemas
 * @return array Structured List of validation request
 */
function getAllValidatedForPoiCinemas()
{
    $prep_stmt = "SELECT * FROM `" . config::$SQL_PREFIX . "cinemas_validate` ;";
    $params = array();
    $result = ExecuteStatementWR($prep_stmt, $params);
    return $result;
}

/**
 * adds new validation value to database for a given cinema count of a point of interest
 * @param int $cinema_id id of cinema of point of interest
 * @param int $value value which is added to validation value of picture
 * @return bool|null state of request
 */
function insertValidateCinemas($cinema_id, $value)
{
    $stmt = 'INSERT INTO `' . config::$SQL_PREFIX . 'cinemas_validate` ( `cinemas_id` , `uid` , `value` ) values ( :pid , :uid , :val );';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $cinema_id;
    $params[0]['nam'] = ":pid";
    $params[1] = array();
    $params[1]['typ'] = 'i';
    $params[1]['val'] = getUserData($_SESSION['username'])['id'];
    $params[1]['nam'] = ":uid";
    $params[2] = array();
    $params[2]['typ'] = 's';
    $params[2]['val'] = $value;
    $params[2]['nam'] = ":val";
    $result = ExecuteStatementWR($stmt, $params, false);
    return $result;
}

/**
 * Deletes Validate-Entries for given cinema count
 * @param $cinema_id int id of given cinema count
 * @return bool|null state of request
 */
function deleteValidateCinema($cinema_id)
{
    $prep_stmt = "DELETE FROM `" . config::$SQL_PREFIX . "cinemas_validate` WHERE cinemas_id = :cinemas_id";
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $cinema_id;
    $params[0]['nam'] = ":cinemas_id";
    dump($params, 8);
    $x = ExecuteStatementWR($prep_stmt, $params, false);
    return $x;
}