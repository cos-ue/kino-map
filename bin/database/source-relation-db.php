<?php
/**
 * This File includes all needed functions for source relation table
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * selects all relations for relations for sources
 * @return array structured result
 */
function getAllSourceRelations() {
    $prep_stmt = "SELECT *  FROM `" . config::$SQL_PREFIX . "source_relation`;";
    $params = array();
    $result = ExecuteStatementWR($prep_stmt, $params);
    return $result;
}