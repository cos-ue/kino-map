<?php

/**
 * This File includes all needed functions for statistics over validated poi
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * @return array structured result
 */
function getStatisticsValidatedPoiData()
{
    $stmt = 'Select ( SELECT count(*) from `' . config::$SQL_PREFIX . 'Statistics_Count_validated` ) AS Total, ( SELECT count(*) from `' . config::$SQL_PREFIX . 'Statistics_Count_validated` where validateVal >= 400 ) AS Validated, ( SELECT count(*) from `' . config::$SQL_PREFIX . 'Statistics_Count_validated` where validateVal < 400  and validateVal > 0 ) as PartValidated, ( SELECT count(*) from `' . config::$SQL_PREFIX . 'Statistics_Count_validated` where validateVal <= 0 or validateVal IS NULL ) as unvalidated;';
    $params = array();
    $result = ExecuteStatementWR($stmt, $params);
    return $result[0];
}