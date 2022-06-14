<?php
/**
 * This File includes all needed functions for cinema_types-table
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * Gets data to all cinema types with id and name
 * @return array Structured List of results
 */
function getAllCinemaTypes()
{
    $prep_stmt = "SELECT * FROM `" . config::$SQL_PREFIX . "cinema_types` ;";
    $params = array();
    $result = ExecuteStatementWR($prep_stmt, $params);
    return $result;
}

/**
 * gets name for a given cinema type
 * @param int $id Cinematype id
 * @return string name
 */
function getCinemaTypeNameByTypeId($id)
{
    $stmt = 'SELECT name FROM  `' . config::$SQL_PREFIX . 'cinema_types` where `id` = :id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $id;
    $params[0]['nam'] = ":id";
    $result = ExecuteStatementWR($stmt, $params);
    return $result[0]['name'];
}