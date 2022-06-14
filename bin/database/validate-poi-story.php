<?php
/**
 * This File includes all needed functions for poi_story_validate-table
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * adds new validation value to database for a given link between point of interest and story
 * @param int $story_poi_id id of link betweenm point of interest and story token
 * @param int $value value which is added to validation value of picture
 * @return bool|null state of request
 */
function insertValidatePoiStory($story_poi_id, $value)
{
    $stmt = 'INSERT INTO `' . config::$SQL_PREFIX . 'poi_story_validate` ( `story_poi_link_id` , `uid` , `value` ) values ( :spid , :uid , :val );';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $story_poi_id;
    $params[0]['nam'] = ":spid";
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
 * Deletes Validate-Entries for given link between point of interest and story
 * @param $story_poi_id int id of link between point of interest and story token
 * @return bool|null state of request
 */
function deleteValidatePoiStory($story_poi_id)
{
    $prep_stmt = "DELETE FROM `" . config::$SQL_PREFIX . "poi_story_validate` WHERE story_poi_link_id = :story_poi_id";
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $story_poi_id;
    $params[0]['nam'] = ":story_poi_id";
    dump($params, 8);
    $x = ExecuteStatementWR($prep_stmt, $params, false);
    return $x;
}

/**
 * Selects validation sum over all performed validation requests for a link between point of interest and story
 * @param int $story_poi_id id of link between point of interest and story token
 * @return int total validation sum
 */
function getValidateSumPoiStory($story_poi_id)
{
    $stmt = 'SELECT SUM(value) FROM  `' . config::$SQL_PREFIX . 'poi_story_validate` where story_poi_link_id = :story_poi_id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $story_poi_id;
    $params[0]['nam'] = ":story_poi_id";
    $result = ExecuteStatementWR($stmt, $params);
    return $result[0]['SUM(value)'] == null ? 0 : $result[0]['SUM(value)'];
}

/**
 * Gets data to all performed validation requests of links between point of interest and story
 * @return array Structured List of validation request
 */
function getAllValidatedForPoiStory()
{
    $prep_stmt = "SELECT * FROM `" . config::$SQL_PREFIX . "poi_story_validate` ;";
    $params = array();
    $result = ExecuteStatementWR($prep_stmt, $params);
    return $result;
}