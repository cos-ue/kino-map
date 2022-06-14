<?php
/**
 * This File includes all needed functions for source poi table
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * adds new source to point of interest
 * @param int $type identifier of type of source
 * @param string $source source
 * @param int $relation identifier of relation of source to data
 * @param int $poiid identifier of point of interest
 * @return array|void|null structured result
 */
function insertSourceOfPOI($type, $source, $relation, $poiid)
{
    $stmt = 'INSERT INTO `' . config::$SQL_PREFIX . 'poi_sources` ( `poiid` , `source` , `typeid` , `relationid` , `creator` ) values ( :poiid , :source , :typeid , :relationid , :creator );';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $poiid;
    $params[0]['nam'] = ":poiid";
    $params[1] = array();
    $params[1]['typ'] = 'i';
    $params[1]['val'] = $source;
    $params[1]['nam'] = ":source";
    $params[2] = array();
    $params[2]['typ'] = 's';
    $params[2]['val'] = $type;
    $params[2]['nam'] = ":typeid";
    $params[3] = array();
    $params[3]['typ'] = 's';
    $params[3]['val'] = $relation;
    $params[3]['nam'] = ":relationid";
    $params[4] = array();
    $params[4]['typ'] = 's';
    $params[4]['val'] = getUserData($_SESSION['username'])['id'];
    $params[4]['nam'] = ":creator";
    $result = ExecuteStatementWR($stmt, $params, false);
    return $result;
}

/**
 * gets all source information for certain point of interest
 * @param int $poiid identifier of point of interest
 * @param bool $overwrite overwrites default behaviour
 * @return array structured result
 */
function getSourceOfPoi($poiid, $overwrite = false)
{
    $stmt = 'Select S.id, S.source, typeid, relationid, R.name as relation, T.name as type, creator, deleted, U.name as username from `' . config::$SQL_PREFIX . 'poi_sources` as S join `' . config::$SQL_PREFIX . 'source_relation` as R on S.relationid = R.id join `' . config::$SQL_PREFIX . 'source_type` as T on T.id = S.typeid join `kino__user-login` as U on S.creator = U.id where S.poiid = :poiid and S.deleted = 0 ;';
    if ($_SESSION['role'] >= config::$ROLE_ADMIN || $overwrite) {
        $stmt = 'Select S.id, S.source, typeid, relationid, R.name as relation, T.name as type, creator, deleted, U.name as username from `' . config::$SQL_PREFIX . 'poi_sources` as S join `' . config::$SQL_PREFIX . 'source_relation` as R on S.relationid = R.id join `' . config::$SQL_PREFIX . 'source_type` as T on T.id = S.typeid join `kino__user-login` as U on S.creator = U.id where S.poiid = :poiid ;';
    }
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $poiid;
    $params[0]['nam'] = ":poiid";
    $result = ExecuteStatementWR($stmt, $params);
    return $result;
}

/**
 * updates certain source
 * @param int $id identifier of source
 * @param int $relation identifier of a relation
 * @param string $source description of source
 * @param int $type identifier of a type
 * @return array|bool|null structured result
 */
function updateSource($id, $relation, $source, $type)
{
    $stmt = 'UPDATE `' . config::$SQL_PREFIX . 'poi_sources` SET `source` = :source , `typeid` = :typeid , `relationid` = :relationid where `id` = :id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $source;
    $params[0]['nam'] = ":source";
    $params[1] = array();
    $params[1]['typ'] = 'i';
    $params[1]['val'] = $type;
    $params[1]['nam'] = ":typeid";
    $params[2] = array();
    $params[2]['typ'] = 'i';
    $params[2]['val'] = $relation;
    $params[2]['nam'] = ":relationid";
    $params[3] = array();
    $params[3]['typ'] = 'i';
    $params[3]['val'] = $id;
    $params[3]['nam'] = ":id";
    $result = ExecuteStatementWR($stmt, $params, false);
    return $result;
}

/**
 * gets information for a certain source
 * @param int $id identifier of source
 * @return array structured result
 */
function getSource($id)
{
    $stmt = 'Select S.id, S.source, typeid, relationid, R.name as relation, T.name as type, creator, deleted, U.name as username from `' . config::$SQL_PREFIX . 'poi_sources` as S join `' . config::$SQL_PREFIX . 'source_relation` as R on S.relationid = R.id join `' . config::$SQL_PREFIX . 'source_type` as T on T.id = S.typeid join `kino__user-login` as U on S.creator = U.id where S.id = :id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $id;
    $params[0]['nam'] = ":id";
    $result = ExecuteStatementWR($stmt, $params);
    if (count($result) > 0) {
        $result = $result[0];
    }
    return $result;
}

/**
 * updates deletion state of source
 * @param int $id identifier of source
 * @param bool $state state of deletion
 * @return array|bool|null structured result
 */
function updateSourceDeletionState($id, $state)
{
    $val = $state ? 1 : 0;
    $stmt = 'UPDATE `' . config::$SQL_PREFIX . 'poi_sources` SET `deleted` = :deleted where `id` = :id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $val;
    $params[0]['nam'] = ":deleted";
    $params[1] = array();
    $params[1]['typ'] = 'i';
    $params[1]['val'] = $id;
    $params[1]['nam'] = ":id";
    $result = ExecuteStatementWR($stmt, $params, false, true);
    return $result;
}

/**
 * deletes a certain source
 * @param int $id identifier of source
 * @return array|bool|null structured result
 */
function deleteSource($id)
{
    $stmt = 'DELETE FROM ' . config::$SQL_PREFIX . 'poi_sources WHERE id = :id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $id;
    $params[0]['nam'] = ":id";
    return ExecuteStatementWR($stmt, $params, false);
}