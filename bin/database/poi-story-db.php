<?php
/**
 * This File includes all needed functions for poi_story-table
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * inserts poi story linking into database
 * @param int $poi_id id of poi
 * @param string $storytoken token of story
 * @return bool|null state of success
 */
function insertPoiStory($poi_id, $storytoken)
{
    $stmt = 'INSERT INTO ' . config::$SQL_PREFIX . 'poi_story (creator, poi_id, story_token) values (:creator , :poi_id , :story_token);';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = getUserData($_SESSION['username'])['id'];
    $params[0]['nam'] = ":creator";
    $params[1] = array();
    $params[1]['typ'] = 'i';
    $params[1]['val'] = $poi_id;
    $params[1]['nam'] = ":poi_id";
    $params[2] = array();
    $params[2]['typ'] = 's';
    $params[2]['val'] = $storytoken;
    $params[2]['nam'] = ":story_token";
    $result = ExecuteStatementWR($stmt, $params, false);
    return $result;
}

/**
 * gets list of poi names related to links
 * @param string $token token of selected story
 * @param bool $override overrides default behaviour
 * @param bool $api must only be set to true if caller is part of an api
 * @return array with poi Information
 */
function getPoiForStory($token, $override = false, $api = false)
{
    $stmt = 'select S.id, P.name , P.poi_id, S.deleted, P.lat, P.lng from ' . config::$SQL_PREFIX . 'poi_story as S join ' . config::$SQL_PREFIX . 'pois as P on S.poi_id = P.poi_id where S.story_token = :story_token and deleted = 0 ;';
    $allowed = false;
    if (!$api) {
        $allowed = $_SESSION['role'] >= config::$ROLE_ADMIN;
    }
    if ($api || $override || $allowed) {
        $stmt = 'select S.id, P.name , P.poi_id, S.deleted, P.lat, P.lng from ' . config::$SQL_PREFIX . 'poi_story as S join ' . config::$SQL_PREFIX . 'pois as P on S.poi_id = P.poi_id where S.story_token = :story_token ;';
    }
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $token;
    $params[0]['nam'] = ":story_token";
    $result = ExecuteStatementWR($stmt, $params);
    return $result;
}

/**
 * gets list of story token related to links
 * @param int $poi_id id of selected poi
 * @param bool $override overrides default behaviur and gives all stories back independent of deletion state
 * @return array with poi Information
 */
function getPoiForStoryByPoiId($poi_id, $override = false)
{
    $stmt = 'select S.id, S.story_token as token, S.deleted from ' . config::$SQL_PREFIX . 'poi_story as S join ' . config::$SQL_PREFIX . 'pois as P on S.poi_id = P.poi_id where S.poi_id = :poi_id and deleted = 0 ;';
    if (($_SESSION['role'] >= config::$ROLE_ADMIN) || $override) {
        $stmt = 'select S.id, S.story_token as token, S.deleted from ' . config::$SQL_PREFIX . 'poi_story as S join ' . config::$SQL_PREFIX . 'pois as P on S.poi_id = P.poi_id where S.poi_id = :poi_id;';
    }
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $poi_id;
    $params[0]['nam'] = ":poi_id";
    $result = ExecuteStatementWR($stmt, $params);
    return $result;
}

/**
 * get creator of historical address
 * @param int $story_poi_id id of link between point of interest and story token
 * @return string creator of historical address
 */
function getCreatorByPoiStoryId($story_poi_id)
{
    $stmt = 'select S.id, U.name as Username from `' . config::$SQL_PREFIX . 'poi_story` as S join `' . config::$SQL_PREFIX . 'user-login` as U on S.creator = U.id where S.id = :id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $story_poi_id;
    $params[0]['nam'] = ":id";
    $result = ExecuteStatementWR($stmt, $params)[0];
    return $result['Username'];
}

/**
 * deletes Link between story and POI
 * @param int $story_poi_id ID of Link
 * @return bool|null state of success
 */
function deletePoiStory($story_poi_id)
{
    $prep_stmt = "DELETE FROM `" . config::$SQL_PREFIX . "poi_story` WHERE id = :id";
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $story_poi_id;
    $params[0]['nam'] = ":id";
    dump($params, 8);
    $x = ExecuteStatementWR($prep_stmt, $params, false);
    return $x;
}

/**
 * updates deletion state of link between poi and story by link id
 * @param int $id Identifier of Link
 * @param bool $state true if it should be marked as deleted
 * @return array|bool|null result
 */
function updateDeletionStateLinkPoiStoryByID($id, $state)
{
    if ((LinkPoiStoryRestictedStory($id) || LinkPoiStoryRestictedPOI($id)) && $state == false) {
        return false;
    }
    $val = $state ? 1 : 0;
    $prep_stmt = 'Update `' . config::$SQL_PREFIX . 'poi_story` SET `deleted`  = :deleted where `id` = :id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $val;
    $params[0]['nam'] = ":deleted";
    $params[1] = array();
    $params[1]['typ'] = 's';
    $params[1]['val'] = $id;
    $params[1]['nam'] = ":id";
    $result = ExecuteStatementWR($prep_stmt, $params, false, true);
    return $result;
}

/**
 * updates reason for  deletion state of link between poi and story by link id
 * @param int $id Identifier of Link
 * @param bool $state true if it should be marked as deleted through poi deletion
 * @return array|bool|null result
 */
function updateDeletionPoiStateLinkPoiStoryByID($id, $state)
{
    $val = $state ? 1 : 0;
    $prep_stmt = 'Update `' . config::$SQL_PREFIX . 'poi_story` SET `poiDel`  = :deleted where `id` = :id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $val;
    $params[0]['nam'] = ":deleted";
    $params[1] = array();
    $params[1]['typ'] = 's';
    $params[1]['val'] = $id;
    $params[1]['nam'] = ":id";
    $result = ExecuteStatementWR($prep_stmt, $params, false, true);
    return $result;
}

/**
 * updates reason for deletion state of link between poi and story by link id
 * @param int $id Identifier of Link
 * @param bool $state true if it should be marked as deleted through story deletion
 * @return array|bool|null result
 */
function updateDeletionStoryStateLinkPoiStoryByID($id, $state)
{
    $val = $state ? 1 : 0;
    $prep_stmt = 'Update `' . config::$SQL_PREFIX . 'poi_story` SET `storyDel`  = :deleted where `id` = :id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $val;
    $params[0]['nam'] = ":deleted";
    $params[1] = array();
    $params[1]['typ'] = 's';
    $params[1]['val'] = $id;
    $params[1]['nam'] = ":id";
    $result = ExecuteStatementWR($prep_stmt, $params, false, true);
    return $result;
}

/**
 * checks if depending poi is mark as deleted
 * @param int $id identifier of link
 * @return bool true if restriction is existent
 */
function LinkPoiStoryRestictedPOI($id)
{
    $stmt = 'select poiDel from `' . config::$SQL_PREFIX . 'poi_story` where id = :id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $id;
    $params[0]['nam'] = ":id";
    $result = ExecuteStatementWR($stmt, $params)[0];
    return $result['poiDel'] == 1;
}

/**
 * checks if depending story is mark as deleted
 * @param int $id identifier of link
 * @return bool true if restriction is existent
 */
function LinkPoiStoryRestictedStory($id)
{
    $stmt = 'select storyDel from `' . config::$SQL_PREFIX . 'poi_story` where id = :id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $id;
    $params[0]['nam'] = ":id";
    $result = ExecuteStatementWR($stmt, $params)[0];
    return $result['storyDel'] == 1;
}