<?php
/**
 * This File includes all needed functions for pois-table
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * inserts an point of interest into database
 * @param array $data Structured array which contains all needed information to insert point of interest
 * @return bool|null On success there will be true returned
 */
function insertPoi($data)
{
    $uid = getUserData($_SESSION['username'])['id'];
    $stmt = 'INSERT INTO `' . config::$SQL_PREFIX . 'pois` (`name`, `lng`, `lat`, `City`, `Postalcode`, `Streetname`, `Housenumber`, `picture`, `start`, `end`, `category`, `history`, `user_id` , `creator_timespan` , `creator_history` , `creator_currentAddress` , `creator_type` , `type` , `duty` ) values ( :name , :lng , :lat ,  :City , :Postalcode , :Streetname , :Housenumber , :picture , :start , :end , :category , :history , :user_id , :cts , :cth , :cta , :ctt , :type , :duty );';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $data['name'];
    $params[0]['nam'] = ":name";
    $params[1] = array();
    $params[1]['typ'] = 'd';
    $params[1]['val'] = $data['lng'];
    $params[1]['nam'] = ":lng";
    $params[2] = array();
    $params[2]['typ'] = 'd';
    $params[2]['val'] = $data['lat'];
    $params[2]['nam'] = ":lat";
    $params[3] = array();
    $params[3]['typ'] = 's';
    $params[3]['val'] = $data['city'];
    $params[3]['nam'] = ":City";
    $params[4] = array();
    $params[4]['typ'] = 'i';
    $params[4]['val'] = $data['postalcode'];
    $params[4]['nam'] = ":Postalcode";
    $params[5] = array();
    $params[5]['typ'] = 's';
    $params[5]['val'] = $data['streetname'];
    $params[5]['nam'] = ":Streetname";
    $params[6] = array();
    $params[6]['typ'] = 's';
    $params[6]['val'] = $data['housenumber'];
    $params[6]['nam'] = ":Housenumber";
    $params[7] = array();
    $params[7]['typ'] = 's';
    $params[7]['val'] = $data['picture'];
    $params[7]['nam'] = ":picture";
    $params[8] = array();
    $params[8]['typ'] = 's';
    $params[8]['val'] = $data['start'];
    $params[8]['nam'] = ":start";
    $params[9] = array();
    $params[9]['typ'] = 's';
    $params[9]['val'] = $data['end'];
    $params[9]['nam'] = ":end";
    $params[10] = array();
    $params[10]['typ'] = 's';
    $params[10]['val'] = $data['category'];
    $params[10]['nam'] = ":category";
    $params[11] = array();
    $params[11]['typ'] = 's';
    $params[11]['val'] = $data['history'];
    $params[11]['nam'] = ":history";
    $params[12] = array();
    $params[12]['typ'] = 'i';
    $params[12]['val'] = $uid;
    $params[12]['nam'] = ":user_id";
    $params[13] = array();
    $params[13]['typ'] = 'i';
    $params[13]['val'] = $uid;
    $params[13]['nam'] = ":cts";
    $params[14] = array();
    $params[14]['typ'] = 'i';
    $params[14]['val'] = $uid;
    $params[14]['nam'] = ":cth";
    $params[15] = array();
    $params[15]['typ'] = 'i';
    $params[15]['val'] = $uid;
    $params[15]['nam'] = ":cta";
    $params[16] = array();
    $params[16]['typ'] = 'i';
    $params[16]['val'] = $uid;
    $params[16]['nam'] = ":ctt";
    $params[17] = array();
    $params[17]['typ'] = 'i';
    $params[17]['val'] = $data['ctype'];
    $params[17]['nam'] = ":type";
    $params[18] = array();
    $params[18]['typ'] = 'i';
    $params[18]['val'] = $data['duty'] ? '1' : '0';
    $params[18]['nam'] = ":duty";

    if (!$stmt) {
        die('Fehler');
    } else {
        dump($params, 5);
        $result = ExecuteStatementWR($stmt, $params, false);
        return $result;
    }
}

/**
 * updates a given point of Interest
 * @param array $input Structured array which contains all needed information to update point of interest
 */
function updatePoi($input)
{
    $prep_stmt = 'UPDATE `' . config::$SQL_PREFIX . 'pois` SET `name` = :name , lng = :lng , lat = :lat , City = :City , Postalcode = :Postalcode , Streetname = :Streetname , Housenumber = :Housenumber , start = :start , `end` = :end , history = :history , type = :type , duty = :duty WHERE poi_id = :id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $input['name'];
    $params[0]['nam'] = ":name";
    $params[1] = array();
    $params[1]['typ'] = 'd';
    $params[1]['val'] = $input['lng'];
    $params[1]['nam'] = ":lng";
    $params[2] = array();
    $params[2]['typ'] = 'd';
    $params[2]['val'] = $input['lat'];
    $params[2]['nam'] = ":lat";
    $params[3] = array();
    $params[3]['typ'] = 's';
    $params[3]['val'] = $input['city'];
    $params[3]['nam'] = ":City";
    $params[4] = array();
    $params[4]['typ'] = 'i';
    $params[4]['val'] = $input['postalcode'];
    $params[4]['nam'] = ":Postalcode";
    $params[5] = array();
    $params[5]['typ'] = 's';
    $params[5]['val'] = $input['streetname'];
    $params[5]['nam'] = ":Streetname";
    $params[6] = array();
    $params[6]['typ'] = 's';
    $params[6]['val'] = $input['housenumber'];
    $params[6]['nam'] = ":Housenumber";
    $params[7] = array();
    $params[7]['typ'] = 's';
    $params[7]['val'] = $input['start'];
    $params[7]['nam'] = ":start";
    $params[8] = array();
    $params[8]['typ'] = 's';
    $params[8]['val'] = $input['end'];
    $params[8]['nam'] = ":end";
    $params[9] = array();
    $params[9]['typ'] = 's';
    $params[9]['val'] = $input['history'];
    $params[9]['nam'] = ":history";
    $params[10] = array();
    $params[10]['typ'] = 's';
    $params[10]['val'] = $input['id'];
    $params[10]['nam'] = ":id";
    $params[11] = array();
    $params[11]['typ'] = 's';
    $params[11]['val'] = $input['type'];
    $params[11]['nam'] = ":type";
    $params[12] = array();
    $params[12]['typ'] = 's';
    $params[12]['val'] = $input['duty'] ? '1' : '0';
    $params[12]['nam'] = ":duty";
    ExecuteStatementWR($prep_stmt, $params, false);
}

/**
 * updates a given point of Interests timespan creator
 * @param int $id id of poi
 */
function updatePoiCreatorTimespan($id)
{
    $prep_stmt = 'UPDATE `' . config::$SQL_PREFIX . 'pois` SET `creator_timespan` = :creator_timespan , `creationdate_timespan` = CURRENT_TIME WHERE poi_id = :id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = getUserData($_SESSION['username'])['id'];
    $params[0]['nam'] = ":creator_timespan";
    $params[1] = array();
    $params[1]['typ'] = 's';
    $params[1]['val'] = $id;
    $params[1]['nam'] = ":id";
    ExecuteStatementWR($prep_stmt, $params, false);
}

/**
 * updates a given point of Interests current address creator
 * @param int $id id of poi
 */
function updatePoiCreatorCurrentAddress($id)
{
    $prep_stmt = 'UPDATE `' . config::$SQL_PREFIX . 'pois` SET `creator_currentAddress` = :creator_timespan , `creationdate_currentAddress` = CURRENT_TIME WHERE poi_id = :id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = getUserData($_SESSION['username'])['id'];
    $params[0]['nam'] = ":creator_timespan";
    $params[1] = array();
    $params[1]['typ'] = 's';
    $params[1]['val'] = $id;
    $params[1]['nam'] = ":id";
    ExecuteStatementWR($prep_stmt, $params, false);
}

/**
 * updates a given point of Interests history creator
 * @param int $id id of poi
 */
function updatePoiCreatorHistory($id)
{
    $prep_stmt = 'UPDATE `' . config::$SQL_PREFIX . 'pois` SET `creator_history` = :creator_timespan , `creatoiondate_history` = CURRENT_TIME WHERE poi_id = :id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = getUserData($_SESSION['username'])['id'];
    $params[0]['nam'] = ":creator_timespan";
    $params[1] = array();
    $params[1]['typ'] = 's';
    $params[1]['val'] = $id;
    $params[1]['nam'] = ":id";
    ExecuteStatementWR($prep_stmt, $params, false);
}

/**
 * updates a given point of Interests history creator
 * @param int $id id of poi
 */
function updatePoiCreatorType($id)
{
    $prep_stmt = 'UPDATE `' . config::$SQL_PREFIX . 'pois` SET `creator_type` = :creator_type , `creationdate_type` = CURRENT_TIME WHERE poi_id = :id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = getUserData($_SESSION['username'])['id'];
    $params[0]['nam'] = ":creator_type";
    $params[1] = array();
    $params[1]['typ'] = 's';
    $params[1]['val'] = $id;
    $params[1]['nam'] = ":id";
    ExecuteStatementWR($prep_stmt, $params, false);
}

/**
 * updates a given point of Interests creator
 * @param int $id id of poi
 */
function updatePoiCreator($id)
{
    $prep_stmt = 'UPDATE `' . config::$SQL_PREFIX . 'pois` SET `user_id` = :creator_timespan , `creationDate` = CURRENT_TIME WHERE poi_id = :id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = getUserData($_SESSION['username'])['id'];
    $params[0]['nam'] = ":creator_timespan";
    $params[1] = array();
    $params[1]['typ'] = 's';
    $params[1]['val'] = $id;
    $params[1]['nam'] = ":id";
    ExecuteStatementWR($prep_stmt, $params, false);
}

/**
 * get information about all available point of interest out of Database
 * @param bool $api sets if caller is api
 * @return array List of all available point of interest
 */
function getAllPois($api = false)
{
    $prep_stmt = "SELECT " . config::$SQL_PREFIX . "pois.*, `" . config::$SQL_PREFIX . "user-login`.name as username FROM " . config::$SQL_PREFIX . "pois join `" . config::$SQL_PREFIX . "user-login` on " . config::$SQL_PREFIX . "pois.user_id = `" . config::$SQL_PREFIX . "user-login`.id where deleted = 0;";
    $allowed = false;
    if (!$api){
        if ($_SESSION['role'] >= config::$ROLE_ADMIN){
            $allowed = true;
        }
    }
    if ($api || $allowed){
        $prep_stmt = "SELECT " . config::$SQL_PREFIX . "pois.*, `" . config::$SQL_PREFIX . "user-login`.name as username FROM " . config::$SQL_PREFIX . "pois join `" . config::$SQL_PREFIX . "user-login` on " . config::$SQL_PREFIX . "pois.user_id = `" . config::$SQL_PREFIX . "user-login`.id ;";
    }
    $params = array();
    $result = ExecuteStatementWR($prep_stmt, $params);
    return $result;
}

/**
 * get title information about all available point of interest out of Database
 * @return array List of all available point of interest
 */
function getAllPoisTitle()
{
    $prep_stmt = "SELECT `poi_id`, `name`, deleted, lat, lng FROM `" . config::$SQL_PREFIX . "pois` where deleted = 0 ;";
    if (config::$ROLE_ADMIN){
        $prep_stmt = "SELECT `poi_id`, `name`, deleted, lat, lng FROM `" . config::$SQL_PREFIX . "pois`;";
    }
    $params = array();
    $result = ExecuteStatementWR($prep_stmt, $params);
    return $result;
}

/**
 * Selects data linked to a single point of interest
 * @param int $poiid ID of point of interest which should be selected
 * @return array data of selected point of interest
 */
function getPoi($poiid)
{
    $prep_stmt = "SELECT " . config::$SQL_PREFIX . "pois.*, `" . config::$SQL_PREFIX . "user-login`.name as username FROM " . config::$SQL_PREFIX . "pois join `" . config::$SQL_PREFIX . "user-login` on " . config::$SQL_PREFIX . "pois.user_id = `" . config::$SQL_PREFIX . "user-login`.id WHERE poi_id = :id and deleted = 0 ;";
    if ($_SESSION['role'] >= config::$ROLE_ADMIN){
        $prep_stmt = "SELECT " . config::$SQL_PREFIX . "pois.*, `" . config::$SQL_PREFIX . "user-login`.name as username FROM " . config::$SQL_PREFIX . "pois join `" . config::$SQL_PREFIX . "user-login` on " . config::$SQL_PREFIX . "pois.user_id = `" . config::$SQL_PREFIX . "user-login`.id WHERE poi_id = :id;";
    }
    $params = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $poiid;
    $params[0]['nam'] = ":id";
    $result = ExecuteStatementWR($prep_stmt, $params)[0];
    return $result;
}

/**
 * deletes given point of interest
 * @param int $poiid ID of point of interest which should be deleted
 * @return bool|null On success there will be true returned
 */
function deletePoi($poiid)
{
    $prep_stmt = "DELETE FROM `" . config::$SQL_PREFIX . "pois` WHERE poi_id = :id";
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $poiid;
    $params[0]['nam'] = ":id";
    dump($params, 8);
    $x = ExecuteStatementWR($prep_stmt, $params, false);
    return $x;
}

/**
 * selects information needed to display 'select more' section on map
 * @param int $poi_id id of selected poi
 * @return array returns all accessible data of point of interest
 */
function selectMore($poi_id)
{
    $stmt = 'SELECT P.poi_id, P.name as poi_name, P.City, P.Postalcode, P.Streetname, P.Housenumber, P.picture, P.start, P.end, P.category,
            P.history, P.type, U.name as user_name, P.deletedPic, P.deleted, P.duty FROM ' . config::$SQL_PREFIX . 'pois as P, `' . config::$SQL_PREFIX .
        'user-login` as U WHERE P.poi_id = :poi_id AND P.user_id = U.id AND P.deleted = 0;';
    if ($_SESSION['role'] >= config::$ROLE_ADMIN){
        $stmt = 'SELECT P.poi_id, P.name as poi_name, P.City, P.Postalcode, P.Streetname, P.Housenumber, P.picture, P.start, P.end, P.category,
            P.history, P.type, U.name as user_name, P.deletedPic, P.deleted, P.duty, P.blog FROM ' . config::$SQL_PREFIX . 'pois as P, `' . config::$SQL_PREFIX .
            'user-login` as U WHERE P.poi_id = :poi_id AND P.user_id = U.id';
    }
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $poi_id;
    $params[0]['nam'] = ":poi_id";
    $result = ExecuteStatementWR($stmt, $params)[0];
    unset($result[0]);
    unset($result[1]);
    unset($result[2]);
    unset($result[3]);
    unset($result[4]);
    unset($result[5]);
    unset($result[6]);
    unset($result[7]);
    unset($result[8]);
    unset($result[9]);
    unset($result[10]);
    unset($result[11]);
    unset($result[12]);
    unset($result[13]);
    unset($result[14]);
    unset($result[15]);
    unset($result[16]);
    if ($result['deletedPic'] == 1 && $_SESSION['role'] < config::$ROLE_ADMIN) {
        $result['picture'] = null;
    }
    return $result;
}

/**
 * gets all current addresses
 * @return array structured result of request
 */
function getCurrentAddresses()
{
    $stmt = 'SELECT P.poi_id, P.City as ct , P.Postalcode as pc , P.Streetname as st , P.Housenumber as hn FROM ' . config::$SQL_PREFIX . 'pois as P where deleted = 0;';
    if ($_SESSION['role'] >= config::$ROLE_ADMIN){
        $stmt = 'SELECT P.poi_id, P.City as ct , P.Postalcode as pc , P.Streetname as st , P.Housenumber as hn FROM ' . config::$SQL_PREFIX . 'pois as P ;';
    }
    $params = array();
    $result = ExecuteStatementWR($stmt, $params);
    return $result;
}

/**
 * gets minmal and maximal year of all points of interest for correct display of slider
 * @return array|bool|null
 */
function selectMinMaxYear()
{
    $prep_stmt = 'SELECT MIN(C.MinYear1) as MinYear, MAX(D.MaxYear1) as MaxYear from ((SELECT MIN(start) as MinYear1 from `' . config::$SQL_PREFIX . 'pois` where deleted = 0) UNION (SELECT MIN(end) as MinYear1 from `' . config::$SQL_PREFIX . 'pois` where deleted = 0)) as C, ((SELECT MAX(start) as MaxYear1 from `' . config::$SQL_PREFIX . 'pois` where deleted = 0) UNION (SELECT MAX(end) as MaxYear1 from `' . config::$SQL_PREFIX . 'pois` where deleted = 0)) as D;';
    if ($_SESSION['role'] >= config::$ROLE_ADMIN){
        $prep_stmt = 'SELECT MIN(C.MinYear1) as MinYear, MAX(D.MaxYear1) as MaxYear from ((SELECT MIN(start) as MinYear1 from `' . config::$SQL_PREFIX . 'pois`) UNION (SELECT MIN(end) as MinYear1 from `' . config::$SQL_PREFIX . 'pois`)) as C, ((SELECT MAX(start) as MaxYear1 from `' . config::$SQL_PREFIX . 'pois`) UNION (SELECT MAX(end) as MaxYear1 from `' . config::$SQL_PREFIX . 'pois`)) as D;';
    }
    $params = array();
    $result = ExecuteStatementWR($prep_stmt, $params);
    return $result;
}

/**
 * selects all point of interest which were created from a user
 * @param string $username username of point of interest creating user
 * @return array Structred array of all data to points of interest which were created by selected user
 */
function getAllPoisOfUser($username)
{
    $prep_stmt = "select " . config::$SQL_PREFIX . "pois.*, `" . config::$SQL_PREFIX . "user-login`.name as username from " . config::$SQL_PREFIX . "pois join `" . config::$SQL_PREFIX . "user-login` on " . config::$SQL_PREFIX . "pois.user_id = `" . config::$SQL_PREFIX . "user-login`.id where `" . config::$SQL_PREFIX . "user-login`.name = :name and " . config::$SQL_PREFIX . "pois.deleted = 0;";
    if ($_SESSION['role'] >= config::$ROLE_ADMIN){
        $prep_stmt = "select " . config::$SQL_PREFIX . "pois.*, `" . config::$SQL_PREFIX . "user-login`.name as username from " . config::$SQL_PREFIX . "pois join `" . config::$SQL_PREFIX . "user-login` on " . config::$SQL_PREFIX . "pois.user_id = `" . config::$SQL_PREFIX . "user-login`.id where `" . config::$SQL_PREFIX . "user-login`.name = :name ;";
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
 * updates a given point of Interest picture
 * @param string $newtoken new picture token
 * @param int $poiid id of poi
 */
function updatePicForPoi($newtoken, $poiid)
{
    $prep_stmt = 'UPDATE `' . config::$SQL_PREFIX . 'pois` SET picture = :pictureNew WHERE poi_id = :poi_id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $newtoken;
    $params[0]['nam'] = ":pictureNew";
    $params[1] = array();
    $params[1]['typ'] = 'd';
    $params[1]['val'] = $poiid;
    $params[1]['nam'] = ":poi_id";
    ExecuteStatementWR($prep_stmt, $params, false);
}

/**
 * get information about all available point of interests with a certain title picture
 * @param string $PicToken token of picture
 * @return array List of all available point of interest
 */
function getAllPoisWithCertainPicture($PicToken)
{
    $prep_stmt = "SELECT * FROM " . config::$SQL_PREFIX . "pois where picture = :picture ;";
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $PicToken;
    $params[0]['nam'] = ":picture";
    $result = ExecuteStatementWR($prep_stmt, $params);
    return $result;
}

/**
 * updates deletion state of point of interest by poiid
 * @param int $poiid Identifier of point of interest
 * @param bool $state true if it should be marked as deleted
 * @return array|bool|null result
 */
function updateDeletionStatePoiByPoiid($poiid, $state)
{
    $val = $state ? 1 : 0;
    $prep_stmt = 'Update `' . config::$SQL_PREFIX . 'pois` SET `deleted`  = :deleted where `poi_id` = :POIID ;';
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
 * updates deletion state of point of interest by poiid
 * @param int $poiid Identifier of point of interest
 * @param bool $state true if it should be marked as deleted
 * @return array|bool|null result
 */
function updateDeletionPicStatePoiByPoiid($poiid, $state)
{
    $val = $state ? 1 : 0;
    $prep_stmt = 'Update `' . config::$SQL_PREFIX . 'pois` SET `deletedPic`  = :deleted where `poi_id` = :POIID ;';
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
 * updates blogentry of certain poi
 * @param int $poiid identifier of poi
 * @param string $poiUrl uri of blogpost
 * @return array|bool|null structured result
 */
function updateBlogPoi($poiid, $poiUrl) {
    $prep_stmt = 'Update `' . config::$SQL_PREFIX . 'pois` SET `blog`  = :blog where `poi_id` = :POIID ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $poiUrl;
    $params[0]['nam'] = ":blog";
    $params[1] = array();
    $params[1]['typ'] = 's';
    $params[1]['val'] = $poiid;
    $params[1]['nam'] = ":POIID";
    $result = ExecuteStatementWR($prep_stmt, $params, false, true);
    return $result;
}