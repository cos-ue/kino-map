<?php
/**
 * This File includes all needed functions for names-table
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * get's all Names for a Point of interest
 * @param int $poiid id of point of interest
 * @return array|bool|null Result of select-statement
 */
function getNamesByPoiId($poiid)
{
    $stmt = 'select N.ID, N.name, N.start, N.end, N.deleted from `' . config::$SQL_PREFIX . 'names` as N join `' . config::$SQL_PREFIX . 'user-login` as U on N.creator = U.id where N.POI_ID = :poi_id and deleted = 0;';
    if ($_SESSION['role'] >= config::$ROLE_ADMIN){
        $stmt = 'select N.ID, N.name, N.start, N.end, N.deleted from `' . config::$SQL_PREFIX . 'names` as N join `' . config::$SQL_PREFIX . 'user-login` as U on N.creator = U.id where N.POI_ID = :poi_id ;';
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
 * Inserts new Operator into database for a certain POI
 * @param int $poi_id id of point of interest
 * @param int $start start of naming
 * @param int $end end of naming
 * @param string $name name
 * @return bool|null state of request
 */
function insertNameOfPOI($poi_id, $start, $end, $name)
{
    $stmt = 'INSERT INTO `' . config::$SQL_PREFIX . 'names` ( `POI_ID` , `start` , `end` , `Name` , `creator` ) values ( :pid , :start , :end , :Name , :creator );';
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
    $params[3]['val'] = $name;
    $params[3]['nam'] = ":Name";
    $params[4] = array();
    $params[4]['typ'] = 's';
    $params[4]['val'] = getUserData($_SESSION['username'])['id'];
    $params[4]['nam'] = ":creator";
    $result = ExecuteStatementWR($stmt, $params, false);
    return $result;
}

/**
 * selects creator of Name for POI
 * @param int $Nameid Id of name whichs creator should be requested
 * @return string Username of creator
 */
function getUsernameByNameId($Nameid)
{
    $stmt = 'select N.ID, U.name as Username from `' . config::$SQL_PREFIX . 'names` as N join `' . config::$SQL_PREFIX . 'user-login` as U on N.creator = U.id where N.ID = :id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $Nameid;
    $params[0]['nam'] = ":id";
    $result = ExecuteStatementWR($stmt, $params)[0];
    return $result['Username'];
}

/**
 * updates data for certain Name
 * @param int $id id of name
 * @param string $name name of poi
 * @param int $start start of naming
 * @param int $end end of naming
 * @return bool|null state of request
 */
function updateName($id, $name, $start, $end)
{
    $prep_stmt = 'Update `' . config::$SQL_PREFIX . 'names` SET Name  = :Name  , start = :start , end = :end , creator = :creator , creationdate =  CURRENT_TIMESTAMP where ID = :ID ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $name;
    $params[0]['nam'] = ":Name";
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

/** deletes given name
 * @param $name_id int ID of given name
 * @return bool|null state of request
 */
function deleteName($name_id)
{
    $prep_stmt = "DELETE FROM `" . config::$SQL_PREFIX . "names` WHERE ID = :name_id";
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $name_id;
    $params[0]['nam'] = ":name_id";
    dump($params, 8);
    $x = ExecuteStatementWR($prep_stmt, $params, false);
    return $x;
}

/**
 * updates deletion state of names by id
 * @param int $id Identifier of name
 * @param bool $state true if it should be marked as deleted
 * @return array|bool|null result
 */
function updateDeletionStateNamesByID($id, $state)
{
    $val = $state ? 1 : 0;
    $prep_stmt = 'Update `' . config::$SQL_PREFIX . 'names` SET `deleted`  = :deleted where `ID` = :ID ;';
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