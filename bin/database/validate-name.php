<?php
/**
 * This File includes all needed functions for name_validate-table
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * adds new validation value to database for a given name of a point of interest
 * @param int $name_id id of name
 * @param int $value value which is added to validation value of picture
 * @return bool|null state of request
 */
function insertValidateName($name_id, $value)
{
    $stmt = 'INSERT INTO `' . config::$SQL_PREFIX . 'name_validate` ( `name_id` , `uid` , `value` ) values ( :pid , :uid , :val );';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $name_id;
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
 * @param int $name_id id of given cinema name
 * @return int total validation sum
 */
function getValidateSumName($name_id)
{
    $stmt = 'SELECT SUM(value) FROM  `' . config::$SQL_PREFIX . 'name_validate` where name_id = :name_id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $name_id;
    $params[0]['nam'] = ":name_id";
    $result = ExecuteStatementWR($stmt, $params);
    return $result[0]['SUM(value)'] == null ? 0 : $result[0]['SUM(value)'];
}

/**
 * Deletes Validate-Entries for given name
 * @param int $name_id id of given name
 * @return bool|null state of request
 */
function deleteValidateName($name_id)
{
    $prep_stmt = "DELETE FROM `" . config::$SQL_PREFIX . "name_validate` WHERE name_id = :name_id";
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $name_id;
    $params[0]['nam'] = ":name_id";
    dump($params, 8);
    $x = ExecuteStatementWR($prep_stmt, $params, false);
    return $x;
}

/**
 * Gets data to all performed validation requests of names
 * @return array Structured List of validation request
 */
function getAllValidatedForPoiNames()
{
    $prep_stmt = "SELECT * FROM `" . config::$SQL_PREFIX . "name_validate` ;";
    $params = array();
    $result = ExecuteStatementWR($prep_stmt, $params);
    return $result;
}