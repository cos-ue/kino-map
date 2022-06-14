<?php
/**
 * This File includes all needed functions for hist_adr-table
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * get's all historical Addresses for a Point of interest
 * @param int $poiid id of point of interest
 * @return array|bool|null Result of select-statement
 */
function getHistoricalAddressesByPoiId($poiid)
{
    $stmt = 'select H.ID, H.City, H.Postalcode, H.Streetname, H.Housenumber, H.start, H.end, H.deleted from `' . config::$SQL_PREFIX . 'hist_adr` as H join `' . config::$SQL_PREFIX . 'user-login` as U on H.creator = U.id where H.POI_ID = :poi_id and deleted = 0 ;';
    if ($_SESSION['role'] >= config::$ROLE_ADMIN){
        $stmt = 'select H.ID, H.City, H.Postalcode, H.Streetname, H.Housenumber, H.start, H.end, H.deleted from `' . config::$SQL_PREFIX . 'hist_adr` as H join `' . config::$SQL_PREFIX . 'user-login` as U on H.creator = U.id where H.POI_ID = :poi_id ;';
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
 * Get's All Addresses of Table
 * @return array structured result
 */
function getAllAddresses()
{
    $stmt = 'select H.ID, H.City as ct , H.Postalcode as pc , H.Streetname as st , H.Housenumber as hn from `' . config::$SQL_PREFIX . 'hist_adr` as H ;';
    $params = array();
    $result = ExecuteStatementWR($stmt, $params);
    return $result;
}

/**
 * Inserts new historical Address into database for a certain POI
 * @param int $poi_id id of point of interest
 * @param int $start start of using this address
 * @param int $end end of using this address
 * @param string $streetname streetname of address
 * @param string $housenumber housenumber of address
 * @param string $city city of address
 * @param int $postalcode postalcode of address
 * @return bool|null state of request
 */
function insertHistoricalAddressOfPOI($poi_id, $start, $end, $streetname, $housenumber, $city, $postalcode)
{
    $stmt = 'INSERT INTO `' . config::$SQL_PREFIX . 'hist_adr` ( `POI_ID` , `start` , `end` , `City` ,`Postalcode` , `Streetname` , `Housenumber` , `creator` ) values ( :pid , :start , :end , :City ,:Postalcode , :Streetname , :Housenumber , :creator );';
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
    $params[3]['val'] = $city;
    $params[3]['nam'] = ":City";
    $params[4] = array();
    $params[4]['typ'] = 's';
    $params[4]['val'] = $postalcode;
    $params[4]['nam'] = ":Postalcode";
    $params[5] = array();
    $params[5]['typ'] = 's';
    $params[5]['val'] = $streetname;
    $params[5]['nam'] = ":Streetname";
    $params[6] = array();
    $params[6]['typ'] = 's';
    $params[6]['val'] = $housenumber;
    $params[6]['nam'] = ":Housenumber";
    $params[7] = array();
    $params[7]['typ'] = 's';
    $params[7]['val'] = getUserData($_SESSION['username'])['id'];
    $params[7]['nam'] = ":creator";
    $result = ExecuteStatementWR($stmt, $params, false);
    return $result;
}

/**
 * get creator of historical address
 * @param int $histAddrid id of historical address
 * @return string creator of historical address
 */
function getCreatorByHistoricalAddressesId($histAddrid)
{
    $stmt = 'select H.ID, U.name as Username from `' . config::$SQL_PREFIX . 'hist_adr` as H join `' . config::$SQL_PREFIX . 'user-login` as U on H.creator = U.id where H.ID = :id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 'i';
    $params[0]['val'] = $histAddrid;
    $params[0]['nam'] = ":id";
    $result = ExecuteStatementWR($stmt, $params)[0];
    return $result['Username'];
}

/** updates  historical Address for a certain POI
 * @param int $id id of historical address
 * @param int $start start of using this address
 * @param int $end end of using this address
 * @param string $streetname streetname of address
 * @param string $housenumber housenumber of address
 * @param string $city city of address
 * @param int $postalcode postalcode of address
 * @return bool|null state of request
 */
function updateHistAddr($id, $start, $end, $streetname, $housenumber, $city, $postalcode)
{
    $prep_stmt = 'Update `' . config::$SQL_PREFIX . 'hist_adr` SET start = :start , end = :end , City = :City ,Postalcode = :Postalcode , Streetname = :Streetname , Housenumber = :Housenumber , creator = :creator , creationdate =  CURRENT_TIMESTAMP where ID = :ID ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $start;
    $params[0]['nam'] = ":start";
    $params[1] = array();
    $params[1]['typ'] = 's';
    $params[1]['val'] = $end;
    $params[1]['nam'] = ":end";
    $params[2] = array();
    $params[2]['typ'] = 's';
    $params[2]['val'] = $city;
    $params[2]['nam'] = ":City";
    $params[3] = array();
    $params[3]['typ'] = 's';
    $params[3]['val'] = $postalcode;
    $params[3]['nam'] = ":Postalcode";
    $params[4] = array();
    $params[4]['typ'] = 's';
    $params[4]['val'] = $streetname;
    $params[4]['nam'] = ":Streetname";
    $params[5] = array();
    $params[5]['typ'] = 's';
    $params[5]['val'] = $housenumber;
    $params[5]['nam'] = ":Housenumber";
    $params[6] = array();
    $params[6]['typ'] = 's';
    $params[6]['val'] = getUserData($_SESSION['username'])['id'];
    $params[6]['nam'] = ":creator";
    $params[7] = array();
    $params[7]['typ'] = 's';
    $params[7]['val'] = $id;
    $params[7]['nam'] = ":ID";
    $result = ExecuteStatementWR($prep_stmt, $params, false);
    return $result;
}

/** deletes given historical address
 * @param $address_id int ID of given historical address
 * @return bool|null state of request
 */
function deleteHistAddress($address_id)
{
    $prep_stmt = "DELETE FROM `" . config::$SQL_PREFIX . "hist_adr` WHERE ID = :address_id";
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $address_id;
    $params[0]['nam'] = ":address_id";
    dump($params, 8);
    $x = ExecuteStatementWR($prep_stmt, $params, false);
    return $x;
}

/**
 * updates deletion state of historical address  by id
 * @param int $id Identifier of historical address
 * @param bool $state true if it should be marked as deleted
 * @return array|bool|null result
 */
function updateDeletionStateHistAddressById($id, $state)
{
    $val = $state ? 1 : 0;
    $prep_stmt = 'Update `' . config::$SQL_PREFIX . 'hist_adr` SET `deleted`  = :deleted where `ID` = :ID ;';
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