<?php
/**
 * This File includes all needed functions for history_validate-table
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * adds new validation value to database for a given history of a point of interest
 * @param int $poi_id id of point of interest
 * @param int $value value which is added to validation value of picture
 * @return bool|null state of request
 */
function insertValidateHistory($poi_id, $value)
{
    $stmt = 'INSERT INTO `' . config::$SQL_PREFIX . 'history_validate` ( `poi_id` , `uid` , `value` ) values ( :pid , :uid , :val );';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $poi_id;
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
 * Deletes Validate-Entries for given history
 * @param $poi_id int id of poi for given history
 * @return bool|null state of request
 */
function deleteValidateHistory($poi_id)
{
    $prep_stmt = "DELETE FROM `" . config::$SQL_PREFIX . "history_validate` WHERE poi_id = :poi_id";
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $poi_id;
    $params[0]['nam'] = ":poi_id";
    dump($params, 8);
    $x = ExecuteStatementWR($prep_stmt, $params, false);
    return $x;
}

/**
 * Selects validation sum over all performed validation requests for a given history
 * @param int $poi_id id of given history
 * @return int total validation sum
 */
function getValidateSumHist($poi_id)
{
    $stmt = 'SELECT SUM(value) FROM  `' . config::$SQL_PREFIX . 'history_validate` where poi_id = :poi_id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $poi_id;
    $params[0]['nam'] = ":poi_id";
    $result = ExecuteStatementWR($stmt, $params);
    return $result[0]['SUM(value)'] == null ? 0 : $result[0]['SUM(value)'];
}

/**
 * Gets data to all performed validation requests of history
 * @return array Structured List of validation request
 */
function getAllValidatedForHistory()
{
    $prep_stmt = "SELECT * FROM `" . config::$SQL_PREFIX . "history_validate` ;";
    $params = array();
    $result = ExecuteStatementWR($prep_stmt, $params);
    return $result;
}