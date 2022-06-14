<?php
/**
 * Page to edit basic data of a certain poi
 *
 * @package default
 */

/**
 * @const enables loading of other files without dying to improve security
 */
define('NICE_PROJECT', true);
require_once "bin/inc.php";
if (isset($_SESSION["username"]) === false) {
    permissionDenied();
}
checkPermission(config::$ROLE_AUTH_USER);
$poienable = false;
$map = false;
if ($_SERVER['REQUEST_METHOD'] == 'GET' || count($_GET) > 0) {
    if (count($_GET) > 0) {
        dump($_GET, 8);
        $poiid = "";
        if (isset($_GET['poi'])) {
            $poiid = filter_input(INPUT_GET, 'poi');
            $poi = getPoi($poiid);
            if (isset($poi["picture"]) || $poi["picture"] != "") {
                $seccode = getRemoteSeccode($poi["picture"]);
                $poi["picture"] = config::$USAPI . "?" . http_build_query(array("type" => "gpf", "data" => $seccode["token"], "seccode" => $seccode["seccode"], "time" => $seccode["time"]), '', '&');
            }
            $poi['duty'] = $poi['duty'] == 1;
            $poienable = true;
            dump($poi, 3);
        }
        if (isset($_GET['map'])) {
            $map = filter_input(INPUT_GET, 'map');
            $map = $map == '1';
        }
    }
}
if (!$poienable) {
    Redirect('poimgmt.php', false);
}
$poiValidated = getValidateSumForPOI($poiid) >= 400 && $_SESSION['role'] < config::$ROLE_EMPLOYEE;
$timespanValidated = getValidateSumTimespan($poiid) >= 400 && $_SESSION['role'] < config::$ROLE_EMPLOYEE;
$curAddrValidated = getValidateSumCurAddresse($poiid) >= 400 && $_SESSION['role'] < config::$ROLE_EMPLOYEE;
$HistValidated = getValidateSumHist($poiid) >= 400 && $_SESSION['role'] < config::$ROLE_EMPLOYEE;
$TypeValalidated = getValidateSumType($poiid) >= 400 && $_SESSION['role'] < config::$ROLE_EMPLOYEE;
if ($_SERVER['REQUEST_METHOD'] == 'POST' || count($_POST) > 0) {
    if (count($_POST) > 0) {
        dump($_POST);
        if (isset($_POST['submitrequest'])) {
            $rpost = array();
            if (isset($poiid)) {
                foreach (array_keys($_POST) as $key) {
                    $rpost[$key] = filter_input(INPUT_POST, $key);
                }
                $rpost['id'] = $poiid;
                $poi = getPoi($poiid);
                $poi['duty'] = $poi['duty'] == 1;
                if (isset($rpost['duty'])) {
                    $rpost['duty'] = ($rpost['duty'] == "on");
                } else {
                    $rpost['duty'] = false;
                }
                if ($timespanValidated) {
                    $rpost['start'] = $poi['start'];
                    $rpost['end'] = $poi['end'];
                    $rpost['duty'] = $poi['duty'];
                } else {
                    if ($rpost['start'] != $poi['start'] || $rpost['end'] != $poi['end'] || $rpost['duty'] != $poi['duty']) {
                        deleteValidateTimeSpan($poiid);
                        updatePoiCreatorTimespan($poiid);
                    }
                }
                if ($HistValidated) {
                    $rpost['history'] = $poi['history'];
                } else {
                    if ($rpost['history'] != $poi['history']) {
                        deleteValidateHistory($poiid);
                        updatePoiCreatorHistory($poiid);
                    }
                }
                if ($curAddrValidated) {
                    $rpost['postalcode'] = $poi['Postalcode'];
                    $rpost['streetname'] = $poi['Streetname'];
                    $rpost['housenumber'] = $poi['Housenumber'];
                    $rpost['city'] = $poi['City'];
                } else {
                    if ($rpost['postalcode'] != $poi['Postalcode'] || $rpost['streetname'] != $poi['Streetname'] || $rpost['housenumber'] != $poi['Housenumber'] || $rpost['city'] != $poi['City']) {
                        deleteValidateCurAddress($poiid);
                        updatePoiCreatorCurrentAddress($poiid);
                    }
                }
                if ($poiValidated) {
                    $rpost['name'] = $poi['name'];
                    $rpost['lng'] = $poi['lng'];
                    $rpost['lat'] = $poi['lat'];
                } else {
                    if ($rpost['name'] != $poi['name'] || $rpost['lng'] != $poi['lng'] || $rpost['lat'] != $poi['lat']) {
                        deletevalidateByPOI($poiid);
                        updatePoiCreator($poiid);
                    }
                }
                if ($TypeValalidated) {
                    $rpost['type'] = $poi['type'];
                } else {
                    if ($rpost['type'] != $poi['type']) {
                        deleteValidateType($poiid);
                        updatePoiCreatorType($poiid);
                    }
                }
                updatePoi($rpost);
                if ($rpost['blog'] != $poi['blog']) {
                    updateBlogPoi($poiid, $rpost['blog']);
                }
                if ($map) {
                    setcookie('OpenPoi', $poiid);
                    setcookie('OpenComment', -1);
                    Redirect('map.php');
                }
                Redirect('poimgmt.php');
            }
        } else if (isset($_POST['abortrequest'])) {
            if ($map) {
                setcookie('OpenPoi', $poiid);
                setcookie('OpenComment', -1);
                Redirect('map.php');
            }
            Redirect('poimgmt.php');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <?php
    //<!-- The above 2 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    generateHeaderTags(
        array(
            array(
                "type" => "script",
                "typeval" => "text/javascript",
                "href" => "js/Marker.js",
                "hrefmin" => "js/Marker.min.js"
            ),
            array(
                "type" => "script",
                "typeval" => "text/javascript",
                "href" => "js/MarkerFunctions.js",
                "hrefmin" => "js/MarkerFunctions.min.js"
            ),
            array(
                "type" => "script",
                "typeval" => "text/javascript",
                "href" => "tjs/poiEdit.js",
                "hrefmin" => "tjs/poiEdit.min.js"
            )
        )
    );
    ?>
    <title>Kino Karte - Eintrag ändern</title>
    <script type="text/javascript">
        poiidedit = <?php echo $poiid ?>;
    </script>
</head>

<body style="height: auto">
<?php
generateHeader(true, $lang);
?>
<div class="container text-light navbar-margin pt-4">
    <h1>Eintrag bearbeiten</h1>
    <?php
    dump($_SERVER['REQUEST_METHOD'], 3);
    dump($_POST, 3);
    $urlParams = array(
        'poi' => $poiid
    );
    if ($map) {
        $urlParams['map'] = 1;
    }
    ?>
    <form action="editPoi.php?<?php echo http_build_query($urlParams, '', '&amp;'); ?>"
          method="post" name="poiedit">
        <div class="row d-flex justify-content-center">
            <div class="col-lg-6">
                <label class="weiß2">Name (Pflichtfeld)</label>
                <input class="form-control textinput mb-3" type="text" name="name"
                       value="<?php echo $poienable ? $poi['name'] : "" ?>"
                       required <?php echo $poiValidated ? "disabled" : ""; ?>>

                <label class="weiß2">Betrieb von</label>
                <input class="form-control textinput mb-3" type="text" name="start"
                       value="<?php echo $poienable ? $poi['start'] : "" ?>" <?php echo $timespanValidated ? "disabled" : ""; ?>>

                <label class="weiß2">Betrieb bis</label>
                <input class="form-control textinput mb-2" type="text" name="end"
                       value="<?php echo $poienable ? $poi['end'] : "" ?>" <?php echo $timespanValidated ? "disabled" : ""; ?>>
                <input type="checkbox"
                       class="form-check-input mb-3"
                       id='duty' name='duty'
                       style="color: #ffffff; margin-left: 0rem;" <?php echo $poi['duty'] ? "checked" : ""; ?>>
                <label for="duty"
                       class="weiß2 mb-3"
                       style="margin-left: 1.5rem">noch in Betrieb</label>

                <label for="streetname" class="weiß2">Addresse</label>
                <div class="form-group">
                    <input type="text"
                           class="form-control textinput"
                           id='streetname' name='streetname'
                           style="width: 83%; display: initial;"
                           placeholder="Musterstraße"
                           value="<?php echo $poienable && $poi['Streetname'] !== "" ? $poi['Streetname'] : "" ?>" <?php echo $curAddrValidated ? "disabled" : ""; ?>>
                    <input type="text" class="form-control textinput"
                           id='housenumber' name='housenumber'
                           style="width: 15%;display: initial;"
                           placeholder="12a"
                           value="<?php echo $poienable && $poi['Housenumber'] !== "" ? $poi['Housenumber'] : "" ?>" <?php echo $curAddrValidated ? "disabled" : ""; ?>>
                    <input type="number" minlength="5" maxlength="5"
                           class="form-control textinput mt-1"
                           id='postalcode' name='postalcode'
                           style="width: 25%; display: initial;"
                           placeholder="00001"
                           value="<?php echo $poienable && $poi['Postalcode'] !== "" ? $poi['Postalcode'] : "" ?>" <?php echo $curAddrValidated ? "disabled" : ""; ?>>
                    <input type="text" class="form-control textinput mt-1"
                           id='city' name='city'
                           style="width: 73%;display: initial;"
                           placeholder="Musterstadt"
                           value="<?php echo $poienable && $poi['City'] !== "" ? $poi['City'] : "" ?>" <?php echo $curAddrValidated ? "disabled" : ""; ?>>
                </div>

                <label class="weiß2">Kinotyp</label>
                <select id="type" name="type"
                        class="form-control selectinput-formular2 mb-3" <?php echo $TypeValalidated ? "disabled" : ""; ?>>
                    <?php
                    $optionsTypeSelect = getAllCinemaTypes();
                    for ($i = 0; $i < count($optionsTypeSelect); $i++) {
                        ?>
                        <option value="<?php echo $optionsTypeSelect[$i]['id']; ?>" <?php echo $optionsTypeSelect[$i]['id'] == $poi['type'] ? 'selected' : ""; ?>><?php echo $optionsTypeSelect[$i]['name']; ?></option>
                        <?php
                    }
                    ?>
                </select>
                <?php
                if ($_SESSION['role'] >= config::$ROLE_EMPLOYEE) {
                    ?>
                    <label class="weiß2">Blogeintrag</label>
                    <input class="form-control textinput mb-3" type="text" name="blog"
                           value="<?php echo $poienable ? $poi['blog'] : "" ?>">
                    <?php
                }
                ?>
            </div>
            <div class="col-lg-6">

                <label class="weiß2">Geschichte</label>
                <textarea class="form-control textinput mb-3" type="text" name="history"
                          style="height: 203px;" <?php echo $HistValidated ? "disabled" : ""; ?>><?php echo $poienable ? $poi['history'] : "" ?></textarea>

                <div class="hidden">
                    <input id="lat" class="form-control textinput" type="text" name="lat"
                           value="<?php echo $poienable ? $poi['lat'] : "" ?>"
                           required <?php echo $poiValidated ? "disabled" : ""; ?>>
                    <input id="lng" class="form-control textinput" type="text" name="lng"
                           value="<?php echo $poienable ? $poi['lng'] : "" ?>"
                           required <?php echo $poiValidated ? "disabled" : ""; ?>>
                </div>

                <div id="mapframepoi" class="form-control mb-3" style="height: 204px">
                    <script type="text/javascript" src="js/editmap.js"></script>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div>
                    <?php
                    if (isset($poi['picture']) && $poi['picture'] !== "") {
                        ?>
                        <img class="mb-3" style="width: 100%" src="<?php echo $poi['picture']; ?>">
                        <?php
                    }
                    ?>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="d-flex justify-content-end pb-3">
                    <?php
                    if ($poiValidated) {
                        ?>
                        <button class="btn btn-secondary col-3 disabled" name="changePicture"
                                data-title="Ändere das Hauptbild eines Interessenpunktes."
                                data-toggle="tooltip" onclick="" disabled>Bild ändern
                        </button>
                        <?php
                    } else {
                        ?>
                        <a class="btn btn-secondary col-3"
                           data-title="Ändere das Hauptbild eines Interessenpunktes."
                           data-toggle="tooltip" onclick="$('#ChangeMainPicEditModal').modal();">Bild ändern
                        </a>
                        <?php
                    }
                    ?>
                    <input class="btn btn-success importantButton col-3 ml-2"
                           name="submitrequest"
                           type="submit"
                           value="Speichern"
                           onclick="if(!CheckAddress()){return false};">
                    <input class="btn btn-secondary importantButton col-3 ml-2"
                           name="abortrequest"
                           type="submit"
                           value="Abbrechen"
                           onclick="if(!CheckAddress()){return false};">
                </div>
            </div>
    </form>
</div>
<div class="modal fade col-6 offset-3" id="ChangeMainPicEditModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
        <!-- because normal overflow-y: auto is displaying scrollbar next to modal and not on right side of browser window-->
        <div class="modal-content">
            <div class="modal-header d-inline-flex align-items-baseline rounded-top-7">
                <h5 class="modal-title" style="color: white" id="announcementModalMainTitle">Hauptbild ändern</h5>
                <button type="button" class="btn btn-link" data-dismiss="modal">
                    <img src="images/times-solid.svg" width="14px">
                </button>
            </div>
            <div class="modal-body modal-body-unround" style="" id="CookieModalBody">
                <div class="container text-light">
                    <div class="row">
                        <div class="col-4">
                            <button class="btn btn-secondary" onclick="preparePicSelectModal();">Bildauswahl</button>
                        </div>
                        <div class="col-sm">
                            <button class="btn btn-secondary"
                                    onclick="location.href='MaterialUpload.php?<?php echo http_build_query(array('map' => $map, "poi" => $poiid), '', '&amp;') ?>';">
                                Neues Bild
                                hochladen
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>