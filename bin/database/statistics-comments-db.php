<?php
/**
 * This File includes a statistical functions for comments.
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * get's statistical data for displaying as graph over the given amount of past weeks starting today
 * @param int $number number of weeks
 * @return array structured result
 */
function getCommentsCreateStatisticalDataLastWeeks($number)
{
    $stmt = "select Count(timestamp) as counter, DATE_FORMAT(`timestamp`,'%Y-%m-%d') as time from `" . config::$SQL_PREFIX . "comments` where timestamp >= DATE(NOW()) - INTERVAL :number WEEK group by `time` ;";
    return ExecuteStatisticStatement($stmt, $number);
}

/**
 * get's statistical data for displaying as graph over the given amount of past month starting today
 * @param int $number number of month
 * @return array structured result
 */
function getCommentsCreateStatisticalDataLastMonth($number)
{
    $stmt = "select Count(timestamp) as counter, DATE_FORMAT(`timestamp`,'%Y-%m-%d') as time from `" . config::$SQL_PREFIX . "comments` where timestamp >= DATE(NOW()) - INTERVAL :number MONTH group by `time` ;";
    return ExecuteStatisticStatement($stmt, $number);
}

/**
 * get's statistical data for displaying as graph over the given amount of past year starting today
 * @param int $number number of years
 * @return array structured result
 */
function getCommentsCreateStatisticalDataLastYear($number)
{
    $stmt = "select Count(timestamp) as counter, DATE_FORMAT(`timestamp`,'%Y-%m-%d') as time from `" . config::$SQL_PREFIX . "comments` where timestamp >= DATE(NOW()) - INTERVAL :number YEAR group by `time` ;";
    return ExecuteStatisticStatement($stmt, $number);
}

/**
 * get's statistical data for displaying as graph over the given amount of past days starting today
 * @param int $number number of days
 * @return array structured result
 */
function getCommentsCreateStatisticalDataLastDays($number)
{
    $stmt = "select Count(timestamp) as counter, DATE_FORMAT(`timestamp`,'%Y-%m-%d') as time from `" . config::$SQL_PREFIX . "comments` where timestamp >= DATE(NOW()) - INTERVAL :number DAY group by `time` ;";
    return ExecuteStatisticStatement($stmt, $number);
}