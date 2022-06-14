<?php
/**
 * This File includes a basic functions used for many statistical functions
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * executes statistical statement
 * @param string $statement code of statement
 * @param int $amount amount which should be applied to prepared statement
 * @return array structured result data
 */
function ExecuteStatisticStatement($statement, $amount){
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $amount;
    $params[0]['nam'] = ":number";
    $result = ExecuteStatementWR($statement, $params);
    return $result;
}