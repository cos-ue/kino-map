<?php
/**
 * This File includes all needed functions for poi_pictures_validate-table
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * inserts new validate into database
 * @param int $pic_poi_id id of pic and poi
 * @param int $value validation value
 * @return bool|null result of request
 */
function insertValidatePoiPicLink($pic_poi_id, $value)
{
    $stmt = 'INSERT INTO `' . config::$SQL_PREFIX . 'poi_pictures_validate` ( `link-id-poi-pic` , `creator` , `value` ) values ( :lip , :uid , :val );';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $pic_poi_id;
    $params[0]['nam'] = ":lip";
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
 * gets sum of validations for certain id of pic and poi link
 * @param int $poi_pic_id id of link
 */
function getValidateSumPoiPic($poi_pic_id)
{
    $stmt = 'SELECT SUM(value) FROM  `' . config::$SQL_PREFIX . 'poi_pictures_validate` where `link-id-poi-pic` = :link ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $poi_pic_id;
    $params[0]['nam'] = ":link";
    $result = ExecuteStatementWR($stmt, $params);
    return $result[0]['SUM(value)'] == null ? 0 : $result[0]['SUM(value)'];
}

/**
 * Gets data to all performed validation requests of poi picture links
 * @param int $lid Link id of poi pic link
 * @return array Structured List of validation request
 */
function getAllValidatedForPoiPicLink($lid)
{
    $prep_stmt = "SELECT * FROM `" . config::$SQL_PREFIX . "poi_pictures_validate` as P join `" . config::$SQL_PREFIX . "user-login` as U on P.creator = U.id where `link-id-poi-pic` = :link ;";
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $lid;
    $params[0]['nam'] = ":link";
    $result = ExecuteStatementWR($prep_stmt, $params);
    return $result;
}

/**
 * deletes Validations for a given link between picture and poi
 * @param int $lid link id
 * @return bool|null state of request
 */
function deleteValidationsForCertainPoiPicLink($lid)
{
    $prep_stmt = "DELETE FROM `" . config::$SQL_PREFIX . "poi_pictures_validate` WHERE `link-id-poi-pic` = :link";
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $lid;
    $params[0]['nam'] = ":link";
    dump($params, 8);
    $x = ExecuteStatementWR($prep_stmt, $params, false);
    return $x;
}