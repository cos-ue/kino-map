<?php
/**
 * This File includes all needed functions for operator_validate-table
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * adds new validation value to database for a given operator of a point of interest
 * @param int $operator_id id of point of interest
 * @param int $value value which is added to validation value of picture
 * @return bool|null state of request
 */
function insertValidateOperator($operator_id, $value)
{
    $stmt = 'INSERT INTO `' . config::$SQL_PREFIX . 'operator_validate` ( `operator_id` , `uid` , `value` ) values ( :pid , :uid , :val );';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $operator_id;
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
 * Selects validation sum over all performed validation requests for a given cinema name
 * @param int $operator_id id of given cinema name
 * @return int total validation sum
 */
function getValidateSumOperator($operator_id)
{
    $stmt = 'SELECT SUM(value) FROM  `' . config::$SQL_PREFIX . 'operator_validate` where operator_id = :operator_id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $operator_id;
    $params[0]['nam'] = ":operator_id";
    $result = ExecuteStatementWR($stmt, $params);
    return $result[0]['SUM(value)'] == null ? 0 : $result[0]['SUM(value)'];
}

/**
 * Deletes Validate-Entries for given operator
 * @param int $operator_id id of given operator
 * @return bool|null state of request
 */
function deleteValidateOperator($operator_id)
{
    $prep_stmt = "DELETE FROM `" . config::$SQL_PREFIX . "operator_validate` WHERE operator_id = :operator_id";
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $operator_id;
    $params[0]['nam'] = ":operator_id";
    dump($params, 8);
    $x = ExecuteStatementWR($prep_stmt, $params, false);
    return $x;
}

/**
 * Gets data to all performed validation requests of operators
 * @return array Structured List of validation request
 */
function getAllValidatedForPoiOperators()
{
    $prep_stmt = "SELECT * FROM `" . config::$SQL_PREFIX . "operator_validate` ;";
    $params = array();
    $result = ExecuteStatementWR($prep_stmt, $params);
    return $result;
}
