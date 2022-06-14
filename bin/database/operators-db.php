<?php
/**
 * This File includes all needed functions for operators-table
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * get's all Operators for a Point of interest
 * @param int $poiid id of point of interest
 * @return array|bool|null Result of select-statement
 */
function getOpertorsByPoiId($poiid)
{
    $stmt = 'select O.ID, O.Operator, O.start, O.end, O.deleted from `' . config::$SQL_PREFIX . 'operators` as O join `' . config::$SQL_PREFIX . 'user-login` as U on O.creator = U.id where O.POI_ID = :poi_id and deleted = 0 ;';
    if ($_SESSION['role'] >= config::$ROLE_ADMIN){
        $stmt = 'select O.ID, O.Operator, O.start, O.end, O.deleted from `' . config::$SQL_PREFIX . 'operators` as O join `' . config::$SQL_PREFIX . 'user-login` as U on O.creator = U.id where O.POI_ID = :poi_id ;';
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
 * @param int $start start of operators operation
 * @param int $end end of operators operation
 * @param string $operator operator name
 * @return bool|null state of request
 */
function insertOperator($poi_id, $start, $end, $operator)
{
    $stmt = 'INSERT INTO `' . config::$SQL_PREFIX . 'operators` ( `POI_ID` , `start` , `end` , `Operator` , `creator` ) values ( :pid , :start , :end , :Operator , :creator );';
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
    $params[3]['val'] = $operator;
    $params[3]['nam'] = ":Operator";
    $params[4] = array();
    $params[4]['typ'] = 's';
    $params[4]['val'] = getUserData($_SESSION['username'])['id'];
    $params[4]['nam'] = ":creator";
    $result = ExecuteStatementWR($stmt, $params, false);
    return $result;
}

/**
 * selects creator of operator
 * @param int $operator_id id of operator entry
 * @return string name of creator
 */
function getCreatorByOperatorID($operator_id)
{
    $stmt = 'select O.ID, U.name as Username from `' . config::$SQL_PREFIX . 'operators` as O join `' . config::$SQL_PREFIX . 'user-login` as U on O.creator = U.id where O.ID = :id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $operator_id;
    $params[0]['nam'] = ":id";
    $result = ExecuteStatementWR($stmt, $params)[0];
    return $result['Username'];
}

/** deletes given Operator
 * @param $operator_id int ID of given operator
 * @return bool|null state of request
 */
function deleteOperator($operator_id)
{
    $prep_stmt = "DELETE FROM `" . config::$SQL_PREFIX . "operators` WHERE ID = :operator_id";
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $operator_id;
    $params[0]['nam'] = ":operator_id";
    dump($params, 8);
    $x = ExecuteStatementWR($prep_stmt, $params, false);
    return $x;
}

/**
 * Updates entry of certain operator
 * @param $id int ID of Operator
 * @param $operator string Name of operator
 * @param $start int start year at which operation time started
 * @param $end int end year at which operation time ended
 * @return array|bool|null
 */
function updateOperator($id, $operator, $start, $end)
{
    $prep_stmt = 'Update `' . config::$SQL_PREFIX . 'operators` SET Operator  = :Operator  , start = :start , end = :end , creator = :creator , creationdate =  CURRENT_TIMESTAMP where ID = :ID ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $operator;
    $params[0]['nam'] = ":Operator";
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
 * updates deletion state of operators by poiid
 * @param int $id Identifier of point of interest
 * @param bool $state true if it should be marked as deleted
 * @return array|bool|null result
 */
function updateDeletionStateOperatorsById($id, $state)
{
    $val = $state ? 1 : 0;
    $prep_stmt = 'Update `' . config::$SQL_PREFIX . 'operators` SET `deleted`  = :deleted where `ID` = :ID ;';
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