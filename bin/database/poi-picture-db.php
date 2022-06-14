<?php
/**
 * This File includes all needed functions for poi_pictures-table
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * writes picture poi mapping to database
 * @param string $pictureId Unique identifier of photo
 * @param int $PoiId id of poi
 * @return bool|null state of request
 */
function insertPoiPicture($pictureId, $PoiId)
{
    $stmt = 'INSERT INTO `' . config::$SQL_PREFIX . 'poi_pictures` (`picture_id`, `poi_id`, `creator`) values ( :picture_id , :poi_id , :creator );';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $pictureId;
    $params[0]['nam'] = ":picture_id";
    $params[1] = array();
    $params[1]['typ'] = 'i';
    $params[1]['val'] = $PoiId;
    $params[1]['nam'] = ":poi_id";
    $params[2] = array();
    $params[2]['typ'] = 'i';
    $params[2]['val'] = getUserData($_SESSION['username'])['id'];
    $params[2]['nam'] = ":creator";
    $result = ExecuteStatementWR($stmt, $params, false);
    return $result;
}

/**
 * gets pictures for certain poi
 * @param int $poiid poiid
 * @return array structured result
 */
function getPicturesForPoi($poiid)
{
    $stmt = 'select `picture_id` as pic, deleted from `' . config::$SQL_PREFIX . 'poi_pictures` where poi_id = :poi_id and deleted = 0 ;';
    if ($_SESSION['role'] >= config::$ROLE_ADMIN) {
        $stmt = 'select `picture_id` as pic, deleted from `' . config::$SQL_PREFIX . 'poi_pictures` where poi_id = :poi_id ;';
    }
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $poiid;
    $params[0]['nam'] = ":poi_id";
    $data = ExecuteStatementWR($stmt, $params);
    return $data;
}


/**
 * gets link of poi pic link
 * @param int $poiid id of poi
 * @param string $picid unique id of pic
 * @return int id of link
 */
function getLinkIdPoiPic($poiid, $picid)
{
    $stmt = 'select `id` as id from `' . config::$SQL_PREFIX . 'poi_pictures` where poi_id = :poi_id and `picture_id` = :picture_id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $poiid;
    $params[0]['nam'] = ":poi_id";
    $params[1] = array();
    $params[1]['typ'] = 'i';
    $params[1]['val'] = $picid;
    $params[1]['nam'] = ":picture_id";
    $result = ExecuteStatementWR($stmt, $params);
    return $result[0]['id'];
}

/**
 * gets links id of poi pic link
 * @param int $poiid id of poi
 * @return array id of links
 */
function getLinkIdsForPoi($poiid)
{
    $stmt = 'select `id` as id from `' . config::$SQL_PREFIX . 'poi_pictures` where poi_id = :poi_id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $poiid;
    $params[0]['nam'] = ":poi_id";
    $result = ExecuteStatementWR($stmt, $params);
    return $result;
}

/**
 * gets creator of certain pic an poi link
 * @param int $pic_poi_id id of link
 * @return string username
 */
function getCreatorByPoiPicId($pic_poi_id)
{
    $stmt = 'select S.id, U.name as Username from `' . config::$SQL_PREFIX . 'poi_pictures` as S join `' . config::$SQL_PREFIX . 'user-login` as U on S.creator = U.id where S.id = :id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $pic_poi_id;
    $params[0]['nam'] = ":id";
    $result = ExecuteStatementWR($stmt, $params)[0];
    return $result['Username'];
}

/**
 * deletes Link between picture and poi
 * @param int $lid link id
 * @return bool|null state of request
 */
function deleteCertainPoiPicLink($lid)
{
    $prep_stmt = "DELETE FROM `" . config::$SQL_PREFIX . "poi_pictures` WHERE `id`  = :id ;";
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $lid;
    $params[0]['nam'] = ":id";
    dump($params, 8);
    $x = ExecuteStatementWR($prep_stmt, $params, false);
    return $x;
}

/**
 * selects all pois for a certain pic
 * @param string $token unique identifier of pic
 * @return array structured result
 */
function getPoisForPic($token)
{
    $stmt = $stmt = 'select `poi_id` as poiid, deleted from `' . config::$SQL_PREFIX . 'poi_pictures` where `picture_id` = :picture_id and deleted = 0;';
    if ($_SESSION['role'] >= config::$ROLE_ADMIN) {
        $stmt = $stmt = 'select `poi_id` as poiid, deleted from `' . config::$SQL_PREFIX . 'poi_pictures` where `picture_id` = :picture_id ;';
    }
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $token;
    $params[0]['nam'] = ":picture_id";
    $result = ExecuteStatementWR($stmt, $params);
    return $result;
}

/**
 * gets link of poi pic links
 * @param string $picid unique id of pic
 * @return array id of link
 */
function getLinkIdsPoiPic($picid)
{
    $stmt = 'select `id` as id from `' . config::$SQL_PREFIX . 'poi_pictures` where `picture_id` = :picture_id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $picid;
    $params[0]['nam'] = ":picture_id";
    $result = ExecuteStatementWR($stmt, $params);
    return $result;
}

/**
 * updates deletion state of link between poi and picture by link id
 * @param int $id Identifier of Link
 * @param bool $state true if it should be marked as deleted
 * @return array|bool|null result
 */
function updateDeletionStateLinkPoiPicByID($id, $state)
{
    if ((LinkPoiPicRestictedPOI($id) || LinkPoiPicRestictedPic($id)) && $state == false) {
        return false;
    }
    $val = $state ? 1 : 0;
    $prep_stmt = 'Update `' . config::$SQL_PREFIX . 'poi_pictures` SET `deleted`  = :deleted where `id` = :id ;';
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
 * updates deletion state of link between poi and picture by link id
 * @param int $id Identifier of Link
 * @param bool $state true if it should be marked as deleted
 * @return array|bool|null result
 */
function updateDeletionPoiStateLinkPoiPicByID($id, $state)
{
    $val = $state ? 1 : 0;
    $prep_stmt = 'Update `' . config::$SQL_PREFIX . 'poi_pictures` SET `poiDel`  = :deleted where `id` = :id ;';
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
 * updates deletion state of link between poi and picture by link id
 * @param int $id Identifier of Link
 * @param bool $state true if it should be marked as deleted
 * @return array|bool|null result
 */
function updateDeletionPicStateLinkPoiPicByID($id, $state)
{
    $val = $state ? 1 : 0;
    $prep_stmt = 'Update `' . config::$SQL_PREFIX . 'poi_pictures` SET `picDel`  = :deleted where `id` = :id ;';
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
 * checks if depending poi is marked as deleted
 * @param int $id identifier of Link
 * @return bool true if there is a restriction
 */
function LinkPoiPicRestictedPOI($id)
{
    $stmt = 'select poiDel from `' . config::$SQL_PREFIX . 'poi_pictures` where `id` = :id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $id;
    $params[0]['nam'] = ":id";
    $result = ExecuteStatementWR($stmt, $params)[0];
    return $result['poiDel'] == 1;
}

/**
 * checks if depending pic is marked as deleted
 * @param int $id identifier of Link
 * @return bool true if there is a restriction
 */
function LinkPoiPicRestictedPic($id)
{
    $stmt = 'select picDel from `' . config::$SQL_PREFIX . 'poi_pictures` where `id` = :id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $id;
    $params[0]['nam'] = ":id";
    $result = ExecuteStatementWR($stmt, $params)[0];
    return $result['picDel'] == 1;
}