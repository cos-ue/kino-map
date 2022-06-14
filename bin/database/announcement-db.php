<?php
/**
 * This File includes all needed functions for announcement-table
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * adds new announcement to database
 * @param string $title Title of announcement
 * @param string $content Content for announcement modal
 * @param DateTime $start day at which modal is going to be displayed
 * @param DateTime $end day at which modal isn't displayed anymore
 * @return array|void|null structured result
 */
function addAnnouncement($title, $content, $start, $end)
{
    $stmt = 'INSERT INTO `' . config::$SQL_PREFIX . 'announcement` ( `title` , `content` , `start` , `end` , `creator` ) values ( :title , :content , :start , :end , :creator );';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $title;
    $params[0]['nam'] = ":title";
    $params[1] = array();
    $params[1]['typ'] = 'i';
    $params[1]['val'] = $content;
    $params[1]['nam'] = ":content";
    $params[2] = array();
    $params[2]['typ'] = 's';
    $params[2]['val'] = $start;
    $params[2]['nam'] = ":start";
    $params[3] = array();
    $params[3]['typ'] = 's';
    $params[3]['val'] = $end;
    $params[3]['nam'] = ":end";
    $params[4] = array();
    $params[4]['typ'] = 's';
    $params[4]['val'] = getUserData($_SESSION['username'])['id'];
    $params[4]['nam'] = ":creator";
    $result = ExecuteStatementWR($stmt, $params, false);
    return $result;
}

/**
 * selects all announcements
 * @return array structured result
 */
function getAllAnnouncements() {
    $prep_stmt = "SELECT A.id, A.title, A.content, A.start, A.end, U.name as creator, A.creator as uid, A.enable  FROM `" . config::$SQL_PREFIX . "announcement` as A join `" . config::$SQL_PREFIX . "user-login` as U on A.creator = U.id;";
    $params = array();
    $result = ExecuteStatementWR($prep_stmt, $params);
    return $result;
}

/**
 * selects all announcements
 * @param int $id Identifier of announcement
 * @return array structured result
 */
function getAnnouncement($id) {
    $prep_stmt = "SELECT A.id, A.title, A.content, A.start, A.end, U.name as creator, A.creator as uid, A.enable  FROM `" . config::$SQL_PREFIX . "announcement` as A join `" . config::$SQL_PREFIX . "user-login` as U on A.creator = U.id where A.id = :id ;";
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $id;
    $params[0]['nam'] = ":id";
    $result = ExecuteStatementWR($prep_stmt, $params)[0];
    return $result;
}

/**
 * updates a certain announcement
 * @param int $id identifier of announcement
 * @param string $title title of announcement
 * @param string $content content of announcement
 * @param string $start startdate of announcement
 * @param string $end enddate of announcement
 * @return array|bool|null structured result
 */
function updateAnnouncement($id, $title, $content, $start, $end) {
    $stmt = 'UPDATE `' . config::$SQL_PREFIX . 'announcement` SET title = :title , content = :content , start = :start , end = :end , creator = :creator where id = :id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $title;
    $params[0]['nam'] = ":title";
    $params[1] = array();
    $params[1]['typ'] = 'i';
    $params[1]['val'] = $content;
    $params[1]['nam'] = ":content";
    $params[2] = array();
    $params[2]['typ'] = 's';
    $params[2]['val'] = $start;
    $params[2]['nam'] = ":start";
    $params[3] = array();
    $params[3]['typ'] = 's';
    $params[3]['val'] = $end;
    $params[3]['nam'] = ":end";
    $params[4] = array();
    $params[4]['typ'] = 's';
    $params[4]['val'] = getUserData($_SESSION['username'])['id'];
    $params[4]['nam'] = ":creator";
    $params[5] = array();
    $params[5]['typ'] = 'i';
    $params[5]['val'] = $id;
    $params[5]['nam'] = ":id";
    $result = ExecuteStatementWR($stmt, $params, false);
    return $result;
}

/** deletes given announcement
 * @param int $id identifier of announcement
 * @return bool|null state of request
 */
function deleteAnnouncement($id)
{
    $prep_stmt = "DELETE FROM `" . config::$SQL_PREFIX . "announcement` WHERE `id` = :id";
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $id;
    $params[0]['nam'] = ":id";
    dump($params, 8);
    $x = ExecuteStatementWR($prep_stmt, $params, false);
    return $x;
}

/**
 * get current announcements
 * @return array|bool|null structured result;
 */
function getCurrentAnnouncement() {
    $prep_stmt = "SELECT id, title, content, `end` FROM `" . config::$SQL_PREFIX . "announcement` where `start` <= CURRENT_DATE and `end` >= CURRENT_DATE and enable = 1;";
    $params = array();
    dump($params, 8);
    $x = ExecuteStatementWR($prep_stmt, $params);
    return $x;
}

/**
 * updates activation state of announcement
 * @param int $id identifier of announcement
 * @param bool $state state of activation
 * @return array|bool|null structured result
 */
function updateAktivationStateAnnouncement($id, $state) {
    $stmt = 'UPDATE `' . config::$SQL_PREFIX . 'announcement` SET enable = :enable where id = :id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $state ? 1 : 0;
    $params[0]['nam'] = ":enable";
    $params[1] = array();
    $params[1]['typ'] = 'i';
    $params[1]['val'] = $id;
    $params[1]['nam'] = ":id";
    $result = ExecuteStatementWR($stmt, $params, false, true);
    return $result;
}