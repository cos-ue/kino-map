<?php
/**
 * This File includes all needed functions for comments-table
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * Inserts new written comment into database
 * @param array $data all needed data to insert comment for a point of interest
 * @return bool|null On success there will be true returned
 */
function insertComment($data)
{
    if ($data['comment'] == "") {
        Redirect("../map.php");
    }
    $stmt = 'INSERT INTO ' . config::$SQL_PREFIX . 'comments (user_id, poi_id, content) values (:user_id , :poi_id , :content);';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = getUserData($_SESSION['username'])['id'];
    $params[0]['nam'] = ":user_id";
    $params[1] = array();
    $params[1]['typ'] = 'i';
    $params[1]['val'] = $data['poi_id'];
    $params[1]['nam'] = ":poi_id";
    $params[2] = array();
    $params[2]['typ'] = 's';
    $params[2]['val'] = $data['comment'];
    $params[2]['nam'] = ":content";
    $result = ExecuteStatementWR($stmt, $params, false);
    return $result;
}

/**
 * Selects comment for a given point of interest
 * @param int $poi_id id of given point of interest
 * @return array Structured array with all needed data to display comments for a given point of interest
 */
function selectComments($poi_id)
{
    $stmt = 'SELECT C.comment_id, C.content, C.timestamp, U.name, C.deleted FROM ' . config::$SQL_PREFIX . 'comments as C, `' .
        config::$SQL_PREFIX . 'user-login` as U, ' . config::$SQL_PREFIX . 'pois as P WHERE C.poi_id = :poiid AND P.poi_id = :poid AND U.id = C.user_id and C.deleted = 0 ; ';
    if ($_SESSION['role'] >= config::$ROLE_ADMIN){
        $stmt = 'SELECT C.comment_id, C.content, C.timestamp, U.name, C.deleted FROM ' . config::$SQL_PREFIX . 'comments as C, `' .
            config::$SQL_PREFIX . 'user-login` as U, ' . config::$SQL_PREFIX . 'pois as P WHERE C.poi_id = :poiid AND P.poi_id = :poid AND U.id = C.user_id;';
    }
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $poi_id;
    $params[0]['nam'] = ":poiid";
    $params[1] = array();
    $params[1]['typ'] = 'i';
    $params[1]['val'] = $poi_id;
    $params[1]['nam'] = ":poid";
    $result = ExecuteStatementWR($stmt, $params);
    return $result;
}

/**
 * Deletes a selected comment
 * @param int $id id of comment which should be deleted
 * @return bool|null On success there will be true returned
 */
function deleteComment($id)
{
    $stmt = 'DELETE FROM ' . config::$SQL_PREFIX . 'comments WHERE comment_id = :cid ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $id;
    $params[0]['nam'] = ":cid";
    return ExecuteStatementWR($stmt, $params, false);
}

/**
 * Function deletes all comments which are related to a given POI.
 * @param int $poiid ID of given POI
 * @return bool result of prepared sql statement
 */
function deleteCommentByPOI($poiid)
{
    $stmt = 'DELETE FROM ' . config::$SQL_PREFIX . 'comments WHERE poi_id = :poiid ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $poiid;
    $params[0]['nam'] = ":poiid";
    return ExecuteStatementWR($stmt, $params, false);
}

/**
 * Selects data for a comment by given ID
 * @param int $commentID ID of given Comment
 * @return array Result of select-statement
 */
function selectCommentsByCommentID($commentID)
{
    $stmt = 'SELECT C.comment_id, C.content, C.timestamp, U.name FROM ' . config::$SQL_PREFIX . 'comments as C, `' .
        config::$SQL_PREFIX . 'user-login` as U WHERE C.comment_id = :cid AND U.id = C.user_id;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $commentID;
    $params[0]['nam'] = ":cid";
    $result = ExecuteStatementWR($stmt, $params)[0];
    return $result;
}

/**
 * Function saves an edited Comment to Database, original one is not used available anymore
 * @param int $commentID Comment which should be changed
 * @param string $commentContent new Content of Comment
 * @return bool|null true if request was sucessfully handled
 */
function saveEditedCommentWithID($commentID, $commentContent)
{
    $stmt = 'UPDATE `' . config::$SQL_PREFIX . 'comments` SET `content` = :content , `timestamp` = CURRENT_TIME where comment_id = :cid ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $commentID;
    $params[0]['nam'] = ":cid";
    $params[1] = array();
    $params[1]['typ'] = 'i';
    $params[1]['val'] = $commentContent;
    $params[1]['nam'] = ":content";
    $result = ExecuteStatementWR($stmt, $params, false);
    return $result;
}

/**
 * selects all comments written by given user
 * @param string $username username which comments should be collected
 * @return array Structured result which contains all comments written by given user
 */
function getAllCommentsOfUser($username)
{
    $prep_stmt = "select `comment_id`, `timestamp`, content, " . config::$SQL_PREFIX . "comments.deleted as deleted, " . config::$SQL_PREFIX . "pois.name as poiname, " . config::$SQL_PREFIX . "pois.poi_id as poi_id, lat, lng from " . config::$SQL_PREFIX . "comments join `" . config::$SQL_PREFIX . "user-login` on  " . config::$SQL_PREFIX . "comments.user_id = `" . config::$SQL_PREFIX . "user-login`.id join " . config::$SQL_PREFIX . "pois on " . config::$SQL_PREFIX . "pois.poi_id = " . config::$SQL_PREFIX . "comments.poi_id where `" . config::$SQL_PREFIX . "user-login`.name = :name and " . config::$SQL_PREFIX . "comments.deleted = 0 ;";
    if ($_SESSION['role'] >= config::$ROLE_ADMIN){
        $prep_stmt = "select `comment_id`, `timestamp`, content, " . config::$SQL_PREFIX . "comments.deleted as deleted, " . config::$SQL_PREFIX . "pois.name as poiname, " . config::$SQL_PREFIX . "pois.poi_id as poi_id, lat, lng from " . config::$SQL_PREFIX . "comments join `" . config::$SQL_PREFIX . "user-login` on  " . config::$SQL_PREFIX . "comments.user_id = `" . config::$SQL_PREFIX . "user-login`.id join " . config::$SQL_PREFIX . "pois on " . config::$SQL_PREFIX . "pois.poi_id = " . config::$SQL_PREFIX . "comments.poi_id where `" . config::$SQL_PREFIX . "user-login`.name = :name ;";
    }
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $username;
    $params[0]['nam'] = ":name";
    dump($params, 8);
    return ExecuteStatementWR($prep_stmt, $params);
}

/**
 * updates deletion state of comment of point of interest by poiid
 * @param int $poiid Identifier of point of interest
 * @param bool $state true if it should be marked as deleted
 * @return array|bool|null result
 */
function updateDeletionStateCommentByPoiid($poiid, $state)
{
    $val = $state ? 1 : 0;
    $prep_stmt = 'Update `' . config::$SQL_PREFIX . 'comments` SET `deleted`  = :deleted where `poi_id` = :POIID ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $val;
    $params[0]['nam'] = ":deleted";
    $params[1] = array();
    $params[1]['typ'] = 's';
    $params[1]['val'] = $poiid;
    $params[1]['nam'] = ":POIID";
    $result = ExecuteStatementWR($prep_stmt, $params, false, true);
    return $result;
}

/**
 * updates deletion state of comment of point of interest by comment id
 * @param int $id Identifier of Comment
 * @param bool $state true if it should be marked as deleted
 * @return array|bool|null result
 */
function updateDeletionStateCommentById($id, $state)
{
    $val = $state ? 1 : 0;
    $prep_stmt = 'Update `' . config::$SQL_PREFIX . 'comments` SET `deleted`  = :deleted where `comment_id` = :cid ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $val;
    $params[0]['nam'] = ":deleted";
    $params[1] = array();
    $params[1]['typ'] = 's';
    $params[1]['val'] = $id;
    $params[1]['nam'] = ":cid";
    $result = ExecuteStatementWR($prep_stmt, $params, false, true);
    return $result;
}