<?php
/**
 * In this file are all Deletion Wrappers written. It enables the feature of mark things as deleted.
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * sets art of deletion depending of config for seat counter
 * @param int $seatID identifier of seat entry
 * @param bool $overwrite force direkt delete
 */
function deleteSeatsDBWrap($seatID, $overwrite = false){
    if (config::$DIRECT_DELETE || $overwrite){
        deleteValidateSeats($seatID);
        deleteSeats($seatID);
        return;
    }
    updateDeletionStateSeatsById($seatID, true);
}

/**
 * sets art of deletion depending of config for names
 * @param int $nameID identifier of name entry
 * @param bool $overwrite force direkt delete
 */
function deleteNamesDBWrap($nameID, $overwrite = false){
    if (config::$DIRECT_DELETE || $overwrite){
        deleteValidateName($nameID);
        deleteName($nameID);
        return;
    }
    updateDeletionStateNamesByID($nameID, true);
}

/**
 * sets art of deletion depending of config for cinema counter
 * @param int $cinemaID identifier of cinema counter entry
 * @param bool $overwrite force direkt delete
 */
function deleteCinemasDBWrap($cinemaID, $overwrite = false){
    if (config::$DIRECT_DELETE || $overwrite){
        deleteValidateCinema($cinemaID);
        deleteCinemas($cinemaID);
        return;
    }
    updateDeletionStateCinemasById($cinemaID, true);
}

/**
 * sets art of deletion depending of config for operator
 * @param int $operatorID identifier of operator entry
 * @param bool $overwrite force direkt delete
 */
function deleteOperatorsDBWrap($operatorID, $overwrite = false){
    if (config::$DIRECT_DELETE || $overwrite){
        deleteValidateOperator($operatorID);
        deleteOperator($operatorID);
        return;
    }
    updateDeletionStateOperatorsById($operatorID, true);
}

/**
 * sets art of deletion depending of config for historical Address
 * @param int $histAddressID identifier of historical Address entry
 * @param bool $overwrite force direkt delete
 */
function deleteHistAddressDBWrap($histAddressID, $overwrite = false){
    if (config::$DIRECT_DELETE || $overwrite){
        deleteValidateHistAddress($histAddressID);
        deleteHistAddress($histAddressID);
        return;
    }
    updateDeletionStateHistAddressById($histAddressID, true);
}

/**
 * sets art of deletion depending of config for comment
 * @param int $commentID identifier of comment
 * @param bool $overwrite force direkt delete
 */
function deleteCommentsDBWrap($commentID, $overwrite = false){
    if (config::$DIRECT_DELETE || $overwrite){
        deleteComment($commentID);
        return;
    }
    updateDeletionStateCommentById($commentID, true);
}

/**
 * sets art of deletion depending of config for comment by poiid
 * @param int $POIID identifier of point of interest
 * @param bool $overwrite force direkt delete
 */
function deleteCommentsByPoiidDBWrap($POIID, $overwrite = false){
    if (config::$DIRECT_DELETE || $overwrite){
        deleteCommentByPOI($POIID);
        return;
    }
    updateDeletionStateCommentByPoiid($POIID, true);
}

/**
 * sets art of deletion depending of config for poi
 * @param int $POIID identifier of point of interest
 * @param bool $overwrite force direkt delete
 */
function deletePoiDBWrap($POIID, $overwrite = false){
    if (config::$DIRECT_DELETE || $overwrite){
        deletePoi($POIID);
        return;
    }
    updateDeletionStatePoiByPoiid($POIID, true);
}

/**
 * sets art of deletion depending of config for link between poi and pic
 * @param int $LinkID identifier of link between poi and pic
 * @param bool $overwrite force direkt delete
 */
function deletePoiPicLinkByIDDBWrap($LinkID, $overwrite = false){
    if (config::$DIRECT_DELETE || $overwrite){
        deleteValidationsForCertainPoiPicLink($LinkID);
        deleteCertainPoiPicLink($LinkID);
        return;
    }
    updateDeletionStateLinkPoiPicByID($LinkID, true);
}

/**
 * sets art of deletion depending of config for link between poi and story
 * @param int $LinkID identifier of link between poi and story
 * @param bool $overwrite force direkt delete
 */
function deletePoiStoryLinkByIDDBWrap($LinkID, $overwrite = false){
    if (config::$DIRECT_DELETE || $overwrite){
        deleteValidatePoiStory($LinkID);
        deletePoiStory($LinkID);
        return;
    }
    updateDeletionStateLinkPoiStoryByID($LinkID, true);
}

/**
 * sets art of deletion depending of config for the main picture of a point of interest
 * @param int $POIID identifier of point of interest
 * @param bool $overwrite force direkt delete
 */
function deletePoiMainPicDBWrap($POIID, $overwrite = false){
    if (config::$DIRECT_DELETE || $overwrite){
        updatePicForPoi("", $POIID);
        deletevalidateByPOI($POIID);
        return;
    }
    updateDeletionPicStatePoiByPoiid($POIID, true);
}

/**
 * sets art of deletion depending on config for sources of a point of interest
 * @param $sid
 * @param false $overwrite
 * @return array|bool|null structured result
 */
function deleteSourceDBWrap($sid, $overwrite = false){
    if (config::$DIRECT_DELETE || $overwrite){
        return deleteSource($sid);
    }
    return updateSourceDeletionState($sid, true);
}