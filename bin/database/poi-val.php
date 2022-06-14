<?php
/**
 * This File includes all needed functions for validate-table
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * Gets data to all performed validation requests of POI
 * @return array Structured List of validation request
 */
function getAllValidatedForPOI()
{
    $prep_stmt = "SELECT * FROM `" . config::$SQL_PREFIX . "validate` ;";
    $params = array();
    $result = ExecuteStatementWR($prep_stmt, $params);
    return $result;
}

/**
 * Inserts an validation request into database
 * @param int $poiid id of point of interest which should be validated
 * @param int $value vale which represents the users power of validationg an point of interest
 * @return bool|null On success there will be true returned
 */
function insertValidateForPOI($poiid, $value)
{
    $stmt = 'INSERT INTO `' . config::$SQL_PREFIX . 'validate` ( poi_id , uid , `value` ) values ( :poi_id , :uid , :value );';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $poiid;
    $params[0]['nam'] = ":poi_id";
    $params[1] = array();
    $params[1]['typ'] = 'i';
    $params[1]['val'] = getUserData($_SESSION['username'])['id'];
    $params[1]['nam'] = ":uid";
    $params[2] = array();
    $params[2]['typ'] = 's';
    $params[2]['val'] = $value;
    $params[2]['nam'] = ":value";
    $result = ExecuteStatementWR($stmt, $params, false);
    return $result;
}

/**
 * deletes Validation data for given poi
 * @param int $poiid Id of given poi
 * @return bool result of prepared sql-statement
 */
function deletevalidateByPOI($poiid)
{
    $stmt = 'DELETE FROM ' . config::$SQL_PREFIX . 'validate WHERE poi_id = :poiid ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $poiid;
    $params[0]['nam'] = ":poiid";
    return ExecuteStatementWR($stmt, $params, false);
}

/**
 * Selects validation sum over all performed validation requests for a given point of interest
 * @param int $poiid id of given point of interest
 * @return int total validation sum
 */
function getValidateSumForPOI($poiid)
{
    $stmt = 'SELECT SUM(value) FROM  `' . config::$SQL_PREFIX . 'validate` where poi_id = :poi_id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $poiid;
    $params[0]['nam'] = ":poi_id";
    $result = ExecuteStatementWR($stmt, $params);
    return $result[0]['SUM(value)'] == null ? 0 : $result[0]['SUM(value)'];
}