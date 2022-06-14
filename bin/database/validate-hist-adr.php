<?php
/**
 * This File includes all needed functions for address_validate-table
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * adds new validation value to database for a given historical Address
 * @param int $address_id id of historical address
 * @param int $value value which is added to validation value of picture
 * @return bool|null state of request
 */
function insertValidateHistAddress($address_id, $value)
{
    $stmt = 'INSERT INTO `' . config::$SQL_PREFIX . 'address_validate` ( `address_id` , `uid` , `value` ) values ( :pid , :uid , :val );';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $address_id;
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
 * Selects validation sum over all performed validation requests for a given address
 * @param int $adress_id id of given address
 * @return int total validation sum
 */
function getValidateSumHistAddress($adress_id)
{
    $stmt = 'SELECT SUM(value) FROM  `' . config::$SQL_PREFIX . 'address_validate` where address_id = :address_id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $adress_id;
    $params[0]['nam'] = ":address_id";
    $result = ExecuteStatementWR($stmt, $params);
    return $result[0]['SUM(value)'] == null ? 0 : $result[0]['SUM(value)'];
}

/**
 * Deletes Validate-Entries for given historical address
 * @param $adress_id int id of given address
 * @return bool|null state of request
 */
function deleteValidateHistAddress($adress_id)
{
    $prep_stmt = "DELETE FROM `" . config::$SQL_PREFIX . "address_validate` WHERE address_id = :address_id";
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $adress_id;
    $params[0]['nam'] = ":address_id";
    dump($params, 8);
    $x = ExecuteStatementWR($prep_stmt, $params, false);
    return $x;
}

/**
 * Gets data to all performed validation requests of historical Addresses
 * @return array Structured List of validation request
 */
function getAllValidatedForPoiHistAddresses()
{
    $prep_stmt = "SELECT * FROM `" . config::$SQL_PREFIX . "address_validate` ;";
    $params = array();
    $result = ExecuteStatementWR($prep_stmt, $params);
    return $result;
}