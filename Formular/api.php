<?php
/**
 * API endpoint for frontend javascript functions
 *
 * @package default
 */

/**
 * @const enables loading of other files without dying to improve security
 */
define('NICE_PROJECT', true);
require_once "../bin/inc-sub.php";
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    permissionDenied();
}
if (key_exists("CONTENT_TYPE", $_SERVER) === false) {
    permissionDenied();
}
if ($_SERVER["CONTENT_TYPE"] !== "application/json") {
    permissionDenied();
}
$input = file_get_contents('php://input');
$json = decode_json($input);
if ($json === null) {
    $json = array();
    foreach (array_keys($_POST) as $key) {
        $json[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_STRING);
    }
}

if (!isset($json['csrf'])){
    Redirect("index.php");
}
if (!checkCSRFtoken($json['csrf'])) {
    Redirect("index.php");
}
if (isset($_SESSION["username"]) == false) {
    if ( ($json['type'] != "cma") && ($json['type'] != "ibd") && ($json['type'] !=  "cue") && ($json['type'] !=  "cpa")) {
        permissionDenied();
    }
}
$result = generateError();
switch ($json['type']) {
    case 'pac': //Data collection for personal area
        $result = PersonalAreaCollection($_SESSION["username"]);
        generateJson($result);
        break;
    case 'duc': //delete User Comment
        $result = deleteUserComment($json['commentid']);
        generateJson($result);
        break;
    case 'auc': //add User Comment
        $result = AddUserComment($json);
        generateJson($result);
        break;
    case 'smd': //Show more Data for certain poi
        $result = selectMoreApi($json);
        generateJson($result);
        break;
    case 'gpu': //get Poi for User
        $result = getPoisForUserApi();
        generateJson($result);
        break;
    case 'mmy': //get Minimal/Maximal Year
        $result = getMinimalMaximalYear();
        generateJson($result);
        break;
    case 'ccp': //ccplatform URL
        $result = getUapiUrl();
        generateJson($result);
        break;
    case 'aus': // insert new User Story
        $result = addUserStory($json);
        generateJson($result);
        break;
    case 'gas':  // get all User Stories at once
        $result = getAllStoriesDataApi();
        generateJson($result);
        break;
    case 'dpi': //delete point of interest
        $result = deletePointOfInterestViaAPI($json);
        generateJson($result);
        break;
    case 'gcs': //get comment as single by ID
        $result = getCommentByCommentID($json);
        generateJson($result);
        break;
    case 'sec': //save edited comment with ID
        $result = saveCommentByID($json);
        generateJson($result);
        break;
    case 'dsm': //data single material
        $result = DataSingleMaterial($json);
        generateJson($result);
        break;
    case 'ssm': //save single Material edit
        $result = saveSingleMaterialViaAPI($json);
        generateJson($result);
        break;
    case 'eus': //edit user story save
        $result = SaveDataForEditedStoryAPI($json);
        generateJson($result);
        break;
    case 'aha': // add historical Address
        $result = SaveHistoricalAddressNewAPI($json);
        generateJson($result);
        break;
    case 'ado': // add operator
        $result = SaveOperatorNewAPI($json);
        generateJson($result);
        break;
    case 'adn': // add name
        $result = SaveNameNewAPI($json);
        generateJson($result);
        break;
    case 'dha': // delete historical Address
        $result = deleteHistAddressApi($json);
        generateJson($result);
        break;
    case 'dop': // delete operator
        $result = deleteOperatorApi($json);
        generateJson($result);
        break;
    case 'dna': // delete name
        $result = deleteNameApi($json);
        generateJson($result);
        break;
    case 'vha': // validate historical Address
        $result = validatePoiHistAddressApi($json);
        generateJson($result);
        break;
    case 'vop': // validate operator
        $result = validatePoiOperatorsApi($json);
        generateJson($result);
        break;
    case 'vna': // validate name
        $result = validatePoiNamesApi($json);
        generateJson($result);
        break;
    case 'vts': // validate time span
        $result = validateTimeSpanApi($json);
        generateJson($result);
        break;
    case 'vca': // validate current address
        $result = validateCurrentAddressApi($json);
        generateJson($result);
        break;
    case 'vhi': // validate history
        $result = validateHistoryApi($json);
        generateJson($result);
        break;
    case 'uha': // update historical Address
        $result = UpdateHistAddrApi($json);
        generateJson($result);
        break;
    case 'uop': // update operator
        $result = UpdateOperatorApi($json);
        generateJson($result);
        break;
    case 'una': // validate name
        $result = UpdateNameApi($json);
        generateJson($result);
        break;
    case 'dsp': //delete Single Picture
        $result = deleteMaterialApi($json);
        generateJson($result);
        break;
    case 'gpt': //get Poi Titles
        $result = getPoiTitleAPI($json);
        generateJson($result);
        break;
    case 'aps': //add poi to story
        $result = addStoryPoiLinkApi($json);
        generateJson($result);
        break;
    case 'gps': //get poi story links by story id
        $result = sendPoiStoryLinkDataApi($json);
        generateJson($result);
        break;
    case 'vps': //validate poi story links
        $result = validatePoiStoryLinkDataApi($json);
        generateJson($result);
        break;
    case 'dps': //delete poi story links
        $result = deletePoiStoryLinkDataApi($json);
        generateJson($result);
        break;
    case 'dus': //delete User Story
        $result = deleteUserStoryApi($json);
        generateJson($result);
        break;
    case 'cha': //checkAddress
        $result = CheckAddressApi($json);
        generateJson($result);
        break;
    case 'gpf': //get pictures list preview and fullsize
        $result = getAllPicturesListAPI();
        generateJson($result);
        break;
    case 'app': //add picturce to poi
        $result = addPicturetoPoi($json);
        generateJson($result);
        break;
    case 'vpp': //validate picture poi link
        $result = insertValidatePicturePoiApi($json);
        generateJson($result);
        break;
    case 'dpp': //delete picture poi link
        $result = deletePoiPicLinkApi($json);
        generateJson($result);
        break;
    case 'lpp': //load poi pic link modal on list material
        $result = loadPoiPicLinker($json);
        generateJson($result);
        break;
    case 'asc': // add new seatcount to poi
        $result = SaveSeatsNewAPI($json);
        generateJson($result);
        break;
    case 'vsc': //validate seat count
        $result = validatePoiSeatsApi($json);
        generateJson($result);
        break;
    case 'dsc': // delete seat count
        $result = deleteSeatsApi($json);
        generateJson($result);
        break;
    case 'usc': // update seat count
        $result = UpdateSeatsApi($json);
        generateJson($result);
        break;
    case 'acc': // add new seatcount to poi
        $result = SaveCinemasNewAPI($json);
        generateJson($result);
        break;
    case 'vcc': //validate seat count
        $result = validatePoiCinemasApi($json);
        generateJson($result);
        break;
    case 'dcc': // delete cinema count
        $result = deleteCinemasApi($json);
        generateJson($result);
        break;
    case 'ucc': // update cinema count
        $result = UpdateCinemasApi($json);
        generateJson($result);
        break;
    case 'vty': // validate type
        $result = validateTypeApi($json);
        generateJson($result);
        break;
    case 'asg': //ask if user is guest
        $result = isUserGuest();
        generateJson($result);
        break;
    case 'gsd': //get statistics Data
        $result = getStatisticalDataAPI($json);
        generateJson($result);
        break;
    case 'asa': //approve user story
        $result = approveUserStoryAPI($json);
        generateJson($result);
        break;
    case 'das': //disapprove user story
        $result = disapproveUserStoryAPI($json);
        generateJson($result);
        break;
    case 'snp': //show more names for poi
        $result = selectNamesPoiAPI($json);
        generateJson($result);
        break;
    case 'sop': //show more operators for poi
        $result = selectOperatorsPoiAPI($json);
        generateJson($result);
        break;
    case 'shp': //show more historical Addresses
        $result = selectHistAddrPoiAPI($json);
        generateJson($result);
        break;
    case 'gue': // question if user is guest
        $result = isGuestAPI();
        generateJson($result);
        break;
    case 'scp': //show more cinemas-count for poi
        $result = selectCinemasPoiAPI($json);
        generateJson($result);
        break;
    case 'ssp': //show more seat-count for poi
        $result = selectSeatsPoiAPI($json);
        generateJson($result);
        break;
    case 'slp': //show more story links for poi
        $result = selectStoriesPoiAPI($json);
        generateJson($result);
        break;
    case 'gsp': //get Stories for option show more
        $result = getStoriesForOptionDropDownShowMoreApi($json);
        generateJson($result);
        break;
    case 'plp': //poi load main picture
        $result = ShowMoreLoadPicture($json);
        generateJson($result);
        break;
    case 'apl': //poi addtional pictures
        $result = ShowMoreLoadAdditionalPictures($json);
        generateJson($result);
        break;
    case 'lcp': //load comments for poi
        $result = ShowMoreComments($json);
        generateJson($result);
        break;
    case 'cse': // sends if stories are disabled
        $result = GetStateOfStories();
        generateJson($result);
        break;
    case 'cpa': //load captcha Code
        $result = GetCaptchaAPI();
        generateJson($result);
        break;
    case 'cmg': //send contact message
        $result = sendContactMessageAPI($json);
        generateJson($result);
        break;
    case 'fdp': //final delete poi pic link
        $result = finalDeletePoiPic($json);
        generateJson(($result));
        break;
    case 'rdp': //restore link poi pic
        $result = RestorePoiPicLink($json);
        generateJson(($result));
        break;
    case 'rna': //restore poi name
        $result = RestorePoiName($json);
        generateJson(($result));
        break;
    case 'fna': //final delete poi name
        $result = FinalDeletePoiName($json);
        generateJson(($result));
        break;
    case 'rop': //restore poi operator
        $result = RestorePoiOperator($json);
        generateJson(($result));
        break;
    case 'fop': //final delete poi operator
        $result = FinalDeletePoiOperator($json);
        generateJson(($result));
        break;
    case 'rsc': //restore poi seat count
        $result = RestorePoiSeats($json);
        generateJson(($result));
        break;
    case 'fsc': //final delete seat count
        $result = FinalDeletePoiSeats($json);
        generateJson(($result));
        break;
    case 'rcc': //restore poi cinema count
        $result = RestorePoiCinemas($json);
        generateJson(($result));
        break;
    case 'fcc': //final delete cinema count
        $result = FinalDeletePoiCinemas($json);
        generateJson(($result));
        break;
    case 'rha': //restore poi historical address
        $result = RestorePoiHistAddr($json);
        generateJson(($result));
        break;
    case 'fha': //final delete historical address
        $result = FinalDeletePoiHistAddr($json);
        generateJson(($result));
        break;
    case 'rsp': //restore poi story link
        $result = RestorePoiStoryLink($json);
        generateJson(($result));
        break;
    case 'fsp': //final delete poi story link
        $result = FinalDeletePoiStoryLink($json);
        generateJson(($result));
        break;
    case 'rcp': //restore poi comment
        $result = RestorePoiComment($json);
        generateJson(($result));
        break;
    case 'fcp': //final delete poi comment
        $result = FinalDeletePoiComment($json);
        generateJson(($result));
        break;
    case 'rpi': //restore poi
        $result = RestorePoiAPI($json);
        generateJson(($result));
        break;
    case 'fpi': //final delete poi
        $result = FinalDeletePoi($json);
        generateJson(($result));
        break;
    case 'rst': //restore story
        $result = RestoreStoryAPI($json);
        generateJson(($result));
        break;
    case 'fst': //final delete story
        $result = FinalDeleteStory($json);
        generateJson(($result));
        break;
    case 'rpc': //restore story
        $result = RestorePictureAPI($json);
        generateJson(($result));
        break;
    case 'fpc': //final delete story
        $result = FinalPictureStory($json);
        generateJson(($result));
        break;
    case 'aan': //add announcement
        $result = addAnnouncementAPI($json);
        generateJson($result);
        break;
    case 'gan': //get Announcement
        $result = getAnnouncementAPI($json);
        generateJson($result);
        break;
    case 'uan': // update announcement
        $result = updateAnnouncementAPI($json);
        generateJson($result);
        break;
    case 'dan': // delete announcement
        $result = deleteAnnouncementAPI($json);
        generateJson($result);
        break;
    case 'gca': // get current announcement
        $result = getCurrentAnnouncementsAPI();
        generateJson($result);
        break;
    case 'saa': // set activation for announcement
        $result = setAktivationAnnouncement($json);
        generateJson($result);
        break;
    case 'asp': // add source for point of interest
        $result = addSourcePoiAPI($json);
        generateJson($result);
        break;
    case 'grp': // get source poi
        $result = getSourcePoiAPI($json);
        generateJson($result);
        break;
    case 'grs': // get source relations
        $result = getSourceRelationsAPI();
        generateJson($result);
        break;
    case 'gts': // get source types
        $result = getSourceTypeAPI();
        generateJson($result);
        break;
    case 'usp': // update source of point of interest
        $result = updateSourcePoiAPI($json);
        generateJson($result);
        break;
    case 'des': //delete source
        $result = deleteSourceAPI($json);
        generateJson($result);
        break;
    case 'fds': //final delete source
        $result = finalDeleteSourceAPI($json);
        generateJson($result);
        break;
    case 'rso': //restore source
        $result = restoreSourceApi($json);
        generateJson($result);
        break;
    case 'vpi': // validate point of interest
        $result = validatePoiAPI($json);
        generateJson($result);
        break;
    case 'ddl': // question if direct delete is active
        $result = getDirectDelete();
        generateJson($result);
        break;
    case 'emp': //edit main picture
        $result = changeMainPicturePoi($json);
        generateJson($result);
        break;
    case 'cma': //check Mailaddress existent
        $result = checkMailAddressExistentAPI($json);
        generateJson($result);
        break;
    case 'ibd': //insert browserdata into database
        $result = insertBrowserdataApi();
        generateJson($result);
        break;
    case 'cue': //check username
        $result = checkUsernameAPI($json);
        generateJson($result);
        break;
    default:
        $result = generateError();
        generateJson($result);
        break;
}