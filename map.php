<?php
/**
 * Page with Map to show Position of pois
 *
 * @package default
 */

/**
 * @const enables loading of other files without dying to improve security
 */
define('NICE_PROJECT', true);
require_once 'bin/inc.php';
if (isset($_SESSION["username"]) == false) {
    $redirect_url = "index.php?" . http_build_query(array("side" => "map", "redirect" => 1), '', '&');;
    Redirect($redirect_url);
    permissionDenied();
}
?>

<!doctype html>
<html>
<head>
    <input type="text" value="<?php echo createCSRFtokenClient() ?>" id="TokenScriptCSRF" hidden>
    <title>Kino Karte</title>
    <?php
    generateHeaderTags(
        array(
            array(
                "type" => "script",
                "typeval" => "text/javascript",
                "href" => "js/pictureUploadNew.js",
                "hrefmin" => "js/pictureUploadNew.min.js"
            ),
            array(
                "type" => "script",
                "typeval" => "text/javascript",
                "href" => "js/search.js",
                "hrefmin" => "js/search.min.js"
            ),
            array(
                "type" => "script",
                "typeval" => "text/javascript",
                "href" => "js/Marker.js",
                "hrefmin" => "js/Marker.min.js"
            ),
            array(
                "type" => "script",
                "typeval" => "text/javascript",
                "href" => "js/mapfunctions.js",
                "hrefmin" => "js/mapfunctions.min.js"
            ),
            array(
                "type" => "script",
                "typeval" => "text/javascript",
                "href" => "tjs/mapFnc.js",
                "hrefmin" => "tjs/mapFnc.min.js"
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
                "href" => "jse/lightbox.js",
                "hrefmin" => "jse/lightbox.min.js"
            ),
            array(
                "type" => "link",
                "rel" => "stylesheet",
                "href" => "csse/lightbox.css",
                "hrefmin" => "css/lightbox.min.css"
            ),
            array(
                "type" => "link",
                "rel" => "stylesheet",
                "href" => "css/timeline.css",
                "hrefmin" => "css/timeline.min.css"
            )
        )
    );

    $GetData = array();
    $token = "";
    if (count($_POST) > 0) {
        if (isset($_POST['name']) === false) {
            permissionDenied("Wrong Keys in Request.");
        }
        if ($_SESSION['role'] < config::$ROLE_UNAUTH_USER) {
            permissionDenied("Sie sind nicht berechtigt Interessenpunkte hinzuzufügen");
        }
        $uploads_dir = 'images/uploadTmp';
        $newName = "";
        if ($_FILES["bild"]["error"] == UPLOAD_ERR_OK) {
            $tmp_name = $_FILES["bild"]["tmp_name"];
            if(function_exists('getimagesize')) {
                if(!@is_array(getimagesize($tmp_name))){
                    permissionDenied("File is no image or imagedata are corrupt.");
                }
            }
            $name = basename($_FILES["bild"]["name"]);
            move_uploaded_file($tmp_name, "$uploads_dir/$name");
            $newName = hash_file('sha512', "$uploads_dir/$name");
            $path_parts = pathinfo($name);
            $newName = $uploads_dir . "/" . $newName . "." . $path_parts['extension'];
            rename("$uploads_dir/$name", $newName);
            $unlinkName = $newName;
            $newName = realpath($newName);
            $name = filter_input(INPUT_POST, 'name');
            $result = UploadPicture($name, null, $newName, $_FILES["bild"]["type"], $_SESSION["username"]);
            unlink($unlinkName);
            $token = $result["token"];
        }

        $start = filter_input(INPUT_POST, 'start');
        $end = filter_input(INPUT_POST, 'end');
        $test_nat1 = false;
        if ((int)$start >= 0 && (int)$end >= 0 && (int)$start <= (int)$end) {
            $test_nat1 = true;
        }
        $test_nat2 = false;
        if (((int)$start >= 0 || $start == "") && ((int)$end >= 0 || $end = "")) {
            $test_nat2 = true;
        }
        if ($test_nat1 || $test_nat2) {
            dump($_POST, 8);
            $data = array(
                "type" => "ipe",
                "name" => filter_input(INPUT_POST, 'name'),
                "streetname" => filter_input(INPUT_POST, 'streetname'),
                "housenumber" => filter_input(INPUT_POST, 'housenumber'),
                "postalcode" => filter_input(INPUT_POST, 'postalcode'),
                "city" => filter_input(INPUT_POST, 'city'),
                "lng" => filter_input(INPUT_POST, 'lng'),
                "lat" => filter_input(INPUT_POST, 'lat'),
                "start" => $start,
                "end" => $end,
                "category" => filter_input(INPUT_POST, 'category'),
                "history" => filter_input(INPUT_POST, 'history'),
                "picture" => $token,
                "ctype" => filter_input(INPUT_POST, 'CinemaTypeSelect'),
                "duty" => false,
            );
            if (isset($_POST['duty'])) {
                $data['duty'] = filter_input(INPUT_POST, 'duty');
                $data['duty'] = $data['duty'] == 'on';
            }
            dump($data, 8);
            $data['username'] = $_SESSION["username"];
            $result2 = array("result2" => insertPoi($data));
            if ($result2 != "") {
                redirect("map.php?" . http_build_query(array('n-step' => "1"), '', '&amp;'));
            }
        } else {
            $message = "Der Eintrag konnte nicht erstellt werden, weil Sie einen ungültigen Zeitraum eingetragen haben. Bitte " .
                "verwenden Sie nur natürliche Zahlen und stellen Sie sicher, dass, sofern Sie beide Daten angegeben haben," .
                " das Startdatum vor dem Enddatum liegt.";
            echo "<script type='text/javascript'>alert('" . $message . "');</script>";
        }
    }
    if (isset($_GET['n-step'])) {
        $n_step = filter_input(INPUT_GET, 'n-step');
        if ($n_step == 1) {
            ?>
            <meta name="n-step" content="show">
            <?php
        }
    }
    ?>
</head>
<body id="map-page" style="overflow: auto;"
      onload="loadMap();checkNStep();CheckCommentShow();CheckFocus();<?php if ($_SESSION['role'] >= config::$ROLE_UNAUTH_USER) {
          echo 'loadMinimap();';
      } ?>">
<?php
generateHeader(isset($_SESSION['username']), $lang, true);
?>
<div id='Kartenframe'>

</div>
<!--Story Modal-->
<?php
if (config::$ENABLE_STORIES) {
    ?>
    <div class="modal fade col-6 offset-3" id="StoryFullMap" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header d-inline-flex align-items-baseline rounded-top-7">
                    <h5 class="modal-title" id="StoryFullTitle" style="color: #ffffff"></h5>
                    <button type="button" class="btn" data-dismiss="modal"
                            id="closeModalFullStoryMap">
                        <img src="images/times-solid.svg" width="14px">
                    </button>
                </div>
                <div class="modal-body rounded-bottom-7" style="color: black">
                    <div id="StoryTextMap" class="mt-3 mr-5 ml-3 text-light">

                    </div>
                    <p id="StoryLongNameDateMap" class="ml-4 mt-3" style="font-size: 0.8em; color: #c2c2c2"></p>
                    <div class="tab-content" id="myTabContent">
                        <button class="btn btn-warning" id="showFullStoryMapBack"
                                style="margin-left:15px;">zurück
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>
<!--Next Step Modal-->
<div class="modal fade col-6 offset-3" id="NextStepMap" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header d-inline-flex align-items-baseline rounded-top-7">
                <h5 class="modal-title" style="color: #ffffff">Mögliche nächste Schritte</h5>
                <button type="button" class="btn" data-dismiss="modal"
                        id="closeModalFullStoryMap">
                    <img src="images/times-solid.svg" width="14px">
                </button>
            </div>
            <div class="modal-body rounded-bottom-7" style="color: black">
                <div class="mt-3 mr-5 ml-3 text-light">
                    Folgendes könnten sie als nächstes machen:
                    <ul>
                        <li>Weitere Kinos auf der Karte eintragen</li>
                        <?php
                        $valValue = getValidationValue();
                        if ($valValue > 0) {
                            ?>
                            <li><a href="poimgmt.php" class="text-light">Ein Kino validieren</a></li>
                            <?php
                        }
                        if (config::$ENABLE_STORIES) {
                            ?>
                            <li><a href="StoryUpload.php" class="text-light">Eine persönliche Geschichte
                                    aufschreiben<?php echo $valValue > 0 ? " oder validieren" : ""; ?></a></li>
                            <?php
                        }
                        ?>
                        <li><a href="MaterialUpload.php" class="text-light">Eine Bild hochladen</a></li>
                        <?php
                        if ($valValue > 0) {
                            ?>
                            <li><a href="ListMaterial.php" class="text-light">Ein Bild validieren</a></li>
                            <?php
                        }
                        ?>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <button class="btn btn-warning"
                                onclick="$('#NextStepMap').modal('hide');window.location.href = window.location.href.substring(0, window.location.href.indexOf('?'));"
                                style="margin-left:15px;">Schließen
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Marker Modal-->
<!-- Modal -->
<div class="modal fade" id="MarkerModalBig" tabindex="-1" role="dialog" style="overflow-y: auto;">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header d-inline-flex align-items-baseline rounded-top-7">
                <h5 class="modal-title" id="showMoreTitle" style="color: #ffffff"></h5>
                <button type="button" class="btn" data-dismiss="modal">
                    <img src="images/times-solid.svg" width="14px">
                </button>
            </div>
            <div class="modal-body rounded-bottom-7">
                <figure class="figure hw center">
                    <img class="picture hw center figure-img" id="pic">
                    <figcaption class="figure-caption text-center caption-white"
                                id="MainPictureCaptionShowMore"></figcaption>
                </figure>
                <div class="containter mt-1 offset-1 col-10">
                    <div class="row">
                        <div class="col">
                            <hr>
                            <div class="d-flex justify-content-between">
                                <h5 style="color: #d2d2d2; margin-top: 10px">Weitere Bilder</h5>
                                <?php
                                if ($_SESSION['role'] >= config::$ROLE_UNAUTH_USER) {
                                    ?>
                                    <div id="addMorePictureDiv">
                                        <div style="float: right">
                                            <input type="button" value="Bilder hinzufügen" class="btn btn-secondary"
                                                   style="float: right;" id="SelectMorePicturesShowMore">
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                            <div id="slideshowShowMore" class="carousel slide" data-ride="carousel">
                                <div class="carousel-inner" id="ShowMoreCarouselItems">

                                </div>
                                <a class="carousel-control-prev" href="#slideshowShowMore" role="button"
                                   data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#slideshowShowMore" role="button"
                                   data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="containter mt-1 offset-1 col-10">
                    <div class="row">
                        <div class="col">
                            <hr>
                            <table class="table table-dark" style="color: #ffffff">
                                <tr class="tablerow">
                                    <th>Zeitraum</th>
                                    <td id="ModalShowMoreTimespan"></td>
                                    <td>
                                        <?php
                                        if ($_SESSION['role'] >= config::$ROLE_UNAUTH_USER) {
                                            ?>
                                            <div id="validateTimespanDiv">
                                                <button onclick=""
                                                        class="btn btn-sq btn-secondary" data-toggle="tooltip"
                                                        data-placement="top" title="Validieren"
                                                        id="ValidateBtnTimeSpanMap">
                                                    <img src="images/check-solid.svg" width="15px"
                                                         style="margin-top: -2px">
                                                </button>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Aktuelle Adresse</th>
                                    <td id="ModalShowMoreCurrentAddress"></td>
                                    <td>
                                        <?php
                                        if ($_SESSION['role'] >= config::$ROLE_UNAUTH_USER) {
                                            ?>
                                            <div id="validateCurrentAddressDiv">
                                                <button onclick=""
                                                        class="btn btn-sq btn-secondary" data-toggle="tooltip"
                                                        data-placement="top" title="Validieren"
                                                        id="ValidateBtnCurrentAddressMap">
                                                    <img src="images/check-solid.svg" width="15px"
                                                         style="margin-top: -2px">
                                                </button>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Typ des Kinos</th>
                                    <td id="ModalShowMoreCinemaType"></td>
                                    <td>
                                        <?php
                                        if ($_SESSION['role'] >= config::$ROLE_UNAUTH_USER) {
                                            ?>
                                            <div id="validateCinematypeDiv">
                                                <button onclick=""
                                                        class="btn btn-sq btn-secondary" data-toggle="tooltip"
                                                        data-placement="top" title="Validieren"
                                                        id="ValidateBtnCinemaType">
                                                    <img src="images/check-solid.svg" width="15px"
                                                         style="margin-top: -2px">
                                                </button>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Historie</th>
                                    <td><p id="ModalShowMoreHistoyEntry" class="overflow-auto"
                                           style="color: #ffffff; max-height: 200px"></p>
                                    </td>
                                    <td>
                                        <?php
                                        if ($_SESSION['role'] >= config::$ROLE_UNAUTH_USER) {
                                            ?>
                                            <div id="validateHistoryDiv">
                                                <button onclick=""
                                                        class="btn btn-sq btn-secondary" data-toggle="tooltip"
                                                        data-placement="top" title="Validieren"
                                                        id="ValidateBtnHistoryMap">
                                                    <img src="images/check-solid.svg" width="15px"
                                                         style="margin-top: -2px">
                                                </button>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Blogeintrag</th>
                                    <td><p id="ModalShowMoreBlogEntry" class="overflow-auto"
                                           style="color: #ffffff; max-height: 200px"></p>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="containter mt-1 offset-1 col-10">
                    <div class="row">
                        <div class="col">
                            <hr>
                            <h5 style="color: #d2d2d2; margin-top: 10px">Namen</h5>
                            <table class="table table-dark" style="color: #ffffff">
                                <thead>
                                <tr class="tablerow">
                                    <th>Von</th>
                                    <th>Bis</th>
                                    <th>Name</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody id="ModalShowMoreNameTitleTable">

                                </tbody>
                                <?php
                                if ($_SESSION['role'] >= config::$ROLE_UNAUTH_USER) {
                                    ?>
                                    <tfoot id="addNamesDiv">
                                    <td>
                                        <input type="number" class="form-control textinput-formular"
                                               style="width: 250px; background-color: #3b3b3b; color: #ffffff"
                                               name="fromNameShowMore" id="fromNameShowMore">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control textinput-formular"
                                               style="width: 250px; background-color: #3b3b3b; color: #ffffff"
                                               name="tillNameShowMore" id="tillNameShowMore">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control textinput-formular"
                                               required="required"
                                               style="width: 250px; background-color: #3b3b3b; color: #ffffff"
                                               name="stringNameShowMore" id="stringNameShowMore">
                                    </td>
                                    <td>
                                        <button onclick="$(this).tooltip('hide'); this.blur(); saveNameShowMore()"
                                                class="btn btn-sq btn-secondary" data-toggle="tooltip"
                                                data-placement="top" title="Speichern" id="ValidateBtnHistory">
                                            <img src="images/save-solid-white.svg" width="15px"
                                                 style="margin-top: -2px">
                                        </button>
                                    </td>
                                    </tfoot>
                                    <?php
                                }
                                ?>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="containter mt-1 offset-1 col-10">
                    <div class="row">
                        <div class="col">
                            <hr>
                            <h5 style="color: #d2d2d2; margin-top: 10px">Betreiber</h5>
                            <table class="table table-dark" style="color: #ffffff">
                                <thead>
                                <tr class="tablerow">
                                    <th>Von</th>
                                    <th>Bis</th>
                                    <th>Betreiber</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody id="ModalShowMoreOperatorTable">

                                </tbody>
                                <?php
                                if ($_SESSION['role'] >= config::$ROLE_UNAUTH_USER) {
                                    ?>
                                    <tfoot id="addOperatorDiv">
                                    <td>
                                        <input type="number" class="form-control textinput-formular"
                                               style="width: 250px; background-color: #3b3b3b; color: #ffffff"
                                               name="fromOperatorShowMore" id="fromOperatorShowMore">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control textinput-formular"
                                               style="width: 250px; background-color: #3b3b3b; color: #ffffff"
                                               name="tillOperatorShowMore" id="tillOperatorShowMore">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control textinput-formular"
                                               required="required"
                                               style="width: 250px; background-color: #3b3b3b; color: #ffffff"
                                               name="stringOperatorShowMore" id="stringOperatorShowMore">
                                    </td>
                                    <td>
                                        <button onclick="$(this).tooltip('hide'); this.blur(); saveOperatorShowMore()"
                                                class="btn btn-sq btn-secondary" data-toggle="tooltip"
                                                data-placement="top" title="Speichern" id="ValidateBtnHistory">
                                            <img src="images/save-solid-white.svg" width="15px"
                                                 style="margin-top: -2px">
                                        </button>
                                    </td>
                                    </tfoot>
                                    <?php
                                }
                                ?>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Table with Seats count -->
                <div class="containter mt-1 offset-1 col-10">
                    <div class="row">
                        <div class="col">
                            <hr>
                            <h5 class="weiß2" style="margin-top: 10px">Sitzplätze</h5>
                            <table class="table table-dark" style="color: #ffffff">
                                <thead>
                                <tr class="tablerow">
                                    <th>Von</th>
                                    <th>Bis</th>
                                    <th>Anzahl der Sitzplätze</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody id="ModalShowMoreSeatsTable">

                                </tbody>
                                <?php
                                if ($_SESSION['role'] >= config::$ROLE_UNAUTH_USER) {
                                    ?>
                                    <tfoot id="addSeatsDiv">
                                    <td>
                                        <input type="number" class="form-control textinput-formular"
                                               style="width: 250px; background-color: #3b3b3b; color: #ffffff"
                                               name="fromSeatsShowMore" id="fromSeatsShowMore">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control textinput-formular"
                                               style="width: 250px; background-color: #3b3b3b; color: #ffffff"
                                               name="tillSeatsShowMore" id="tillSeatsShowMore">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control textinput-formular"
                                               required="required"
                                               style="width: 250px; background-color: #3b3b3b; color: #ffffff"
                                               name="CountSeatsShowMore" id="CountSeatsShowMore">
                                    </td>
                                    <td>
                                        <button onclick="$(this).tooltip('hide'); this.blur(); saveSeatCount();"
                                                class="btn btn-sq btn-secondary" data-toggle="tooltip"
                                                data-placement="top" title="Speichern" id="ValidateBtnSeats">
                                            <img src="images/save-solid-white.svg" width="15px"
                                                 style="margin-top: -2px">
                                        </button>
                                    </td>
                                    </tfoot>
                                    <?php
                                }
                                ?>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Table with cinemas counter -->
                <div class="containter mt-1 offset-1 col-10">
                    <div class="row">
                        <div class="col">
                            <hr>
                            <h5 style="color: #d2d2d2; margin-top: 10px">Kinosäle</h5>
                            <table class="table table-dark" style="color: #ffffff">
                                <thead>
                                <tr class="tablerow">
                                    <th>Von</th>
                                    <th>Bis</th>
                                    <th>Anzahl an Kinosälen</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody id="ModalShowMoreCinemasTable">

                                </tbody>
                                <?php
                                if ($_SESSION['role'] >= config::$ROLE_UNAUTH_USER) {
                                    ?>
                                    <tfoot id="addCinemaDiv">
                                    <td>
                                        <input type="number" class="form-control textinput-formular"
                                               style="width: 250px; background-color: #3b3b3b; color: #ffffff"
                                               name="fromCinemasShowMore" id="fromCinemasShowMore">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control textinput-formular"
                                               style="width: 250px; background-color: #3b3b3b; color: #ffffff"
                                               name="tillCinemasShowMore" id="tillCinemasShowMore">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control textinput-formular"
                                               required="required"
                                               style="width: 250px; background-color: #3b3b3b; color: #ffffff"
                                               name="countCinemasShowMore" id="countCinemasShowMore">
                                    </td>
                                    <td>
                                        <button onclick="$(this).tooltip('hide'); this.blur(); saveCinemaCount();"
                                                class="btn btn-sq btn-secondary" data-toggle="tooltip"
                                                data-placement="top" title="Speichern" id="ValidateBtnCinemas">
                                            <img src="images/save-solid-white.svg" width="15px"
                                                 style="margin-top: -2px">
                                        </button>
                                    </td>
                                    </tfoot>
                                    <?php
                                }
                                ?>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Table with historical addresses -->
                <div class="containter mt-1 offset-1 col-10">
                    <div class="row">
                        <div class="col">
                            <hr>
                            <h5 style="color: #d2d2d2; margin-top: 10px">Historische Adressen</h5>
                            <table class="table table-dark" style="color: #ffffff">
                                <thead>
                                <tr class="tablerow">
                                    <th>Von</th>
                                    <th>Bis</th>
                                    <th>Historische Adresse</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody id="ModalShowMoreHistAddressTable">

                                </tbody>
                                <?php
                                if ($_SESSION['role'] >= config::$ROLE_UNAUTH_USER) {
                                    ?>
                                    <tfoot id="addHistAddressDiv">
                                    <td>
                                        <button onclick="$(this).tooltip('hide'); this.blur(); toggleHistoricalAdressAdd();"
                                                class="btn btn-sq btn-secondary" data-toggle="tooltip"
                                                data-placement="top" title="Addresse eintragen"
                                                id="ValidateBtnHistory">
                                            <img id="showMoreHistAdressDropOutBtn"
                                                 src="images/caret-square-down-regular-white.svg" width="15px"
                                                 style="margin-top: -2px">
                                        </button>
                                    </td>
                                    <td colspan="2">
                                        <div id="toggleHideShowMoreAddress" style="display:none; transition: 0.5s;"
                                             class="container flex-column mx-auto">
                                            <form name="historicalAddress" accept-charset="utf-8">
                                                <div class="form-group row">
                                                    <label for="fromDateShowMore"
                                                           class="col-sm-2 col-form-label">Von:</label>
                                                    <div class="col-sm-10">
                                                        <input type="number" class="form-control textinput-formular"
                                                               name="fromDateShowMore" id="fromDateShowMore"
                                                               style="width: 250px; background-color: #3b3b3b; color: #ffffff">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="tillDateShowMore"
                                                           class="col-sm-2 col-form-label">Bis:</label>
                                                    <div class="col-sm-10">
                                                        <input type="number" class="form-control textinput-formular"
                                                               name="tillDateShowMore" id="tillDateShowMore"
                                                               style="width: 250px; background-color: #3b3b3b; color: #ffffff">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="StreetnameShowMore"
                                                           class="col-sm-2 col-form-label">Adresszeile 1</label>
                                                    <div class="col-sm-4">
                                                        <input type="text" class="form-control textinput-formular"
                                                               name="StreetnameShowMore" id="StreetnameShowMore"
                                                               style="background-color: #3b3b3b; color: #ffffff"
                                                               placeholder="Straßenname">
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <input type="text" class="form-control textinput-formular"
                                                               name="HousenumberShowMore" id="HousenumberShowMore"
                                                               style=" background-color: #3b3b3b; color: #ffffff"
                                                               placeholder="Hausnummer">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="CityShowMore"
                                                           class="col-sm-2 col-form-label">Adresszeile 2</label>
                                                    <div class="col-sm-4">
                                                        <input type="number" class="form-control textinput-formular"
                                                               name="PostalcodeShowMore" id="PostalcodeShowMore"
                                                               style="background-color: #3b3b3b; color: #ffffff"
                                                               placeholder="Postleitzahl">
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <input type="text" class="form-control textinput-formular"
                                                               name="CityShowMore" id="CityShowMore"
                                                               style="background-color: #3b3b3b; color: #ffffff"
                                                               placeholder="Ortsname">
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                    <td>
                                        <div id="toggleHideShowMoreAddressSaveBtn"
                                             style="display:none;transition: 0.5s;">
                                            <button onclick="$(this).tooltip('hide'); this.blur(); saveHistoricalShowMore()"
                                                    class="btn btn-sq btn-secondary" data-toggle="tooltip"
                                                    data-placement="top" title="Speichern" id="ValidateBtnHistory">
                                                <img src="images/save-solid-white.svg" width="15px"
                                                     style="margin-top: -2px">
                                            </button>
                                        </div>
                                    </td>
                                    </tfoot>
                                    <?php
                                }
                                ?>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- table with sources -->
                <div class="containter mt-1 offset-1 col-10">
                    <div class="row">
                        <div class="col">
                            <hr>
                            <h5 style="color: #d2d2d2; margin-top: 10px">Quellen</h5>
                            <table class="table table-dark" style="color: #ffffff">
                                <thead>
                                <tr class="tablerow">
                                    <th scope="row">Typ</th>
                                    <th scope="row">Quelle</th>
                                    <th scope="row">Bezug</th>
                                    <th scope="row"></th>
                                </tr>
                                </thead>
                                <tbody id="ModalShowMoreSourcesTable">
                                </tbody>
                                <?php
                                if ($_SESSION['role'] >= config::$ROLE_UNAUTH_USER) {
                                    ?>
                                    <tfoot id="addSourceDiv">
                                    <td>
                                        <select name="SourceAddTypeSelect" id="SourceAddTypeSelect"
                                                class="form-control dropdown-list">
                                            <?php
                                            $types = getAllSourceTypes();
                                            foreach ($types as $type) {
                                                ?>
                                                <option value="<?php echo $type['id'] ?>"><?php echo $type['name'] ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control textinput-formular"
                                               required="required"
                                               style="background-color: #3b3b3b; color: #ffffff"
                                               name="SourceAddDescriptionInput" id="SourceAddDescriptionInput">
                                    </td>
                                    <td>
                                        <select name="SourceAddRelationSelect" id="SourceAddRelationSelect"
                                                class="form-control dropdown-list">
                                            <?php
                                            $relations = getAllSourceRelations();
                                            foreach ($relations as $relation) {
                                                ?>
                                                <option value="<?php echo $relation['id'] ?>"><?php echo $relation['name'] ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td>
                                        <button onclick="$(this).tooltip('hide'); this.blur(); saveAddNewSourceShowMore();"
                                                class="btn btn-sq btn-secondary" data-toggle="tooltip"
                                                data-placement="top" title="Speichern" id="SourceAddButton">
                                            <img src="images/save-solid-white.svg" width="15px"
                                                 style="margin-top: -2px">
                                        </button>
                                    </td>
                                    </tfoot>
                                    <?php
                                }
                                ?>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Table with stories -->
                <?php
                if (config::$ENABLE_STORIES) {
                    ?>
                    <hr>
                    <div class="containter mt-1 offset-1 col-10">
                        <div class="row">
                            <div class="col">
                                <h5 style="color: #d2d2d2; margin-top: 10px">Geschichten</h5>
                                <table class="table table-dark" style="color: #ffffff">
                                    <thead>
                                    <tr class="tablerow">
                                        <th>Title</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody id="ModalShowMoreStoryTable">

                                    </tbody>
                                    <?php
                                    if ($_SESSION['role'] >= config::$ROLE_UNAUTH_USER) {
                                        ?>
                                        <tfoot id="addStoryDiv">
                                        <tr>
                                            <td>
                                                <div class="form-group">
                                                    <select id="LinkPoiStorySelectMap" name="LinkPoiStorySelectMap"
                                                            class="form-control selectinput-formular"></select>
                                                </div>
                                                <input id="LinkPoiStoryPoiId" name="LinkPoiStoryPoiId" value="-1"
                                                       class="hidden">
                                            </td>
                                            <td>
                                                <button class="btn btn-sq btn-secondary" data-toggle="tooltip"
                                                        data-placement="top" title=""
                                                        data-original-title="Speichern"
                                                        onclick="$(this).tooltip('hide'); this.blur(); saveLinkedPoiMap();">
                                                    <img
                                                            src="images/save-solid-white.svg"
                                                            width="14px"></button>
                                            </td>
                                        </tr>
                                        </tfoot>
                                        <?php
                                    }
                                    ?>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                if ($_SESSION['role'] >= config::$ROLE_UNAUTH_USER) {
                    ?>
                    <hr>
                    <div class="offset-1 col-10 mt-1">
                        <p id="comments"></p>
                        <div id="addCommentDiv">
                            <form class="mt-5" name="formComment" action="#" id="formComment"
                                  onsubmit=""
                                  enctype="multipart/form-data" accept-charset="utf-8">
                                <label for="comment" style="color: #d2d2d2">Kommentar</label>
                                <textarea class="form-control textinput-formular" id="poi_content_comment_map"
                                          name="comment"
                                          rows="5"
                                          required="required"
                                          style="background-color: #3b3b3b; color: #ffffff"></textarea>
                                <input type="number" id="poi_id_comment_map" class="hidden">
                                <button onclick="getCommentFromFormular()" class="btn btn-success"
                                        style="float: right; margin-top:6px; ">
                                    absenden
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>


<!-- Add-Button trigger modal -->
<?php
if ($_SESSION['role'] >= config::$ROLE_UNAUTH_USER) { //ADD POI abfrage
    ?>
    <div class="modal fade" id="AddPOIWarningModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header d-inline-flex align-items-baseline rounded-top-7">
                    <h5 class="modal-title" style="color: #ffffff" id="AddPOI"><?php echo $lang['Poi']; ?></h5>
                    <button type="button" class="btn btn-link" data-dismiss="modal">
                        <img src="images/times-solid.svg" width="14px">
                    </button>
                </div>
                <div class="modal-body rounded-bottom-7" style="overflow: auto;">
                    <h5 style="color: white">Lieber Nutzer,</h5>
                    <p class="weiß2">
                        wir freuen uns, dass Sie einen neuen Eintrag erstellen. Falls Sie einen längeren Text zur
                        Kinogeschichte schreiben möchten, raten wir Ihnen dazu, alles vorab in einem externen Programm
                        wie Word oder Texteditor zu verfassen und den Text dann im Anschluss in das Formular zu
                        kopieren.
                    </p>
                    <h5 style="color: white">Viel Spaß!
                        <span data-toggle="tooltip" data-placement="top" title="Weiter">
                        <button type="button" class="btn btn-sq-sm btn-success ml-3 btn-important" data-toggle="modal"
                                data-target="#AddPOI_btn"
                                onclick="$('#AddStoryWarningModal').modal('hide'); insertCoordinates(false);">
                            <img src="images/chevron-right-solid-white.svg" width="10px">
                        </button>
                    </span>
                    </h5>
                </div>
            </div>
        </div>
    </div>
    <button type="button" class="btn btn-dark disabled" id="addPOIButton" data-toggle="tooltip" data-placement="top"
            data-original-title="Bitte zuerst einen Ort auf der Karte auswählen."
            onclick="" data-target="#AddPOIWarningModal">
        <img src="images/plus-solid.svg" width="30px">
    </button>
    <!-- Modal -->
    <div class="modal fade" id="AddPOI_btn" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header d-inline-flex align-items-baseline rounded-top-7">
                    <h5 class="modal-title" id="AddPOI" style="color: #ffffff"><?php echo $lang['Poi']; ?></h5>
                    <button type="button" class="btn btn-link" data-dismiss="modal">
                        <img src="images/times-solid.svg" width="14px">
                    </button>
                </div>
                <div class="modal-body rounded-bottom-7" style="overflow: auto;">
                    <div class="row">
                        <div class="col-2">
                            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist">
                                <a class="nav-link active" id="v-pills-cinema-tab" data-toggle="pill"
                                   href="#v-pills-cinema" role="tab"
                                   style="color: black;"><?php echo $lang['Spielstätte']; ?></a>
                            </div>
                        </div>
                        <div class="col-10">
                            <div class="tab-content" id="v-pills-tabContent">
                                <div class="tab-pane fade show active" id="v-pills-cinema" role="tabpanel"
                                     style="color: black;">
                                    <form action="map.php"
                                          name="cinema" id="formCinema" method="post"
                                          enctype="multipart/form-data" accept-charset="utf-8">
                                        <input type="hidden" id="formType" name="formType" value="Spielstätte">

                                        <div class="row d-flex justify-content-center">
                                            <div class="col-lg-6">

                                                <div class="form-group">
                                                    <label for="name"
                                                           style="color: #d2d2d2"><?php echo $lang['Name']; ?></label>
                                                    <input type="text" class="form-control textinput-formular"
                                                           id='name' name='name'
                                                           required="required"
                                                           style="background-color: #3b3b3b; color: #ffffff">
                                                </div>

                                                <div class="form-group">
                                                    <label for="start"
                                                           style="color: #d2d2d2"><?php echo $lang['betrieb_von']; ?></label>
                                                    <input type="number" maxlength="4" minlength="4"
                                                           class="form-control textinput-formular"
                                                           id='start' name='start'
                                                           style="background-color: #3b3b3b; color: #ffffff"
                                                           placeholder="Wenn bekannt">
                                                </div>

                                                <div class="form-group">
                                                    <label for="end"
                                                           style="color: #d2d2d2"><?php echo $lang['betrieb_bis']; ?></label>
                                                    <input type="number" maxlength="4" minlength="4"
                                                           class="form-control textinput-formular"
                                                           id='end' name='end'
                                                           style="background-color: #3b3b3b; color: #ffffff"
                                                           placeholder="Wenn bekannt">
                                                    <input type="checkbox"
                                                           class="hidden"
                                                           id='duty' name='duty'>
                                                    <div class="d-inline-flex justify-content-center align-items-center checkbox-green"
                                                        onclick="document.getElementById('duty').click();"> 
                                                        <img class="checkbox-check" src="images/check-solid.svg">
                                                    </div>
                                                    <label for="duty"
                                                           style="color: #d2d2d2; margin-left: 1.5rem; margin-top: 0.5rem;">noch
                                                        in Betrieb</label>
                                                </div>

                                                <label for="streetname" style="color: #d2d2d2">Adresse</label>
                                                <div class="form-group">
                                                    <input type="text"
                                                           class="form-control textinput-formular"
                                                           id='streetname' name='streetname'
                                                           style="width: 84%; display: initial; background-color: #3b3b3b; color: #ffffff"
                                                           placeholder="Musterstraße">
                                                    <input type="text" class="form-control textinput-formular"
                                                           id='housenumber' name='housenumber'
                                                           style="width: 15%; display: initial; background-color: #3b3b3b;
                                                           color: #ffffff; float: right" placeholder="12a">
                                                    <input type="number" minlength="5" maxlength="5"
                                                           class="form-control textinput-formular mt-1"
                                                           id='postalcode' name='postalcode'
                                                           style="width: 25%; display: initial; background-color: #3b3b3b; color: #ffffff"
                                                           placeholder="00001">
                                                    <input type="text" class="form-control textinput-formular mt-1"
                                                           id='city' name='city'
                                                           style="width: 74%;display: initial; background-color: #3b3b3b;
                                                           color: #ffffff; float: right" placeholder="Musterstadt">
                                                </div>

                                                <div class="form-group">
                                                    <?php
                                                    $optionsTypeSelect = getAllCinemaTypes();
                                                    ?>
                                                    <input type="hidden" id="CinemaTypeSelect" name="CinemaTypeSelect"
                                                           value="<?php echo $optionsTypeSelect[0]['id']; ?>">
                                                    <label style="color: #d2d2d2">Kinotyp</label>
                                                    <div class="dropdown">
                                                        <button id="cinemaTypeButton"
                                                                class="btn btn-secondary textinput-formular text-left col-12 poi-dropdown-button"
                                                                type="button"
                                                                data-toggle="dropdown">
                                                            <?php
                                                            echo $optionsTypeSelect[0]['name'];
                                                            ?>
                                                        </button>
                                                        <div class="dropdown-menu poi-dropdown-menu">
                                                            <?php
                                                            for ($i = 0; $i < count($optionsTypeSelect); $i++) {
                                                                ?>
                                                                <a class="dropdown-item poi-dropdown-item"
                                                                   onclick="setCinemaType(<?php echo $optionsTypeSelect[$i]['id'] . ', \'' . $optionsTypeSelect[$i]['name']; ?>')">
                                                                    <?php echo $optionsTypeSelect[$i]['name']; ?>
                                                                </a>
                                                                <?php
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group hidden" style="color: #d2d2d2">
                                                    <label for="latlng"><?php echo $lang['Koordinaten']; ?></label>
                                                    <br>
                                                    <small><?php echo $lang['Breitengrad']; ?></small><input
                                                            type="Number"
                                                            step="0.0000000001"
                                                            id='lat'
                                                            name='lat'
                                                            required="required"
                                                            readonly="readonly"
                                                            class="form-control"
                                                            style="background-color: #3b3b3b; color: #d2d2d2"
                                                            value="" ;
                                                            placeholder="Z.B 50.6835500049">
                                                    <small><?php echo $lang['Längengrad']; ?></small><input
                                                            type="Number"
                                                            step="0.0000000001"
                                                            id='lng'
                                                            name='lng'
                                                            required="required"
                                                            readonly="readonly"
                                                            class="form-control"
                                                            style="background-color: #3b3b3b; color: #d2d2d2"
                                                            value="" ;
                                                            placeholder="Z.B 10,9206143514">

                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <label for="history" class="popover-label"
                                                       style="color: #d2d2d2"><?php echo $lang['Geschichte'] . " "; ?>
                                                    <div id="history-popover"
                                                         class="blend-out-animation popover-container d-inline-flex flex-column"
                                                         onmouseleave="hidePopover('history-popover')">
                                                        <img class="popover-icon"
                                                             src="images/info-circle-solid-white.svg" height="18px"
                                                             onmouseenter="showPopover('history-popover')">
                                                        <div class="popover popover-hover d-flex flex-column align-items-center">
                                                            <div class="arrow-container d-flex justify-content-center align-items-end">
                                                                <img class="arrow-hover"
                                                                     src="images/caret-up-solid-tooltip.svg">
                                                            </div>
                                                            <div class="popover-body">
                                                                Dieses Feld ist nur für die Geschichte des Kinos.
                                                                <?php
                                                                if (config::$ENABLE_STORIES) {
                                                                    ?>
                                                                    Persönliche Geschichten können
                                                                    <a class='link-green'
                                                                       href='/StoryUpload.php'>hier</a>
                                                                    erzählt werden.
                                                                    <?php
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </label>
                                                <textarea class="form-control border textinput-formular"
                                                          name='history' id='history'
                                                          style="border: 3px; height: 203px; background-color: #3b3b3b; color: #ffffff"></textarea>
                                                <div id='Kartemini' class="form-control border mt-3 mb-3"
                                                     style="height: 204px"></div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="input-group">
                                                    <input name="bild" class="btn-secondary" id="bild" type="file"
                                                           size="100" accept="image/jpeg, image/png">
                                                    <button class="btn btn-warning" id='picUploadNice' type="button"
                                                            onclick="document.getElementById('bild').click();">
                                                        <img src="images/image-regular.svg" width="25px">
                                                    </button>

                                                    <input type="text" id="formularImageName" class="form-control"
                                                           value="Bild auswählen"
                                                           style="background-color: transparent; border-color: transparent; color: white"
                                                           readonly>

                                                </div>
                                                <input type="hidden" name="MAX_FILE_SIZE" value="209715200"/>
                                                <div class="form-group hidden">
                                                    <input type="text" class="form-control" id='category'
                                                           name='category'
                                                           value='0'>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 d-flex justify-content-end">
                                                <input type="button" class="btn btn-warning btn-important"
                                                       name="speichern"
                                                       value="<?php echo $lang['Speichern']; ?>"
                                                       id="poisubmit" onclick="checkInputDataAddPOI()">
                                            </div>
                                        </div>
                                        <div class="row img-preview-wrapper"><img class="new-img-preview hide"></div>
                                        <div>
                                            <input id="img1" name="img1" type="hidden" value="">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
} //schließen der ADD POI Abfrage
?>
<div id="BotRight">
    <span id="checkboxesStateShow">
        <button class="btn btn-sq btn-dark2" data-toggle="tooltip" data-placement="top"
                title="Validierte Interessenpunkte" data-id="validatedOnMapShow">
            <img src="images/map-marker-green.svg" width="17px" style="margin-top: -2px"
                 id="validatedBtnOnMapPic"></button>
        </button>
        <button class="btn btn-sq btn-dark2" data-toggle="tooltip" data-placement="top"
                title="Teilvalidierte Interessenpunkte" data-id="partValidatedOnMapShow">
            <img src="images/map-marker-blue.svg" width="17px" style="margin-top: -2px"
                 id="partvalidatedBtnOnMapPic"></button>
        </button>
        <button class="btn btn-sq btn-dark2" data-toggle="tooltip" data-placement="top"
                title="Unvalidierte Interessenpunkte" data-id="unvalidatedOnMapShow">
            <img src="images/map-marker-red.svg" width="17px" style="margin-top: -2px"
                 id="unvalidatedBtnOnMapPic"></button>
        </button>
        <input type="checkbox" id="validatedOnMapShow" class="hidden" checked>
        <input type="checkbox" id="partValidatedOnMapShow" class="hidden" checked>
        <input type="checkbox" id="unvalidatedOnMapShow" class="hidden" checked>
    </span>
    <span id="minYearSpan" class="noselect"></span>
    <span id="yearSliderSpan"></span>
    <span id="maxYearSpan" class="noselect"></span>
    <script type="text/javascript" src="js/slider.js"></script>
</div>
</body>
</html>