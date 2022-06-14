<?php
/**
 * This File includes all needed functions for seats_validate-table
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * Selects validation sum over all performed validation requests for a given seats dataset
 * @param int $seats_id id of given seats dataset
 * @return int total validation sum
 */
function getValidateSumSeats($seats_id)
{
    $stmt = 'SELECT SUM(value) FROM  `' . config::$SQL_PREFIX . 'seats_validate` where seats_id = :seats_id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $seats_id;
    $params[0]['nam'] = ":seats_id";
    $result = ExecuteStatementWR($stmt, $params);
    return $result[0]['SUM(value)'] == null ? 0 : $result[0]['SUM(value)'];
}

/**
 * Gets data to all performed validation requests of seats
 * @return array Structured List of validation request
 */
function getAllValidatedForPoiSeats()
{
    $prep_stmt = "SELECT * FROM `" . config::$SQL_PREFIX . "seats_validate` ;";
    $params = array();
    $result = ExecuteStatementWR($prep_stmt, $params);
    return $result;
}

/**
 * adds new validation value to database for a given seatscount of a point of interest
 * @param int $seat_id id of seats of point of interest
 * @param int $value value which is added to validation value of picture
 * @return bool|null state of request
 */
function insertValidateSeats($seat_id, $value)
{
    $stmt = 'INSERT INTO `' . config::$SQL_PREFIX . 'seats_validate` ( `seats_id` , `uid` , `value` ) values ( :pid , :uid , :val );';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $seat_id;
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
 * Deletes Validate-Entries for given seats count
 * @param $seat_id int id of given seats count
 * @return bool|null state of request
 */
function deleteValidateSeats($seat_id)
{
    $prep_stmt = "DELETE FROM `" . config::$SQL_PREFIX . "seats_validate` WHERE seats_id = :seats_id";
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $seat_id;
    $params[0]['nam'] = ":seats_id";
    dump($params, 8);
    $x = ExecuteStatementWR($prep_stmt, $params, false);
    return $x;
}