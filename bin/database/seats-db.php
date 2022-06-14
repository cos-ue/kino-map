<?php
/**
 * This File includes all needed functions for seats-table
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * Inserts new Seat count into database for a certain POI
 * @param int $poi_id id of point of interest
 * @param int $start start of naming
 * @param int $end end of naming
 * @param int $seatcount count of seats in poi
 * @return bool|null state of request
 */
function insertSeatsOfPOI($poi_id, $start, $end, $seatcount)
{
    $stmt = 'INSERT INTO `' . config::$SQL_PREFIX . 'seats` ( `POI_ID` , `start` , `end` , `seats` , `creator` ) values ( :pid , :start , :end , :seats , :creator );';
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
    $params[3]['val'] = $seatcount;
    $params[3]['nam'] = ":seats";
    $params[4] = array();
    $params[4]['typ'] = 's';
    $params[4]['val'] = getUserData($_SESSION['username'])['id'];
    $params[4]['nam'] = ":creator";
    $result = ExecuteStatementWR($stmt, $params, false);
    return $result;
}

/**
 * get's all Names for a Point of interest
 * @param int $poiid id of point of interest
 * @return array|bool|null Result of select-statement
 */
function getSeatsByPoiId($poiid)
{
    $stmt = 'select N.ID, N.seats, N.start, N.end, N.deleted from `' . config::$SQL_PREFIX . 'seats` as N join `' . config::$SQL_PREFIX . 'user-login` as U on N.creator = U.id where N.POI_ID = :poi_id and deleted = 0 ;';
    if ($_SESSION['role'] >= config::$ROLE_ADMIN){
        $stmt = 'select N.ID, N.seats, N.start, N.end, N.deleted from `' . config::$SQL_PREFIX . 'seats` as N join `' . config::$SQL_PREFIX . 'user-login` as U on N.creator = U.id where N.POI_ID = :poi_id ;';
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
 * selects creator of Seats
 * @param int $seat_id id of seats entry
 * @return string name of creator
 */
function getCreatorBySeatsID($seat_id)
{
    $stmt = 'select O.ID, U.name as Username from `' . config::$SQL_PREFIX . 'seats` as O join `' . config::$SQL_PREFIX . 'user-login` as U on O.creator = U.id where O.ID = :id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $seat_id;
    $params[0]['nam'] = ":id";
    $result = ExecuteStatementWR($stmt, $params)[0];
    return $result['Username'];
}

/** deletes given Seat coun
 * @param $seat_id int ID of given seat count
 * @return bool|null state of request
 */
function deleteSeats($seat_id)
{
    $prep_stmt = "DELETE FROM `" . config::$SQL_PREFIX . "seats` WHERE ID = :seats_id";
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $seat_id;
    $params[0]['nam'] = ":seats_id";
    dump($params, 8);
    $x = ExecuteStatementWR($prep_stmt, $params, false);
    return $x;
}

/**
 * Updates entry of certain seats entry
 * @param $id int ID of seats entry
 * @param $seatCount int count of seats of poi
 * @param $start int start year at which seats count time started
 * @param $end int end year at which seats count time ended
 * @return array|bool|null
 */
function updateSeats($id, $seatCount, $start, $end)
{
    $prep_stmt = 'Update `' . config::$SQL_PREFIX . 'seats` SET seats  = :seats  , start = :start , end = :end , creator = :creator , creationdate =  CURRENT_TIMESTAMP where ID = :ID ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $seatCount;
    $params[0]['nam'] = ":seats";
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
 * updates deletion state of seat count by id
 * @param int $id Identifier of seat count
 * @param bool $state true if it should be marked as deleted
 * @return array|bool|null result
 */
function updateDeletionStateSeatsById($id, $state)
{
    $val = $state ? 1 : 0;
    $prep_stmt = 'Update `' . config::$SQL_PREFIX . 'seats` SET `deleted`  = :deleted where `ID` = :ID ;';
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