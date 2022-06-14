<?php
/**
 * In this files are all functions called by Formular/api.php
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * Gethers all intel needed by a javascript function to display the personal area modal
 * Collects all information which a javascript functions needs to display personal area modal. The data is from different database tables and is not used in this compact form anywhere else.
 * @param string $username username of user for which this request is processed
 * @return array structured array with all informations in data section; data section has three diffrent subsections "pois", "User" and "comments"
 */
function PersonalAreaCollection($username)
{
    if ($username == 'gast') {
        return generateError();
    }
    $result = array();
    $pois = getAllPoisOfUser($username);
    $rpois = array();
    foreach ($pois as $poi) {
        $curAdr = "";
        if ($poi["Streetname"] !== "") {
            $curAdr = $curAdr . $poi["Streetname"];
        }
        if ($poi["Housenumber"] !== "") {
            if ($curAdr !== "") {
                $curAdr = $curAdr . " " . $poi["Housenumber"];
            } else {
                $curAdr = $curAdr . $poi["Housenumber"];
            }
        }
        if ($poi["City"] !== "") {
            if ($curAdr !== "") {
                $curAdr = $curAdr . ", " . $poi["City"];
            } else {
                $curAdr = $curAdr . $poi["City"];
            }
        }
        if ($poi["Postalcode"] !== "") {
            if ($curAdr !== "") {
                $curAdr = $curAdr . " " . $poi["Postalcode"];
            } else {
                $curAdr = $curAdr . $poi["Postalcode"];
            }
        }
        $rpois[] = array(
            "lat" => $poi["lat"],
            "lng" => $poi["lng"],
            "name" => $poi["name"],
            "poi_id" => $poi["poi_id"],
            "address" => $curAdr,
            "edit_enable" => (getValidateSumForPOI($poi["poi_id"]) < 400) || ($_SESSION['role'] >= config::$ROLE_EMPLOYEE),
            "val_sum" => getValidateSumForPOI($poi["poi_id"]),
            "deleted" => $poi['deleted'] == 1,
        );
    }
    $result["pois"] = $rpois;
    $userdata = getUserData($username);
    $result["User"] = array(
        "username" => $userdata['name'],
        "firstname" => isset($userdata["firstname"]) ? $userdata["firstname"] : "",
        "lastname" => isset($userdata["lastname"]) ? $userdata["lastname"] : "",
        "email" => $userdata["email"]
    );
    $comments = getAllCommentsOfUser($username);
    $rcomments = array();
    foreach ($comments as $comment) {
        $datum = new DateTime($comment['timestamp']);
        $rcomments[] = array(
            "date" => $datum->format('d.m.Y H:i'),
            "content" => $comment['content'],
            "poiname" => $comment['poiname'],
            "poiid" => $comment['poi_id'],
            "lat" => $comment['lat'],
            "lng" => $comment['lng'],
            "cid" => $comment['comment_id'],
            "deleted" => $comment['deleted'] == 1,
        );
    }
    $result["comments"] = $rcomments;
    return array_merge(array('data' => $result), successfullRequest());
}

/**
 * deletes a comment and returns state of result
 * @param int $cid unique identificator of comment
 * @return array result of action
 */
function deleteUserComment($cid)
{
    $comment = selectCommentsByCommentID($cid);
    dump($comment, 4);
    if (($_SESSION['role'] < config::$ROLE_EMPLOYEE) || ($comment['name'] !== $_SESSION['username'])) {
        return generateError();
    }
    deleteCommentsDBWrap($cid);
    return generateSuccess();
}

/**
 * processes request to add comment into database with link to a point of interest
 * @param array $json all needed data for add a comment
 * @return array success state
 */
function AddUserComment($json)
{
    if ($_SESSION['role'] <= config::$ROLE_UNAUTH_USER) {
        return generateError();
    }
    $ard = array(
        'comment' => $json['comment'],
        'poi_id' => $json['poi_id']
    );
    $result = insertComment($ard);
    if (!$result) {
        return generateError();
    }
    return array_merge($json, successfullRequest());
}

/**
 * generates json output for api
 * @param array $array all data that should be returned
 */
function generateJson($array)
{
    echo json_encode($array);
}

/**
 * genereates error message for all functions
 * @param null|string|array|int $msg error message to send
 * @return array represents static error message
 */
function generateError($msg = null)
{
    if ($msg !== null) {
        return array(
            "result" => "Wrong Request!",
            "code" => 1,
            "msg" => $msg
        );
    }
    return array(
        "result" => "Wrong Request!",
        "code" => 1
    );
}

/**
 * genereates error message for all functions with code 2
 * @param null|string|array|int $msg error message to send
 * @return array represents static error message
 */
function generateError2($msg = null)
{
    if ($msg !== null) {
        return array(
            "result" => "Wrong Request!",
            "code" => 2,
            "msg" => $msg
        );
    }
    return array(
        "result" => "Wrong Request!",
        "code" => 2
    );
}

/**
 * generates success message for all functions
 * @return array represents static success message
 */
function generateSuccess()
{
    return array(
        "result" => "ack",
        "code" => 0
    );
}

/**
 * returns minimal and maximal year for slider from database
 * @return array results and success state
 */
function getMinimalMaximalYear()
{
    $result = selectMinMaxYear();
    $result2 = array(
        'MinYear' => null,
        'MaxYear' => null
    );
    if (isset($result[0])) {
        $result2['MinYear'] = $result[0]['MinYear'];
        $result2['MaxYear'] = $result[0]['MaxYear'];
        return array_merge($result2, generateSuccess());
    }
    return array_merge($result2, generateSuccess());
}

/**
 * executes success more function for api and adds result state
 * @param array $json all needed information for executing request
 * @return array result and result state
 */
function selectMoreApi($json)
{
    $poi_id = $json['poi_id'];
    $poiData = selectMore($poi_id);
    if (getValidateSumTimespan($poi_id) < 400 && ($_SESSION["role"] < config::$ROLE_AUTH_USER) && ($poiData['user_name'] !== $_SESSION['username'])) {
        $poiData['start'] = "";
        $poiData['end'] = "";
    } else {
        $poiData['timespan_validate'] = getValidateSumTimespan($poi_id) >= 400 || in_array($poi_id, getValidationsByUserForTimeSpans());
    }
    if (getValidateSumCurAddresse($poi_id) < 400 && ($_SESSION["role"] < config::$ROLE_AUTH_USER) && ($poiData['user_name'] !== $_SESSION['username'])) {
        $poiData['Housenumber'] = "";
        $poiData['City'] = "";
        $poiData['Postalcode'] = "";
        $poiData['Streetname'] = "";
    } else {
        $poiData['currAddr_validate'] = getValidateSumCurAddresse($poi_id) >= 400 || in_array($poi_id, getValidationsByUserForCurrentAddress());
    }
    if (getValidateSumHist($poi_id) < 400 && ($_SESSION["role"] < config::$ROLE_AUTH_USER) && ($poiData['user_name'] !== $_SESSION['username'])) {
        $poiData['history'] = "";
    } else {
        $poiData['history_validate'] = getValidateSumHist($poi_id) >= 400 || in_array($poi_id, getValidationsByUserForHistory());
    }
    $poiData['type'] = getCinemaTypeNameByTypeId($poiData['type']);
    if (getValidateSumType($poi_id) < 400 && ($_SESSION["role"] < config::$ROLE_AUTH_USER) && ($poiData['user_name'] !== $_SESSION['username'])) {
        $poiData['type'] = "";
    } else {
        $poiData['type_validate'] = getValidateSumType($poi_id) >= 400 || in_array($poi_id, getValidationsByUserForCinemaType());
    }
    if ($_SESSION['role'] < config::$ROLE_ADMIN) {
        $poiData['deleted'] = false;
    } else {
        $poiData['deleted'] = ($poiData['deleted'] == 1);
    }
    $poiData['duty'] = ($poiData['duty'] == 1);
    $poiData['validated'] = getValidateSumForPOI($poi_id) >= 400 || in_array($poi_id, getValidationsByUserForPOI());
    $poiData['editable'] = $_SESSION['role'] >= config::$ROLE_EMPLOYEE || ($_SESSION['role'] >= config::$ROLE_AUTH_USER && (getValidateSumForPOI($poi_id) < 400 || getValidateSumTimespan($poi_id) < 400 || getValidateSumCurAddresse($poi_id) < 400 || getValidateSumHist($poi_id) < 400 || getValidateSumType($poi_id) < 400 || getValidateSumForPOI($poi_id) < 400));
    $poiData['deletable'] = $_SESSION['role'] >= config::$ROLE_EMPLOYEE || ($_SESSION['role'] >= config::$ROLE_AUTH_USER && $_SESSION['username'] == $poiData['user_name'] && (getValidateSumForPOI($poi_id) < 400 || getValidateSumTimespan($poi_id) < 400 || getValidateSumCurAddresse($poi_id) < 400 || getValidateSumHist($poi_id) < 400 || getValidateSumType($poi_id) < 400 || getValidateSumForPOI($poi_id) < 400));
    $poiData['validatable'] = $_SESSION['role'] >= config::$ROLE_AUTH_USER;
    $poiData['finalDelete'] = $_SESSION['role'] >= config::$ROLE_ADMIN;
    $poiData['editLink'] = 'editPoi.php?' . http_build_query(array("poi" => $poi_id, "map" => 1), '', '&');
    if (getValidateSumForPOI($poi_id) < 400 && $_SESSION['role'] < config::$ROLE_AUTH_USER && ($poiData['user_name'] !== $_SESSION['username'])) {
        return generateError();
    }
    return successfullRequest(array('data' => $poiData));
}

/**
 * loads comments for certain poi
 * @param array $json structured request data
 * @return array strutured result data
 */
function ShowMoreComments($json)
{
    $poi_id = $json['poi_id'];
    $deleteComments = false;
    $comments = array();
    $poi_name = selectMore($poi_id)['poi_name'];
    if ($_SESSION["role"] >= config::$ROLE_UNAUTH_USER) {
        $comments = selectComments($poi_id);
        $deleteComments = $_SESSION["role"] >= config::$ROLE_EMPLOYEE;
    }
    $result = array();
    foreach ($comments as $comment) {
        $comment['deleted'] = $comment['deleted'] == 1;
        $result[] = array_merge(array('deletable' => $_SESSION['username'] == $comment['name']), $comment);
    }
    return array_merge(array('data' => array('comments' => $result, 'deleteComments' => $deleteComments, 'poi_name' => $poi_name)), successfullRequest());
}

/**
 * loads addtional Pictures from POI
 * @param array $json structured request data
 * @return array structured result data
 */
function ShowMoreLoadAdditionalPictures($json)
{
    $poi_id = $json['poi_id'];
    $pictures = getAllPicturesListAPI(true);
    $links = getPicturesForPoi($poi_id);
    $needed = array();
    $pics_DB = array();
    foreach ($links as $link) {
        $needed[] = $link['pic'];
        $pics_DB[$link['pic']] = $link;
    }
    $pics = array();
    foreach ($pictures as $pic) {
        if (in_array($pic['identifier'], $needed)) {
            $id = getLinkIdPoiPic($poi_id, $pic['identifier']);
            $val = getValidateSumPoiPic($id);
            if ($val >= 400 || $_SESSION['role'] >= config::$ROLE_AUTH_USER) {
                $validators = GetPoiPicLinkValidators($id);
                $validated = ($val >= 400) || in_array($_SESSION['username'], $validators);
                $deletable = ($val < 400) || $_SESSION['role'] >= config::$ROLE_EMPLOYEE;
                $restrictions = LinkPoiPicRestictedPic($id) || LinkPoiPicRestictedPOI($id);
                $pics[] = array_merge($pic, array('ppid' => $id, 'validated' => $validated, "deletable" => $deletable, "deleted" => $pics_DB[$pic['identifier']]['deleted'] == 1, "restrictions" => $restrictions));
            }
        }
    }
    return array_merge(array('data' => $pics), successfullRequest());
}

/**
 * returns url to main picture
 * @param array $json structured request data
 * @return array structured result data
 */
function ShowMoreLoadPicture($json)
{
    $poi_id = $json['poi_id'];
    $poiData = selectMore($poi_id);
    $picture = "";
    $sourcetype = "";
    $source = "";
    if (isset($poiData["picture"]) || ($poiData["picture"] != "" && $poiData["picture"] != null)) {
        $seccode = getRemoteSeccode($poiData["picture"]);
        $picturedata = GetDataForSingleMaterial($poiData["picture"])['pic'];
        $source = $picturedata['source'];
        $sourcetype = $picturedata['sourcename'];
        $picture = config::$USAPI . "?" . http_build_query(array("type" => "gpf", "data" => $seccode["token"], "seccode" => $seccode["seccode"], "time" => $seccode["time"]), '', '&');
    }
    return array_merge(array('data' => $picture, "deleted" => $poiData['deletedPic'] == 1, "source" => $source, "sourceType" => $sourcetype), successfullRequest());
}

/**
 * selects all stories for option field for a certain POI
 * @param array $json structured request data
 * @return array structured result data
 */
function getStoriesForOptionDropDownShowMoreApi($json)
{
    $poi_id = $json['poi_id'];
    $result = array();
    if ($_SESSION["role"] >= config::$ROLE_AUTH_USER) {
        $result = ApiCall(array(), "gst")['data'];
    }
    $endresult = array();
    $known = getPoiForStoryByPoiId($poi_id, true);
    $tokens = array();
    foreach ($known as $k) {
        $tokens[] = $k['token'];
    }
    foreach ($result as $res) {
        if (in_array($res['token'], $tokens) == false) {
            $endresult[] = $res;
        }
    }
    return array_merge(array('data' => $endresult), successfullRequest());
}

/**
 * selects all story links for a certain POI
 * @param array $json structured request data
 * @return array structured result data
 */
function selectStoriesPoiAPI($json)
{
    $poi_id = $json['poi_id'];
    $links = getPoiForStoryByPoiId($poi_id);
    $tokens = array();
    $result = array();
    if (count($links) > 0) {
        for ($i = 0; $i < count($links); $i++) {
            $tokens[] = $links[$i]['token'];
        }
        $stories = getStoriesAsListFromCose($tokens)['data'];
        for ($i = 0; $i < count($links); $i++) {
            if ((getValidateSumPoiStory($links[$i]['id']) >= 400) || ($_SESSION["role"] >= config::$ROLE_AUTH_USER)) {
                $result[$i] = array_merge($stories[$links[$i]['token']], $links[$i]);
                $result[$i]['deleted'] = $links[$i]['deleted'] == 1;
                if ($_SESSION["username"] !== "gast") {
                    $result[$i]['LinkValidated'] = in_array($links[$i]['id'], getValidationsByUserForLinkPoiStory()) || (getValidateSumPoiStory($links[$i]['id']) >= 400);
                    $result[$i]['LinkDeletable'] = ((getValidateSumPoiStory($links[$i]['id']) < 400) && ($_SESSION['role'] > config::$ROLE_UNAUTH_USER)) || ($_SESSION['role'] >= config::$ROLE_EMPLOYEE);
                    $result[$i]['restrictions'] = LinkPoiStoryRestictedStory($links[$i]['id']) || LinkPoiStoryRestictedPOI($links[$i]['id']);;
                } else {
                    $result[$i]['LinkValidated'] = true;
                    $result[$i]['LinkDeletable'] = false;
                    $result[$i]['restrictions'] = false;
                }
            }
        }
    }
    $endresult = array();
    foreach ($result as $res) {
        $endresult[] = $res;
    }
    return array_merge(array('data' => $endresult), successfullRequest());
}

/**
 * selects all cinema count for a certain POI
 * @param array $json structured request data
 * @return array structured result data
 */
function selectSeatsPoiAPI($json)
{
    $poi_id = $json['poi_id'];
    return array_merge(array('data' => getCompleteInformationOfPoiSeats($poi_id)), successfullRequest());
}

/**
 * selects all cinema count for a certain POI
 * @param array $json structured request data
 * @return array structured result data
 */
function selectCinemasPoiAPI($json)
{
    $poi_id = $json['poi_id'];
    return array_merge(array('data' => getCompleteInformationOfPoiCinemas($poi_id)), successfullRequest());
}

/**
 * returns if user is guest
 * @return array structured result data
 */
function isGuestAPI()
{
    return array_merge(array('data' => ($_SESSION['username'] === 'gast')), successfullRequest());
}

/**
 * selects all historical Addresses for a certain POI
 * @param array $json structured request data
 * @return array structured result data
 */
function selectHistAddrPoiAPI($json)
{
    $poi_id = $json['poi_id'];
    return array_merge(array('data' => getCompleteInformationOfPoiHistAddress($poi_id)), successfullRequest());
}

/**
 * selects all Names for a certain POI
 * @param array $json structured request data
 * @return array structured result data
 */
function selectNamesPoiAPI($json)
{
    $poi_id = $json['poi_id'];
    $poiData = selectMore($poi_id);
    return array_merge(array('data' => getCompleteInformationOfPoiNames($poi_id, $poiData['poi_name'])), successfullRequest());
}

/**
 * selects all operators for a certain poi
 * @param array $json structured request data
 * @return array structured result data
 */
function selectOperatorsPoiAPI($json)
{
    $poi_id = $json['poi_id'];
    return array_merge(array('data' => getCompleteInformationOfPoiOperators($poi_id)), successfullRequest());
}

/**
 * execute poi selection an adds result state
 * @return array result and result state
 */
function getPoisForUserApi()
{
    return array_merge(array('data' => getPoisForUser()), successfullRequest());
}

/**
 * get link for user to retriev stories's information from cosp
 * @return array result and result state
 */
function getAllStoriesDataApi()
{
    $result = getAllStoriesData();
    return array_merge(array('data' => $result), successfullRequest());
}

/**
 * uploads story to cosp
 * @param array $json all information user provides for story
 * @return array result and result state
 */
function addUserStory($json)
{
    if ($_SESSION['role'] <= config::$ROLE_UNAUTH_USER) {
        return generateError();
    }
    $request = $json;
    $request['username'] = $_SESSION["username"];
    $result = addUserStoryRemote($request);
    return array_merge($result, successfullRequest());
}

/**
 * delete POI via API
 * @param array $json Structured array with information to perform delete operation
 */
function deletePointOfInterestViaAPI($json)
{
    if ($_SESSION['role'] >= config::$ROLE_EMPLOYEE || ($_SESSION['username'] == getPoi($json['poiid'])['username'] && getValidateSumForPOI($json['poiid']) < 400 && $_SESSION['role'] >= config::$ROLE_AUTH_USER)) {
        deletePOIComplete($json['poiid']);
        return generateSuccess();
    }
    return generateError();
}

/**
 * Selects single comments data based on its ID
 * @param array $json Structured array with information to perform select operation for comment
 */
function getCommentByCommentID($json)
{
    $comment = selectCommentsByCommentID($json['commentid']);
    $datum = new DateTime($comment['timestamp']);
    $result[] = array(
        "date" => $datum->format('d.m.Y H:i'),
        "content" => $comment['content'],
        "cid" => $comment['comment_id'],
        "username" => $comment['name']
    );
    $result2 = array_merge(generateSuccess(), $result);
    return $result2;
}

/**
 * Save changed Comment by using its ID
 * @param array $json data to save changed comment
 * @return array will always return true
 */
function saveCommentByID($json)
{
    if ($_SESSION['role'] <= config::$ROLE_UNAUTH_USER) {
        return generateError();
    }
    $comment = getCommentByCommentID($json['commentid']);
    if (($_SESSION['role'] < config::$ROLE_EMPLOYEE) || ($comment['name'] !== $_SESSION['username'])) {
        return generateError();
    }
    saveEditedCommentWithID($json['commentid'], $json['commentContent']);
    return generateSuccess();
}

/**
 * Loads single Data for selected Material by Material Token
 * @param array $json Needed Information to perform request
 * @return array gethered and perpared information for Javascript
 */
function DataSingleMaterial($json)
{
    $data = GetDataForSingleMaterial($json['token'])['pic'];
    $seccode = $data['token'];
    $picture = config::$USAPI . "?" . http_build_query(array("type" => "gpf", "data" => $seccode["token"], "seccode" => $seccode["seccode"], "time" => $seccode["time"]), '', '&');
    $result = array(
        "token" => explode(";", $seccode["token"])[0],
        "title" => $data['title'],
        "description" => $data['description'],
        "sourcetype" => $data['sourcename'],
        "source" => $data['source'],
        "sourcetypeid" => $data['sourceid'],
        "picture" => $picture
    );
    return array_merge(generateSuccess(), $result);
}

/**
 * Transmits edited Material Metadata to cosp
 * @param array $json array with required Information
 * @return array result is always true
 */
function saveSingleMaterialViaAPI($json)
{
    $Pictures = getRemotePictureList()['pics'];
    if ($_SESSION['role'] < config::$ROLE_EMPLOYEE) {
        foreach ($Pictures as $pic) {
            if ($json['token'] == $pic["token"]["token"]) {
                if ($pic['validationValue'] >= 400) {
                    return generateError();
                }
                if ($_SESSION['username'] != $pic['username']) {
                    return generateError();
                }
            }
        }
    }
    if (isset($json['source'], $json['sourcetype'])) {
        SaveDataForSingleMaterial($json['token'], $json['title'], $json['description'], $json['source'], $json['sourcetype']);
    }
    SaveDataForSingleMaterial($json['token'], $json['title'], $json['description']);
    return generateSuccess();
}

/**
 * Transmits edited Story data to cosp
 * @param array $json array with required Information
 * @return array result is always true
 */
function SaveDataForEditedStoryAPI($json)
{
    if ($_SESSION['role'] <= config::$ROLE_UNAUTH_USER) {
        return generateError();
    }
    $Story = getStoriesAsListFromCose(array($json['storytoken']))['data'][$json['storytoken']];
    dump($Story);
    if (($_SESSION['role'] < config::$ROLE_EMPLOYEE) && (($Story['name'] !== $_SESSION['username']) || ($Story['validate'])) ) {
        return generateError();
    }
    saveStoryEditedDataToCose($json['storytoken'], $json['title'], $json['story']);
    return generateSuccess();
}

/**
 * Save Information about a new Operator of a POI
 * gathers information from array and orders it for inserting in database
 * @param array $json array with required Information
 * @return array result is always true
 */
function SaveOperatorNewAPI($json)
{
    if ($_SESSION['role'] >= config::$ROLE_AUTH_USER) {
        insertOperator($json['poi_id'], $json['from'], $json['till'], $json['operator']);
        return array_merge($json, generateSuccess());
    }
    return generateError();
}

/**
 * Save Information about a new Name of a POI
 * gathers information from array and orders it for inserting in database
 * @param array $json array with required Information
 * @return array result is always true
 */
function SaveNameNewAPI($json)
{
    if ($_SESSION['role'] <= config::$ROLE_UNAUTH_USER) {
        return generateError();
    }
    insertNameOfPOI($json['poi_id'], $json['from'], $json['till'], $json['name']);
    return array_merge($json, generateSuccess());
}

/**
 * Save Information about a new Historical Address of a POI
 * gathers information from array and orders it for inserting in database
 * @param $json array with required Information
 * @return array result is always true
 */
function SaveHistoricalAddressNewAPI($json)
{
    if ($_SESSION['role'] <= config::$ROLE_UNAUTH_USER) {
        return generateError();
    }
    insertHistoricalAddressOfPOI($json['poi_id'], $json['from'], $json['till'], $json['streetname'], $json['housenumber'], $json['city'], $json['postalcode']);
    return array_merge($json, generateSuccess());
}

/**
 * gatheres needed information to validate time span of poi
 * @param $json array required information
 * @return array result will allways be true
 */
function validateTimeSpanApi($json)
{
    $poiid = $json['POIID'];
    if ($_SESSION['role'] >= config::$ROLE_AUTH_USER && getValidateSumTimespan($poiid) < 400 && in_array($poiid, getValidationsByUserForTimeSpans()) == false) {
        validateTimeSpanPoi($poiid);
    }
    return array_merge($json, generateSuccess());
}

/**
 * gatheres needed information to validate current address of poi
 * @param $json array required information
 * @return array result will allways be true
 */
function validateCurrentAddressApi($json)
{
    $poiid = $json['POIID'];
    if ($_SESSION['role'] >= config::$ROLE_AUTH_USER && getValidateSumCurAddresse($poiid) < 400 && in_array($poiid, getValidationsByUserForCurrentAddress()) == false) {
        validateCurrentAddressPoi($poiid);
    }
    return array_merge($json, generateSuccess());
}

/**
 * gatheres needed information to validate history of poi
 * @param $json array required information
 * @return array result will allways be true
 */
function validateHistoryApi($json)
{
    $poiid = $json['POIID'];
    if ($_SESSION['role'] >= config::$ROLE_AUTH_USER && getValidateSumHist($poiid) < 400 && in_array($poiid, getValidationsByUserForHistory()) == false) {
        validateHistoryPoi($poiid);
    }
    return array_merge($json, generateSuccess());
}

/**
 * gatheres needed information to validate name of poi
 * @param $json array required information
 * @return array result will allways be true
 */
function validatePoiNamesApi($json)
{
    $name_id = $json['NAMEID'];
    if ($_SESSION['role'] >= config::$ROLE_AUTH_USER && getValidateSumName($name_id) < 400 && in_array($name_id, getValidationsByUserForPoiNames()) == false) {
        validatePoiName($name_id);
    }
    return array_merge($json, generateSuccess());
}

/**
 * gatheres needed information to validate operators of poi
 * @param $json array required information
 * @return array result will allways be true
 */
function validatePoiOperatorsApi($json)
{
    $operator_id = $json['OPERATORID'];
    if ($_SESSION['role'] >= config::$ROLE_AUTH_USER && getValidateSumOperator($operator_id) < 400 && in_array($operator_id, getValidationsByUserForPoiOperators()) == false) {
        validatePoiOperator($operator_id);
    }
    return array_merge($json, generateSuccess());
}

/**
 * gatheres needed information to validate historical address of poi
 * @param $json array required information
 * @return array result will allways be true
 */
function validatePoiHistAddressApi($json)
{
    $address_id = $json['ADDRESSID'];
    if ($_SESSION['role'] >= config::$ROLE_AUTH_USER && getValidateSumHistAddress($address_id) < 400 && in_array($address_id, getValidationsByUserForPoiHistAddresses()) == false) {
        validatePoiHistAddress($address_id);
    }
    return array_merge($json, generateSuccess());
}

/**
 * gatheres needed information to delete name of poi
 * @param $json array required information
 * @return array result will allways be true
 */
function deleteNameApi($json)
{
    if ($_SESSION['role'] <= config::$ROLE_UNAUTH_USER) {
        return generateError();
    }
    $name_id = $json['IDent'];
    if ($_SESSION['role'] >= config::$ROLE_EMPLOYEE || getValidateSumName($name_id) < 400) {
        deleteNamesDBWrap($name_id);
    }
    return array_merge($json, generateSuccess());
}

/**
 * gatheres needed information to delete operator of poi
 * @param $json array required information
 * @return array result will allways be true
 */
function deleteOperatorApi($json)
{
    if ($_SESSION['role'] <= config::$ROLE_UNAUTH_USER) {
        return generateError();
    }
    $operator_id = $json['IDent'];
    if ($_SESSION['role'] >= config::$ROLE_EMPLOYEE || getValidateSumOperator($operator_id) < 400) {
        deleteOperatorsDBWrap($operator_id);
    }
    return array_merge($json, generateSuccess());
}

/**
 * gatheres needed information to delete historical address of poi
 * @param $json array required information
 * @return array result will allways be true
 */
function deleteHistAddressApi($json)
{
    if ($_SESSION['role'] <= config::$ROLE_UNAUTH_USER) {
        return generateError();
    }
    $operator_id = $json['IDent'];
    if ($_SESSION['role'] >= config::$ROLE_EMPLOYEE || getValidateSumHistAddress($operator_id) < 400) {
        deleteHistAddressDBWrap($operator_id);
    }
    return array_merge($json, generateSuccess());
}

/**
 * gatheres needed information to update operator of poi
 * @param $json array required information
 * @return array result will allways be true
 */
function UpdateOperatorApi($json)
{
    if ($_SESSION['role'] <= config::$ROLE_UNAUTH_USER) {
        return generateError();
    }
    if (((getValidateSumOperator($json['id']) < 400) || ($_SESSION['role'] >= config::$ROLE_EMPLOYEE)) == false) {
        return generateSuccess();
    }
    deleteValidateOperator($json['id']);
    $result = updateOperator($json['id'], $json['operator'], $json['start'], $json['end']);
    return array_merge(generateSuccess(), array('state' => $result));
}

/**
 * gatheres needed information to update name of poi
 * @param $json array required information
 * @return array result will allways be true
 */
function UpdateNameApi($json)
{
    if (((getValidateSumName($json['id']) < 400) || ($_SESSION['role'] >= config::$ROLE_EMPLOYEE)) == false) {
        return generateSuccess();
    }
    deleteValidateName($json['id']);
    $result = updateName($json['id'], $json['name'], $json['start'], $json['end']);
    return array_merge(generateSuccess(), array('state' => $result));
}

/**
 * gatheres needed information to update name of poi
 * @param $json array required information
 * @return array result will allways be true
 */
function UpdateHistAddrApi($json)
{
    if ($_SESSION['role'] <= config::$ROLE_UNAUTH_USER) {
        return generateError();
    }
    if (((getValidateSumHistAddress($json['id']) < 400) || ($_SESSION['role'] >= config::$ROLE_EMPLOYEE)) == false) {
        return generateSuccess();
    }
    deleteValidateHistAddress($json['id']);
    $result = updateHistAddr($json['id'], $json['start'], $json['end'], $json['streetname'], $json['housenumber'], $json['city'], $json['postalcode']);
    return array_merge(generateSuccess(), array('state' => $result));
}

/**
 * deletes material with request to cosp
 * @param array $json required information
 * @return array result will allways be true
 */
function deleteMaterialApi($json)
{
    if ($_SESSION['role'] <= config::$ROLE_UNAUTH_USER) {
        return generateError();
    }
    $data = GetDataForSingleMaterial($json['token']);
    if (((($data['pic']['validationValue'] < 400) && ($data['pic']['username'] == $_SESSION['username'])) || ($_SESSION['role'] >= config::$ROLE_EMPLOYEE)) == false) {
        return generateSuccess();
    }
    return array_merge(generateSuccess(), deleteMaterial($json['token']));
}

/**
 * gets all title of pois, which are not linked to a certain story yet
 * @param array $json structured request
 * @return array result will contain all needed Information
 */
function getPoiTitleAPI($json)
{
    $data = getPoisTitleForUser();
    $result = array();
    $pois = array();
    $poi_comp = getPoiForStory($json['storytoken'], true);
    foreach ($poi_comp as $poi) {
        $pois[] = $poi['poi_id'];
    }
    foreach ($data as $poi) {
        if (in_array($poi['poi_id'], $pois) == false && $poi['deleted'] == 0) {
            $result[] = $poi;
        }
    }
    return array_merge(generateSuccess(), array('data' => $result));
}

/**
 * inserts link between poi and story into database
 * @param array $json required information
 * @return array state of succes, will always be true
 */
function addStoryPoiLinkApi($json)
{
    if ($_SESSION['role'] <= config::$ROLE_UNAUTH_USER) {
        return generateError();
    }
    if ($_SESSION['role'] >= config::$ROLE_AUTH_USER) {
        $data = insertPoiStory($json['poiid'], $json['storytoken']);
        return array_merge(generateSuccess(), array('data' => $data));
    }
    return generateError();
}

/**
 * gets wanted information about the selected story and points of interest link
 * @param array $json required information
 * @return array result of request
 */
function sendPoiStoryLinkDataApi($json)
{
    $data = getPoiForStory($json['storytoken']);
    for ($i = 0; $i < count($data); $i++) {
        $data[$i]['validated'] = in_array($data[$i]['id'], getValidationsByUserForLinkPoiStory()) || (getValidateSumPoiStory($data[$i]['id']) >= 400);
        $data[$i]['deletable'] = (getValidateSumPoiStory($data[$i]['id']) < 400 && $_SESSION['role'] >= config::$ROLE_AUTH_USER) || ($_SESSION['role'] >= config::$ROLE_EMPLOYEE);
        $data[$i]['deleted'] = $data[$i]['deleted'] == 1;
        $data[$i]['restrictions'] = LinkPoiStoryRestictedStory($data[$i]['id']) || LinkPoiStoryRestictedPOI($data[$i]['id']);
    }
    $result = array();
    if ($_SESSION['role'] < config::$ROLE_AUTH_USER) {
        foreach ($data as $date) {
            if ($date['validated'] == true) {
                $result[] = $date;
            }
        }
    } else {
        $result = $data;
    }
    return array_merge(generateSuccess(), array('data' => array('pois' => $result, "admin" => $_SESSION['role'] >= config::$ROLE_ADMIN, "guest" => $_SESSION['role'] < config::$ROLE_AUTH_USER, 'valpos' => getValidationValue() > 0)));
}

/**
 * validates selected story and points of interest link
 * @param array $json required information
 * @return array result of request, will always be success
 */
function validatePoiStoryLinkDataApi($json)
{
    $story_poi_id = $json['poiStoryId'];
    if ($_SESSION['role'] >= config::$ROLE_AUTH_USER && getValidateSumPoiStory($story_poi_id) < 400 && in_array($story_poi_id, getValidationsByUserForLinkPoiStory()) == false) {
        validatePoiStory($story_poi_id);
    }
    return array_merge($json, generateSuccess());
}

/**
 * deletes selected story and points of interest link
 * @param array $json required information
 * @return array result of request, will always be success
 */
function deletePoiStoryLinkDataApi($json)
{
    if ($_SESSION['role'] <= config::$ROLE_UNAUTH_USER) {
        return generateError();
    }
    $story_poi_id = $json['poiStoryId'];
    if ($_SESSION['role'] >= config::$ROLE_EMPLOYEE || (getValidateSumPoiStory($story_poi_id) < 400 && $_SESSION['role'] >= config::$ROLE_AUTH_USER)) {
        deletePoiStoryLinkByIDDBWrap($story_poi_id);
    }
    return array_merge($json, generateSuccess());
}

/**
 * checks if user is allowed to delete story
 * @param array $json required Information
 * @return array result state as array, will always be true
 */
function deleteUserStoryApi($json)
{
    if ($_SESSION['role'] <= config::$ROLE_UNAUTH_USER) {
        return generateError();
    }
    $coseReq = array("story_token" => $json['story_token']);
    if ($_SESSION['role'] >= config::$ROLE_EMPLOYEE) {
        $coseReq['admin'] = true;
    } else {
        $coseReq['admin'] = false;
    }
    $coseReq['user'] = $_SESSION["username"];
    deleteUserstoryInCose($coseReq);
    return array_merge(generateSuccess(), array("data" => $json));
}

/**
 * checks if Address is already in Use
 * @param array $json required structured information
 * @return array data is true if address is already in database
 */
function CheckAddressApi($json)
{
    $Addresses = array_merge(getCurrentAddresses(), getAllAddresses());
    $cityOrPostalcode = ($json['ct'] !== null && $json['ct'] !== "") && ($json['pc'] !== null && $json['pc'] !== "");
    if (!$cityOrPostalcode) {
        return array_merge(generateSuccess(), array("data" => false, "request" => $json));
    }
    foreach ($Addresses as $adr) {
        $st = false;
        $hn = false;
        $cn = false;
        $pc = false;
        $nullOrEmpty = 0;
        if ($json['st'] == $adr['st']) {
            $st = true;
        } else if ($json['st'] == "" || $json['st'] == null) {
            $st = true;
            $nullOrEmpty += 1;
        }
        if ($json['hn'] == $adr['hn']) {
            $hn = true;
        } else if ($json['hn'] == "" || $json['hn'] == null) {
            $hn = true;
            $nullOrEmpty += 1;
        }
        if ($json['ct'] == $adr['ct']) {
            $cn = true;
        } else if ($json['ct'] == "" || $json['ct'] == null) {
            $cn = true;
            $nullOrEmpty += 1;
        }
        if ($json['pc'] == $adr['pc']) {
            $pc = true;
        } else if ($json['pc'] == "" || $json['pc'] == null) {
            $pc = true;
            $nullOrEmpty += 1;
        }
        if ($pc && $cn && $st && $hn && $nullOrEmpty <= 2) {
            return array_merge(generateSuccess(), array("data" => true, "request" => $json));
        }
    }
    return array_merge(generateSuccess(), array("data" => false, "request" => $json));
}

/**
 * gets a list of pictures from cosp for picture select modal
 * @param bool $incomplete standard value is false, if false array without ack will be returned
 * @return array always a positiv result, even without data
 */
function getAllPicturesListAPI($incomplete = false)
{
    $picList = getRemotePictureList()['pics'];
    $result = array();
    if (count($picList) > 0) {
        for ($i = 0; $i < count($picList); $i++) {
            $pic = $picList[$i];
            $picList[$i]['fullsize'] = config::$USAPI . "?" . http_build_query(array('type' => "gpf", "data" => $pic["token"]["token"], "seccode" => $pic["token"]["seccode"], "time" => $pic["token"]["time"]), '', '&amp;');
            $picList[$i]['preview'] = config::$USAPI . "?" . http_build_query(array('type' => "gpp", "data" => $pic["token"]["token"], "seccode" => $pic["token"]["seccode"], "time" => $pic["token"]["time"]), '', '&amp;');
            if ($_SESSION['role'] >= config::$ROLE_AUTH_USER || ($pic['validationValue'] >= 400)) {
                $result[] = $picList[$i];
            }
        }
    }
    if ($incomplete) {
        return $result;
    }
    return array_merge(generateSuccess(), array("data" => $result));
}

/**
 * adds pictures to poi via database
 * @param array $json structures request data
 * @return array result of request
 */
function addPicturetoPoi($json)
{
    $data = $json['data'];
    $poi = $json['poi'];
    if ($_SESSION['role'] >= config::$ROLE_AUTH_USER) {
        foreach ($data as $date) {
            insertPoiPicture($date, $poi);
        }
        return generateSuccess();
    } else {
        return generateError();
    }
}

/**
 * inserts validate picture poi link
 * @param array $json structured required data
 * @return array structured answer data
 */
function insertValidatePicturePoiApi($json)
{
    $id = $json['id'];
    $validationValue = getValidationValue();
    insertValidatePoiPicLink($id, $validationValue);
    if ($validationValue >= 1) {
        addRankPoints($_SESSION['username'], 2, "Hat einen Link zwischen einer Bild und einem Interessenpunkt validiert.");
    }
    if (getValidateSumPoiPic($id) >= 400) {
        $user = getCreatorByPoiPicId($id);
        addRankPoints($user, 10, "Durch Nutzer wurde Verbindung zwischen Bild und Interessenpunkt validiert.");
    }
    return generateSuccess();
}

/**
 * deletes Link between Poi and Picture
 * @param array $json structured required data
 */
function deletePoiPicLinkApi($json)
{
    $lid = $json['id'];
    $creator = getCreatorByPoiPicId($lid);
    if ($creator !== null && $creator !== "") {
        if (getValidateSumPoiPic($lid) < 400 || $_SESSION['role'] >= config::$ROLE_EMPLOYEE) {
            deletePoiPicLinkByIDDBWrap($lid);
            return generateSuccess();
        }
    }
    return generateError();
}

/**
 * sends information for poi pic link modal on material list
 * @param array $json structured request data
 * @return array structured request answer
 */
function loadPoiPicLinker($json)
{
    $data = getPoisTitleForUser();
    $result = array();
    $linked = array();
    $poiList = getPoisForPic($json['pictoken']);
    $list = array();
    $pois = array();
    foreach ($poiList as $poi) {
        $pois[] = $poi['poiid'];
        $list[$poi['poiid']] = $poi;
    }
    foreach ($data as $poi) {
        unset($poi[0]);
        unset($poi[1]);
        unset($poi[2]);
        unset($poi[3]);
        unset($poi[4]);
        if (in_array($poi['poi_id'], $pois) == false) {
            if ($poi['deleted'] == 0) {
                $result[] = $poi;
            }
        } else {
            $id = getLinkIdPoiPic($poi['poi_id'], $json['pictoken']);
            $validators = GetPoiPicLinkValidators($id);
            $val = getValidateSumPoiPic($id);
            $validated = ($val >= 400) || in_array($_SESSION['username'], $validators);
            $deletable = ($val < 400) || $_SESSION['role'] >= config::$ROLE_EMPLOYEE;
            $deleted = $list[$poi['poi_id']]['deleted'] == 1;
            $restrictions = LinkPoiPicRestictedPic($id) || LinkPoiPicRestictedPOI($id);
            $linked[] = array_merge($poi, array('validated' => $validated, 'deletable' => $deletable, 'lid' => $id, 'deleted' => $deleted, "restrictions" => $restrictions));
        }
    }
    return array_merge(generateSuccess(), array('data' => array('options' => $result, "linked" => $linked, "guest" => $_SESSION['role'] < config::$ROLE_AUTH_USER, 'valpos' => getValidationValue() > 0)));
}

/**
 * return uapi url for api request
 * @return array structured result
 */
function getUapiUrl()
{
    return array_merge(generateSuccess(), array('data' => config::$USAPI));
}

/**
 * Save Information about a new Seat count of a POI
 * gathers information from array and orders it for inserting in database
 * @param array $json array with required Information
 * @return array result is always true
 */
function SaveSeatsNewAPI($json)
{
    if ($_SESSION['role'] >= config::$ROLE_AUTH_USER) {
        insertSeatsOfPOI($json['poi_id'], $json['from'], $json['till'], $json['seats']);
        return array_merge($json, generateSuccess());
    }
    return generateError();
}

/**
 * gatheres needed information to validate seats of poi
 * @param $json array required information
 * @return array result will allways be true
 */
function validatePoiSeatsApi($json)
{
    $seat_id = $json['SEATID'];
    if ($_SESSION['role'] >= config::$ROLE_AUTH_USER && getValidateSumSeats($seat_id) < 400 && in_array($seat_id, getValidationsByUserForPoiSeats()) == false) {
        validatePoiSeats($seat_id);
    }
    return array_merge($json, generateSuccess());
}

/**
 * gatheres needed information to delete seats of poi
 * @param $json array required information
 * @return array result will allways be true
 */
function deleteSeatsApi($json)
{
    if ($_SESSION['role'] <= config::$ROLE_UNAUTH_USER) {
        return generateError();
    }
    $seat_id = $json['IDent'];
    if ($_SESSION['role'] >= config::$ROLE_EMPLOYEE || getValidateSumSeats($seat_id) < 400) {
        deleteSeatsDBWrap($seat_id);
    }
    return array_merge($json, generateSuccess());
}

/**
 * gatheres needed information to update operator of poi
 * @param $json array required information
 * @return array result will allways be true
 */
function UpdateSeatsApi($json)
{
    if ($_SESSION['role'] <= config::$ROLE_UNAUTH_USER) {
        return generateError();
    }
    if (((getValidateSumSeats($json['id']) < 400) || ($_SESSION['role'] >= config::$ROLE_EMPLOYEE)) == false) {
        return generateSuccess();
    }
    deleteValidateSeats($json['id']);
    $result = updateSeats($json['id'], $json['seats'], $json['start'], $json['end']);
    return array_merge(generateSuccess(), array('state' => $result));
}

/**
 * Save Information about a new Cinemas count of a POI
 * gathers information from array and orders it for inserting in database
 * @param array $json array with required Information
 * @return array result is always true
 */
function SaveCinemasNewAPI($json)
{
    if ($_SESSION['role'] >= config::$ROLE_AUTH_USER) {
        insertCinemasOfPOI($json['poi_id'], $json['from'], $json['till'], $json['cinemas']);
        return array_merge($json, generateSuccess());
    }
    return generateError();
}

/**
 * gatheres needed information to validate cinemas of poi
 * @param $json array required information
 * @return array result will allways be true
 */
function validatePoiCinemasApi($json)
{
    $cinema_id = $json['CINEMAID'];
    if ($_SESSION['role'] >= config::$ROLE_AUTH_USER && getValidateSumCinemas($cinema_id) < 400 && in_array($cinema_id, getValidationsByUserForPoiCinemas()) == false) {
        validatePoiCinemas($cinema_id);
    }
    return array_merge($json, generateSuccess());
}

/**
 * gatheres needed information to delete cinemas of poi
 * @param $json array required information
 * @return array result will allways be true
 */
function deleteCinemasApi($json)
{
    if ($_SESSION['role'] <= config::$ROLE_UNAUTH_USER) {
        return generateError();
    }
    $cinema_id = $json['IDent'];
    if ($_SESSION['role'] >= config::$ROLE_EMPLOYEE || getValidateSumCinemas($cinema_id) < 400) {
        deleteCinemasDBWrap($cinema_id);
    }
    return array_merge($json, generateSuccess());
}

/**
 * gatheres needed information to update operator of poi
 * @param $json array required information
 * @return array result will allways be true
 */
function UpdateCinemasApi($json)
{
    if ($_SESSION['role'] <= config::$ROLE_UNAUTH_USER) {
        return generateError();
    }
    if (((getValidateSumCinemas($json['id']) < 400) || ($_SESSION['role'] >= config::$ROLE_EMPLOYEE)) == false) {
        return generateSuccess();
    }
    deleteValidateCinema($json['id']);
    $result = updateCinemas($json['id'], $json['cinemas'], $json['start'], $json['end']);
    return array_merge(generateSuccess(), array('state' => $result));
}

/**
 * gatheres needed information to validate type of poi
 * @param $json array required information
 * @return array result will allways be true
 */
function validateTypeApi($json)
{
    $poiid = $json['POIID'];
    if ($_SESSION['role'] >= config::$ROLE_AUTH_USER && getValidateSumType($poiid) < 400 && in_array($poiid, getValidationsByUserForCinemaType()) == false) {
        validateTypePoi($poiid);
    }
    return array_merge($json, generateSuccess());
}

/**
 * checks if user is guest
 * @return array structured result request
 */
function isUserGuest()
{
    return array_merge(generateSuccess(), array('data' => $_SESSION['role'] < config::$ROLE_AUTH_USER || $_SESSION["username"] === GuestAuthData()['name']));
}

/**
 * transforms data in correkt format for javascript
 * @param array $json structured query information
 * @return array structured result data
 */
function getStatisticalDataAPI($json)
{
    $result = "";
    if ($_SESSION['role'] < config::$ROLE_EMPLOYEE) {
        return generateError();
    }
    switch ($json['data']['src']) {
        case 'login':
            $result = loginStatistics($json['data']);
            break;
        case 'poisc':
            $result = CreateStatistics($json['data'], "poi");
            break;
        case "comm":
            $result = CreateStatistics($json['data'], "com");
            break;
        case "poival":
            $result = CreatePoiValidationStats();
            break;
    }
    return array_merge(generateSuccess(), array('data' => $result));
}

/**
 * approves user story
 * @param array $json structured request data
 * @return array structured result, false if user is no employee
 */
function approveUserStoryAPI($json)
{
    if ($_SESSION['role'] >= config::$ROLE_EMPLOYEE) {
        SendStoryApprovalChange($json['story_token'], true);
        return generateSuccess();
    }
    return generateError();
}

/**
 * disapproves user story
 * @param array $json structured request data
 * @return array structured result, false if user is no employee
 */
function disapproveUserStoryAPI($json)
{
    if ($_SESSION['role'] >= config::$ROLE_EMPLOYEE) {
        SendStoryApprovalChange($json['story_token'], false);
        return generateSuccess();
    }
    return generateError();
}

/**
 * sends data if stories are enabled
 * @return array structured result data
 */
function GetStateOfStories()
{
    return array_merge(generateSuccess(), array('data' => config::$ENABLE_STORIES));
}

/**
 * structures request for captcha
 */
function GetCaptchaAPI()
{
    $result = GetCaptchaFromCose();
    $_SESSION['captcha'] = $result['code'];
    return array_merge(array("data" => $result['captcha']), generateSuccess());
}

/**
 * sends a contactmail to admin mailadress of Cosp
 * @param array $json structured request
 * @return array result of mailsending
 */
function sendContactMessageAPI($json)
{
    $mail = "";
    if (isset($json['email'])) {
        $mail = $json['email'];
    }
    if (isset($_SESSION['email'])) {
        $mail = $_SESSION['email'];
    }
    if (isset($_SESSION['captcha']) == false) {
        return generateError2(array(
            "cap" => true,
            "msg" => $json['msg'] == "" || $json['msg'] == null,
            "title" => $json['title'] == null || $json['title'] == "",
            "mail" => $mail == "" || checkMailAddress($mail) == false
        ));
    }
    if ($_SESSION['captcha'] !== $json['cap']) {
        return generateError2(array(
            "cap" => true,
            "msg" => $json['msg'] == "" || $json['msg'] == null,
            "title" => $json['title'] == null || $json['title'] == "",
            "mail" => $mail == "" || checkMailAddress($mail) == false
        ));
    }
    if ($mail == "" || checkMailAddress($mail) == false) {
        return generateError2(array(
            "cap" => false,
            "msg" => $json['msg'] == "" || $json['msg'] == null,
            "title" => $json['title'] == null || $json['title'] == "",
            "mail" => true
        ));
    }
    if ($json['msg'] == "" || $json['msg'] == null || $json['title'] == null || $json['title'] == "") {
        return generateError2(array(
            "cap" => false,
            "msg" => $json['msg'] == "" || $json['msg'] == null,
            "title" => $json['title'] == null || $json['title'] == "",
            "mail" => false
        ));
    }
    $msg = $json['msg'];
    $subject = $json['title'];
    sendContactMail($mail, $subject, $msg);
    unset($_SESSION['captcha']);
    return generateSuccess();
}

/**
 * final deletion of poi pic link
 * @param array $json structured request
 * @return array structured result
 */
function finalDeletePoiPic($json)
{
    if ($_SESSION['role'] >= config::$ROLE_ADMIN) {
        deletePoiPicLinkByIDDBWrap($json['IDent'], true);
        return generateSuccess();
    }
    return generateError('Power you need.');
}

/**
 * restore link between pic and poi
 * @param array $json structured request
 * @return array structured result
 */
function RestorePoiPicLink($json)
{
    if ($_SESSION['role'] >= config::$ROLE_ADMIN) {
        updateDeletionStateLinkPoiPicByID($json['IDent'], false);
        return generateSuccess();
    }
    return generateError('Power you need.');
}

/**
 * restores a Name of a POI
 * @param array $json structured request
 * @return array structured result
 */
function RestorePoiName($json)
{
    if ($_SESSION['role'] >= config::$ROLE_ADMIN) {
        updateDeletionStateNamesByID($json['IDent'], false);
        return generateSuccess();
    }
    return generateError('Power you need.');
}

/**
 * final deletion of a poi name
 * @param array $json structured request
 * @return array structured result
 */
function FinalDeletePoiName($json)
{
    if ($_SESSION['role'] >= config::$ROLE_ADMIN) {
        deleteNamesDBWrap($json['IDent'], true);
        return generateSuccess();
    }
    return generateError('Power you need.');
}

/**
 * restores a Name of a POI
 * @param array $json structured request
 * @return array structured result
 */
function RestorePoiOperator($json)
{
    if ($_SESSION['role'] >= config::$ROLE_ADMIN) {
        updateDeletionStateOperatorsById($json['IDent'], false);
        return generateSuccess();
    }
    return generateError('Power you need.');
}

/**
 * final deletion of a poi name
 * @param array $json structured request
 * @return array structured result
 */
function FinalDeletePoiOperator($json)
{
    if ($_SESSION['role'] >= config::$ROLE_ADMIN) {
        deleteOperatorsDBWrap($json['IDent'], true);
        return generateSuccess();
    }
    return generateError('Power you need.');
}

/**
 * restores a seat count of a POI
 * @param array $json structured request
 * @return array structured result
 */
function RestorePoiSeats($json)
{
    if ($_SESSION['role'] >= config::$ROLE_ADMIN) {
        updateDeletionStateSeatsById($json['IDent'], false);
        return generateSuccess();
    }
    return generateError('Power you need.');
}

/**
 * final deletion of a poi seat count
 * @param array $json structured request
 * @return array structured result
 */
function FinalDeletePoiSeats($json)
{
    if ($_SESSION['role'] >= config::$ROLE_ADMIN) {
        deleteSeatsDBWrap($json['IDent'], true);
        return generateSuccess();
    }
    return generateError('Power you need.');
}

/**
 * restores a cinema count of a POI
 * @param array $json structured request
 * @return array structured result
 */
function RestorePoiCinemas($json)
{
    if ($_SESSION['role'] >= config::$ROLE_ADMIN) {
        updateDeletionStateCinemasById($json['IDent'], false);
        return generateSuccess();
    }
    return generateError('Power you need.');
}

/**
 * final deletion of a poi cinema count
 * @param array $json structured request
 * @return array structured result
 */
function FinalDeletePoiCinemas($json)
{
    if ($_SESSION['role'] >= config::$ROLE_ADMIN) {
        deleteCinemasDBWrap($json['IDent'], true);
        return generateSuccess();
    }
    return generateError('Power you need.');
}

/**
 * restores a historical address of a POI
 * @param array $json structured request
 * @return array structured result
 */
function RestorePoiHistAddr($json)
{
    if ($_SESSION['role'] >= config::$ROLE_ADMIN) {
        updateDeletionStateHistAddressById($json['IDent'], false);
        return generateSuccess();
    }
    return generateError('Power you need.');
}

/**
 * final deletion of a poi historical address
 * @param array $json structured request
 * @return array structured result
 */
function FinalDeletePoiHistAddr($json)
{
    if ($_SESSION['role'] >= config::$ROLE_ADMIN) {
        deleteHistAddressDBWrap($json['IDent'], true);
        return generateSuccess();
    }
    return generateError('Power you need.');
}

/**
 * restores a link of a POI and a story
 * @param array $json structured request
 * @return array structured result
 */
function RestorePoiStoryLink($json)
{
    if ($_SESSION['role'] >= config::$ROLE_ADMIN) {
        updateDeletionStateLinkPoiStoryByID($json['IDent'], false);
        return generateSuccess();
    }
    return generateError('Power you need.');
}

/**
 * final deletion of a link between a poi and a story
 * @param array $json structured request
 * @return array structured result
 */
function FinalDeletePoiStoryLink($json)
{
    if ($_SESSION['role'] >= config::$ROLE_ADMIN) {
        deletePoiStoryLinkByIDDBWrap($json['IDent'], true);
        return generateSuccess();
    }
    return generateError('Power you need.');
}

/**
 * restores a link of a POI and a story
 * @param array $json structured request
 * @return array structured result
 */
function RestorePoiComment($json)
{
    if ($_SESSION['role'] >= config::$ROLE_ADMIN) {
        updateDeletionStateCommentById($json['IDent'], false);
        return generateSuccess();
    }
    return generateError('Power you need.');
}

/**
 * final deletion of a link between a poi and a story
 * @param array $json structured request
 * @return array structured result
 */
function FinalDeletePoiComment($json)
{
    if ($_SESSION['role'] >= config::$ROLE_ADMIN) {
        deleteCommentsDBWrap($json['IDent'], true);
        return generateSuccess();
    }
    return generateError('Power you need.');
}

/**
 * restores a a POI
 * @param array $json structured request
 * @return array structured result
 */
function RestorePoiAPI($json)
{
    if ($_SESSION['role'] >= config::$ROLE_ADMIN) {
        restorePOI($json['IDent']);
        return generateSuccess();
    }
    return generateError('Power you need.');
}

/**
 * final deletion of a poi
 * @param array $json structured request
 * @return array structured result
 */
function FinalDeletePoi($json)
{
    if ($_SESSION['role'] >= config::$ROLE_ADMIN) {
        deletePOIComplete($json['IDent'], true);
        return generateSuccess();
    }
    return generateError('Power you need.');
}

/**
 * restores a story
 * @param array $json structured request
 * @return array structured result
 */
function RestoreStoryAPI($json)
{
    if ($_SESSION['role'] >= config::$ROLE_ADMIN) {
        ApiCall(array('IDent' => $json['IDent']), "rst");
        return generateSuccess();
    }
    return generateError('Power you need.');
}

/**
 * final deletion of a story
 * @param array $json structured request
 * @return array structured result
 */
function FinalDeleteStory($json)
{
    if ($_SESSION['role'] >= config::$ROLE_ADMIN) {
        ApiCall(array('IDent' => $json['IDent']), "fst");
        return generateSuccess();
    }
    return generateError('Power you need.');
}

/**
 * restores a story
 * @param array $json structured request
 * @return array structured result
 */
function RestorePictureAPI($json)
{
    if ($_SESSION['role'] >= config::$ROLE_ADMIN) {
        $token = explode(";", $json['IDent'])[0];
        ApiCall(array('IDent' => $token), "rpc");
        return generateSuccess();
    }
    return generateError('Power you need.');
}

/**
 * final deletion of a story
 * @param array $json structured request
 * @return array structured result
 */
function FinalPictureStory($json)
{
    if ($_SESSION['role'] >= config::$ROLE_ADMIN) {
        $token = explode(";", $json['IDent'])[0];
        ApiCall(array('IDent' => $token), "fpc");
        return generateSuccess();
    }
    return generateError('Power you need.');
}


/**
 * adds a new announcement to kino tool
 * @param array $json structured request
 * @return array structured result
 */
function addAnnouncementAPI($json)
{
    if ($_SESSION['role'] < config::$ROLE_EMPLOYEE) {
        return generateError();
    }
    $title = $json['title'];
    $content = $json['content'];
    $start = $json['start'];
    $end = $json['end'];
    addAnnouncement($title, $content, $start, $end);
    return generateSuccess();
}

/**
 * get Data for a single announcement
 * @param array $json structured request
 * @return array structured result
 */
function getAnnouncementAPI($json)
{
    if ($_SESSION['role'] < config::$ROLE_EMPLOYEE) {
        return generateError();
    }
    $ann = getAnnouncement($json['id']);
    dump($ann, 8);
    $resultPart = array(
        "id" => $ann['id'],
        "title" => $ann['title'],
        "content" => $ann['content'],
        "start" => $ann['start'],
        "end" => $ann['end'],
    );
    return array_merge(generateSuccess(), array("data" => $resultPart));
}

/**
 * updates a certain announcement
 * @param array $json structured request
 * @return array structured result
 */
function updateAnnouncementAPI($json)
{
    if ($_SESSION['role'] < config::$ROLE_EMPLOYEE) {
        return generateError();
    }
    updateAnnouncement($json['id'], $json['title'], $json['content'], $json['start'], $json['end']);
    return generateSuccess();
}

/**
 * deletes a certain announcement
 * @param array $json structured request
 * @return array structured result
 */
function deleteAnnouncementAPI($json)
{
    if ($_SESSION['role'] < config::$ROLE_EMPLOYEE) {
        return generateError();
    }
    deleteAnnouncement($json['id']);
    return generateSuccess();
}

/**
 * get current announcement
 * @return array structured result
 */
function getCurrentAnnouncementsAPI()
{
    if ($_SESSION['role'] < config::$ROLE_AUTH_USER) {
        return array_merge(generateSuccess(), array("data" => array()));
    }
    $announcements = getCurrentAnnouncement();
    $resultPart = array();
    foreach ($announcements as $announcement) {
        $enddate = strtotime($announcement['end']);
        $today = time();
        $interval = $enddate - $today;
        $resultPart[] = array(
            "id" => $announcement['id'],
            "title" => $announcement['title'],
            "content" => $announcement['content'],
            "end" => round($interval / (60 * 60 * 24))
        );
    }
    return array_merge(generateSuccess(), array("data" => $resultPart));
}

/**
 * updates activation state of announcement
 * @param array $json structured request
 * @return array structured result
 */
function setAktivationAnnouncement($json)
{
    if ($_SESSION['role'] < config::$ROLE_EMPLOYEE) {
        return generateError();
    }
    updateAktivationStateAnnouncement($json['id'], $json['state']);
    return generateSuccess();
}

/**
 * adds new source to a point of interest
 * @param array $json structured request
 * @return array structured result
 */
function addSourcePoiAPI($json)
{
    if ($_SESSION['role'] < config::$ROLE_AUTH_USER) {
        return generateError();
    }
    if (insertSourceOfPOI($json['typeSource'], $json['source'], $json['relation'], $json['poiid'])) {
        return generateSuccess();
    } else {
        return generateError();
    }
}

/**
 * gets all source information for certain point of interest
 * @param array $json structured request
 * @return array structured result
 */
function getSourcePoiAPI($json)
{
    $sources = getSourceOfPoi($json['poiid']);
    $result = array();
    foreach ($sources as $source) {
        $result[] = array(
            "id" => $source['id'],
            "type" => $source['type'],
            "typeid" => $source['typeid'],
            "source" => $source['source'],
            "relation" => $source['relation'],
            "relationid" => $source['relationid'],
            "editable" => $source['username'] == $_SESSION['username'] || $_SESSION['role'] >= config::$ROLE_EMPLOYEE,
            "deleted" => $source['deleted'] == 1,
        );
    }
    return array_merge(generateSuccess(), array('data' => $result));
}

/**
 * gets all source relations
 * @return array structured result
 */
function getSourceRelationsAPI()
{
    $relations = getAllSourceRelations();
    $result = array();
    foreach ($relations as $relation) {
        $result[] = array(
            "id" => $relation['id'],
            "name" => $relation['name'],
        );
    }
    return array_merge(generateSuccess(), array("data" => $result));
}

/**
 * get all source types
 * @return array structured result
 */
function getSourceTypeAPI()
{
    $types = getAllSourceTypes();
    $result = array();
    foreach ($types as $type) {
        $result[] = array(
            "id" => $type['id'],
            "name" => $type['name'],
        );
    }
    return array_merge(generateSuccess(), array("data" => $result));
}

/**
 * updates a source of a point of interest
 * @param array $json structured request
 * @return array structured result
 */
function updateSourcePoiAPI($json)
{
    $src = getSource($json['id']);
    if ($_SESSION['role'] < config::$ROLE_EMPLOYEE && $src['username'] !== $_SESSION['username']) {
        return generateError();
    }
    if (updateSource($json['id'], $json['relation'], $json['source'], $json['typeSource'])) {
        return generateSuccess();
    }
    return generateError();
}

/**
 * deletes a source or marks it as deleted
 * @param array $json structured request
 * @return array structured result
 */
function deleteSourceAPI($json)
{
    $src = getSource($json['id']);
    if ($_SESSION['role'] < config::$ROLE_EMPLOYEE && $src['username'] !== $_SESSION['username']) {
        return generateError();
    }
    if (deleteSourceDBWrap($json['id'])) {
        return generateSuccess();
    }
    return generateError();
}

/**
 * deletes a source finally
 * @param array $json structured request
 * @return array structured result
 */
function finalDeleteSourceAPI($json)
{
    if ($_SESSION['role'] < config::$ROLE_ADMIN) {
        return generateError();
    }
    if (deleteSourceDBWrap($json['id'], true)) {
        return generateSuccess();
    }
    return generateError();
}

/**
 * restores a certain source
 * @param array $json structured request
 * @return array structured result
 */
function restoreSourceApi($json)
{
    if ($_SESSION['role'] < config::$ROLE_ADMIN) {
        return generateError();
    }
    if (updateSourceDeletionState($json['id'], false)) {
        return generateSuccess();
    }
    return generateError();
}

/**
 * adds new validation to point of interest
 * @param array $json structured request
 * @return array structured result
 */
function validatePoiAPI($json)
{
    $validationsByUser = getValidationsByUserForPOI();
    $validation = getValidateSumForPOI($json['id']);
    dump($validation, 3);
    if ($_SESSION['role'] >= config::$ROLE_AUTH_USER && $validation < 400 && in_array($json['id'], $validationsByUser) == false) {
        validatePoi($json['id']);
        return generateSuccess();
    }
    return generateError();
}

/**
 * returns information if direct delete is activated
 * @return array structured result
 */
function getDirectDelete()
{
    return array_merge(generateSuccess(), array('data' => config::$DIRECT_DELETE));
}

/**
 * changes main picture of point of interesst
 * @param array $json structured request
 * @return array structured result
 */
function changeMainPicturePoi($json)
{
    if ($_SESSION['role'] < config::$ROLE_AUTH_USER) {
        return generateError();
    }
    $poiValidated = getValidateSumForPOI($json['poiid']) >= 400 && $_SESSION['role'] < config::$ROLE_EMPLOYEE;
    if ($poiValidated) {
        return generateError();
    }
    updatePicForPoi($json['token'], $json['poiid']);
    deletevalidateByPOI($json['poiid']);
    updatePoiCreator($json['poiid']);
    return generateSuccess();
}

/**
 * checks if mailadress is already used
 * @param array $json structured request
 * @return array structured result
 */
function checkMailAddressExistentAPI($json)
{
    $return = checkMailAddressExistent($json['mail']);
    return array_merge(generateSuccess(), array("data" => $return));
}

/**
 * checks if a username is existend or not
 * @param array $json structured request
 * @return array structured result
 */
function checkUsernameAPI($json)
{
    $username = $json['username'];
    $userExists = false;
    $usernames = getAllUsernames();
    if (in_array($username, $usernames)) {
        $userExists = true;
    }
    return array_merge(generateSuccess(), array("payload" => $userExists));
}

function insertBrowserdataApi()
{
    $browserinfo = detectBrowser();
    $result = insertBrowserData($browserinfo['name'], $browserinfo['version'], $browserinfo['platform'], $browserinfo['userAgent'], $browserinfo['hrname']);
    return array_merge(generateSuccess(), array('data' => $result));
}