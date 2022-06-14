<?php
/**
 * This File includes all needed functions for cinemas-table
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * Inserts new cinema count into database for a certain POI
 * @param int $poi_id id of point of interest
 * @param int $start start of naming
 * @param int $end end of naming
 * @param int $cinema_count count of cinemas in poi
 * @return bool|null state of request
 */
function insertCinemasOfPOI($poi_id, $start, $end, $cinema_count)
{
    $stmt = 'INSERT INTO `' . config::$SQL_PREFIX . 'cinemas` ( `POI_ID` , `start` , `end` , `cinemas` , `creator` ) values ( :pid , :start , :end , :cinemas , :creator );';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $poi_id;
    $params[0]['nam'] = ":pid";
    $params[1] = array();
    $params[1]['typ'] = 'i';
    $params[1]['val'] = $start;
    $params[1]['nam'] = ":start";
    $params[2] = array();
    $params[2]['typ'] = 's';
    $params[2]['val'] = $end;
    $params[2]['nam'] = ":end";
    $params[3] = array();
    $params[3]['typ'] = 's';
    $params[3]['val'] = $cinema_count;
    $params[3]['nam'] = ":cinemas";
    $params[4] = array();
    $params[4]['typ'] = 's';
    $params[4]['val'] = getUserData($_SESSION['username'])['id'];
    $params[4]['nam'] = ":creator";
    $result = ExecuteStatementWR($stmt, $params, false);
    return $result;
}

/**
 * get's all cinema count for a Point of interest
 * @param int $poiid id of point of interest
 * @return array|bool|null Result of select-statement
 */
function getCinemasByPoiId($poiid)
{
    $stmt = 'select N.ID, N.cinemas, N.start, N.end, N.deleted from `' . config::$SQL_PREFIX . 'cinemas` as N join `' . config::$SQL_PREFIX . 'user-login` as U on N.creator = U.id where N.POI_ID = :poi_id and deleted = 0 ;';
    if ($_SESSION['role'] >= config::$ROLE_ADMIN){
        $stmt = 'select N.ID, N.cinemas, N.start, N.end, N.deleted from `' . config::$SQL_PREFIX . 'cinemas` as N join `' . config::$SQL_PREFIX . 'user-login` as U on N.creator = U.id where N.POI_ID = :poi_id ;';
    }
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $poiid;
    $params[0]['nam'] = ":poi_id";
    $result = ExecuteStatementWR($stmt, $params);
    return $result;
}

/**
 * selects creator of cinemas
 * @param int $cinemas_id id of cinemas entry
 * @return string name of creator
 */
function getCreatorByCinemasID($cinemas_id)
{
    $stmt = 'select O.ID, U.name as Username from `' . config::$SQL_PREFIX . 'cinemas` as O join `' . config::$SQL_PREFIX . 'user-login` as U on O.creator = U.id where O.ID = :id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $cinemas_id;
    $params[0]['nam'] = ":id";
    $result = ExecuteStatementWR($stmt, $params)[0];
    return $result['Username'];
}

/** deletes given cinema count
 * @param $cinemas_id int ID of given cinema count
 * @return bool|null state of request
 */
function deleteCinemas($cinemas_id)
{
    $prep_stmt = "DELETE FROM `" . config::$SQL_PREFIX . "cinemas` WHERE ID = :cinema_id";
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $cinemas_id;
    $params[0]['nam'] = ":cinema_id";
    dump($params, 8);
    $x = ExecuteStatementWR($prep_stmt, $params, false);
    return $x;
}

/**
 * Updates entry of certain cinemas entry
 * @param $id int ID of cinemas entry
 * @param $cinemas int count of cinemas of poi
 * @param $start int start year at which cinemas count time started
 * @param $end int end year at which cinemas count time ended
 * @return array|bool|null
 */
function updateCinemas($id, $cinemas, $start, $end)
{
    $prep_stmt = 'Update `' . config::$SQL_PREFIX . 'cinemas` SET cinemas  = :cinemas  , start = :start , end = :end , creator = :creator , creationdate =  CURRENT_TIMESTAMP where ID = :ID ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $cinemas;
    $params[0]['nam'] = ":cinemas";
    $params[1] = array();
    $params[1]['typ'] = 's';
    $params[1]['val'] = $start;
    $params[1]['nam'] = ":start";
    $params[2] = array();
    $params[2]['typ'] = 's';
    $params[2]['val'] = $end;
    $params[2]['nam'] = ":end";
    $params[3] = array();
    $params[3]['typ'] = 's';
    $params[3]['val'] = getUserData($_SESSION['username'])['id'];
    $params[3]['nam'] = ":creator";
    $params[4] = array();
    $params[4]['typ'] = 's';
    $params[4]['val'] = $id;
    $params[4]['nam'] = ":ID";
    $result = ExecuteStatementWR($prep_stmt, $params, false);
    return $result;
}

/**
 * updates deletion state of Cinema count by poiid
 * @param int $id Identifier of point of interest
 * @param bool $state true if it should be marked as deleted
 * @return array|bool|null result
 */
function updateDeletionStateCinemasById($id, $state)
{
    $val = $state ? 1 : 0;
    $prep_stmt = 'Update `' . config::$SQL_PREFIX . 'cinemas` SET `deleted`  = :deleted where `ID` = :ID ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $val;
    $params[0]['nam'] = ":deleted";
    $params[1] = array();
    $params[1]['typ'] = 's';
    $params[1]['val'] = $id;
    $params[1]['nam'] = ":ID";
    $result = ExecuteStatementWR($prep_stmt, $params, false, true);
    return $result;
}