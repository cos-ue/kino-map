<?php
/**
 * In this file are all functions called from Formular/tapi.php . Some functions are only trampoline functions.
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * checks if request is valid and has right api-token
 * @param string $token api token which is given in incoming request
 * @return bool true if token is correct
 */
function checkApiToken($token)
{
    $Tokens = array(config::$CSTOKEN);
    foreach ($Tokens as $dbtoken) {
        if ($dbtoken == $token) {
            return true;
        }
    }
    return false;
}

/**
 * creates array which is returned if request was successful
 * @param array $input optional possibility to reply with data
 * @return array complete reply array with all needed fields
 */
function successfullRequest($input = array())
{
    $std = array(
        "result" => "ack",
        "code" => 0
    );
    return array_merge($std, $input);
}

/**
 * creates array which is returned if request was not successful
 * @param array $input optional possibility to reply with data
 * @return array complete reply array with all needed fields
 */
function failedRequest($input = array())
{
    $std = array(
        "result" => "nack",
        "code" => 1
    );
    return array_merge($std, $input);
}

/**
 * check if a transmitted username is in local database
 * @param string $username transmitted username from cosp
 * @return bool true if user is existent in local database
 */
function checkForUsername($username)
{
    return in_array($username, getAllUsernames(true));
}

/**
 * activates or deactivates a user account on this docked in platform
 * @param array $json needed data from request
 * @param bool $value if true user is activated otherwise deactivated
 * @return array returns success-state of request
 */
function AktivateUserRapi($json, $value)
{
    if (checkForUsername($json['username']) == false) {
        return successfullRequest($json);
    }
    $result = updateDeaktivate($json['username'], $value ? 0 : 1);
    if ($result == null) {
        return failedRequest($json);
    } else {
        return successfullRequest($json);
    }
}

/**
 * removes pictureTokens from entries
 * @param array $json structured request data
 * @return array structured result
 */
function removePictureTokenRevApi($json)
{
    $links = getLinkIdsPoiPic($json['picToken']);
    foreach ($links as $link) {
        if (isset($json['override'])) {
            deletePoiPicLinkByIDDBWrap($link['id'], $json['override']);
            if ($json['override'] == false){
                updateDeletionPicStateLinkPoiPicByID($link['id'], true);
            }
        } else {
            deletePoiPicLinkByIDDBWrap($link['id']);
            updateDeletionPicStateLinkPoiPicByID($link['id'], true);
        }
    }
    $pois = getAllPoisWithCertainPicture($json['picToken']);
    foreach ($pois as $poi) {
        if (isset($json['override'])) {
            deletePoiMainPicDBWrap($poi['poi_id'], $json['override']);
        } else {
            deletePoiMainPicDBWrap($poi['poi_id']);
        }
    }
    return successfullRequest($json);
}

/**
 * removes references to a deleted story
 * @param array $json structured request
 * @return array structured result
 */
function deleteStoryReference($json)
{
    $links = getPoiForStory($json['StoryToken'], true, true);
    foreach ($links as $link) {
        deletePoiStoryLinkByIDDBWrap($link['id'], $json['overwrite']);
        updateDeletionStoryStateLinkPoiStoryByID($link['id'], true);
    }
    return successfullRequest($json);
}

/**
 * restores all links of a story
 * @param array $json structured request
 * @return array structured result
 */
function restoreStoryRapi($json)
{
    $links = getPoiForStory($json['StoryToken'], true, true);
    foreach ($links as $link) {
        updateDeletionStoryStateLinkPoiStoryByID($link['id'], false);
        updateDeletionStateLinkPoiStoryByID($link['id'], false);
    }
    return successfullRequest($json);
}

/**
 * restores all links of a picture
 * @param array $json structured request
 * @return array structured result
 */
function restorePictureRapi($json)
{
    $links = getLinkIdsPoiPic($json['picToken']);
    foreach ($links as $link) {
        updateDeletionPicStateLinkPoiPicByID($link['id'], false);
        updateDeletionStateLinkPoiPicByID($link['id'], false);
    }
    $pois = getAllPoisWithCertainPicture($json['picToken']);
    foreach ($pois as $poi) {
        updateDeletionPicStatePoiByPoiid($poi['poi_id'], false);
    }
    return successfullRequest($json);
}