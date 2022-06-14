<?php
/**
 * In this are all basic functions, which are used all over the plattform.
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * generates header-bar for all pages, page individual things can be displayed
 * @param bool $Login define if the bar is displayed by a user which is logged in
 * @param array $lg should be fitting the language array og the language chosen by the user
 * @param bool $map define if the bar is displayed on map.php
 * @param bool $loginpage defines if calling page is loginpage
 */
function generateHeader($Login = true, $lg = array(), $map = false, $loginpage = false)
{
    ?>
    <nav class="navbar navbar-expand-lg sticky-top" <?php if ($map) {
        echo 'style="position: relative; top: 0"';
    } ?>>
        <a class="navbar-brand"
            <?php
            if ($Login == true) {
                echo 'href="hub.php"';
            } else {
                echo 'href="index.php"';
            }
            ?>
        ><img src="<?php echo config::$LOGO ?>" width="70"></a>
        <button class="navbar-toggler btn-secondary" type="button" data-toggle="collapse"
                data-target="#navbarSupportedContent">
            <img src="images/bars-solid.svg" width="17px">
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <?php
                if ($Login) {
                    if ($_SESSION['role'] >= config::$ROLE_AUTH_USER) {
                        ?>
                        <li class="nav-item active">
                            <button type="button" onclick="loadPersonalArea()"
                                    class="btn text-white"><?php echo $lg['infos']; ?></button>
                        </li>
                        <?php
                    }
                    ?>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" style="color: #d2d2d2" href="#" id="navbarDropdown"
                           role="button"
                           data-toggle="dropdown"><?php echo $lg['Verw']; ?></a>
                        <div class="dropdown-menu navbar-dropdown-menu">
                            <a class="dropdown-item" href="map.php">Karte</a>
                            <?php
                            if ($_SESSION['role'] >= config::$ROLE_AUTH_USER) {
                                ?>
                                <a class="dropdown-item" href="poimgmt.php"><?php echo $lg['eintragsverwaltung']; ?></a>
                                <a class="dropdown-item" href="ranklist.php">Bestenliste</a>
                                <?php
                            }
                            ?>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" style="color: #d2d2d2" href="#" id="navbarDropdown"
                           role="button"
                           data-toggle="dropdown">Material</a>
                        <div class="dropdown-menu navbar-dropdown-menu">
                            <?php
                            if (config::$ENABLE_STORIES) {
                                ?>
                                <a class="dropdown-item" href="StoryUpload.php">Erfahrungsberichte</a>
                                <?php
                            }
                            if ($_SESSION['role'] >= config::$ROLE_UNAUTH_USER) {
                                ?>
                                <a class="dropdown-item" href="MaterialUpload.php">Bild hinzufügen</a>
                                <?php
                            }
                            ?>
                            <a class="dropdown-item" href="ListMaterial.php">Archiv</a>
                        </div>
                    </li>
                    <?php
                    if ($_SESSION['role'] >= config::$ROLE_EMPLOYEE) {
                        ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" style="color: #d2d2d2" href="#" id="navbarDropdown"
                               role="button"
                               data-toggle="dropdown">Admintools</a>
                            <div class="dropdown-menu navbar-dropdown-menu">
                                <a class="dropdown-item" href="statistics.php">Statistik</a>
                                <a class="dropdown-item" href="announcement.php">Ankündigungen</a>
                            </div>
                        </li>
                        <?php
                    }
                }
                ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" style="color: #d2d2d2" href="#" id="navbarDropdown"
                       role="button"
                       data-toggle="dropdown">Kontakt</a>
                    <div class="dropdown-menu navbar-dropdown-menu">
                        <a class="dropdown-item" href="background.php">Projekthintergrund</a>
                        <?php if (($Login && $loginpage == false && config::$PUBLIC_CONTACT) || ($Login && $loginpage == false && $_SESSION['role'] >= config::$ROLE_AUTH_USER)) {
                            ?>
                            <a class="dropdown-item" href="contact.php">Kontakt</a>
                            <?php
                        }
                        ?>
                        <a class="dropdown-item" href="impressum.php">Impressum</a>
                        <a class="dropdown-item" href="privacy-policy.php">Datenschutz</a>
                    </div>
                </li>
                <?php if ($Login) {
                    ?>
                    <li class="nav-item greeting">
                        <a class="nav-link disabled" style="color: #d2d2d2" href="#" tabindex="-1">Hallo
                            <?php
                            if ((isset($_SESSION['firstName']) == false && isset($_SESSION['lastName']) == false) || ($_SESSION['firstName'] == "" && $_SESSION['lastName'] == "")) {
                                echo $_SESSION['username'];
                            } else {
                                echo (isset($_SESSION['firstName']) ? $_SESSION['firstName'] : '') . ' ' . (isset($_SESSION['lastName']) ? $_SESSION['lastName'] : '');
                            }
                            $caption = " (" . $_SESSION['rolename'];
                            if (config::$ROLE_AUTH_USER <= $_SESSION['role'] && $_SESSION['role'] < config::$ROLE_EMPLOYEE) {
                                $rankPoints = getRemoteRank($_SESSION['username'])['points'];
                                if ($rankPoints <= 50) {
                                    $rankPoints = getValidationValue();
                                }
                                $ranktypesReq = getRanktypes()['ranktypes'];
                                $ranktypes = array();
                                ksort($ranktypesReq);
                                foreach ($ranktypesReq as $r) {
                                    $ranktypes[] = $r;
                                }
                                $set = false;
                                $icon = "";
                                dump($ranktypes, 8);
                                for ($i = sizeof($ranktypes) - 1; $i > -1; $i = $i - 1) {
                                    if ($rankPoints >= $ranktypes[$i]['value']) {
                                        if ($set == false) {
                                            $caption = $caption . "/" . $ranktypes[$i]['name'];
                                            $icon = '<img class="rankitem-navbar ml-2" src=\'' . $ranktypes[$i]['image'] . '\'>';
                                            $set = true;
                                        }
                                    }
                                }
                            }
                            $caption = $caption . ")";
                            if (config::$ROLE_AUTH_USER <= $_SESSION['role'] && $_SESSION['role'] < config::$ROLE_EMPLOYEE) {
                                $caption = $caption . $icon;
                            }
                            echo $caption;
                            ?>

                        </a>
                    </li>
                    <?php
                }
                ?>
            </ul>
            <!--</div>-->
        </div>
        <?php
        if ($Login) {
            ?>
            <a href="contact.php?error=1" class="btn btn-warning ml-1 report-error-btn">Fehler melden</a>
            <form action="logoutpage.php">
                <button type="submit" class="btn btn-warning logout-btn"
                ><?php echo $_SESSION['username'] == "gast" ? "Anmelden" : $lg['Abmelden']; ?></button>
            </form>
            <?php
        } else {
            if ($loginpage == false) { ?>
                <form action="index.php">
                    <button type="submit" class="btn btn-warning logout-btn">Anmelden</button>
                </form>
                <?php
            }
        }
        ?>
    </nav>
    <?php
    if (!$map) {
        ?>
        <input type="text" value="<?php echo createCSRFtokenClient() ?>" id="TokenScriptCSRF" hidden>
        <?php
    }
    ?>
    <div class="modal fade" id="MeineInfos" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header d-inline-flex align-items-baseline rounded-top-7">
                    <h5 class="modal-title" style="color: white"><?php echo $lg['Kommentare']; ?></h5>
                    <button type="button" class="btn" data-dismiss="modal" style="margin-top:-5px">
                        <img src="images/times-solid.svg" width="14px">
                    </button>
                </div>
                <div class="modal-body rounded-bottom-7">
                    <div id="wichtig">
                    </div>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="MeineKommentareTb" role="tabpanel">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade overflow-auto" id="EditComment" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header d-inline-flex align-items-baseline rounded-top-7">
                    <h5 style="color: white">Kommentar bearbeiten</h5>
                    <button type="button" class="btn" data-dismiss="modal" style="margin-right: 20px; color: #ffffff">
                        <img src="images/times-solid.svg" width="14px">
                    </button>
                </div>
                <div class="modal-body rounded-bottom-7" style="color: black">
                    <div id="wichtig">
                        <form name="formComment" action="#" id="formCommentEdit"
                              enctype="multipart/form-data" accept-charset="utf-8">
                            <label for="comment" style="color: #d2d2d2">Kommentar</label>
                            <textarea class="form-control textinput-formular" id="commentEditTBfield" name="comment"
                                      required="required" rows="10"
                                      style="background-color: #3b3b3b; color: #ffffff; display:inline-block;"></textarea>
                            <input type="hidden" name="cid" id="cidEditComment">
                            <button type="button" class="btn btn-success" style="float: right; margin-top:6px; "
                                    onclick="saveEditedComment()">
                                absenden
                            </button>
                        </form>
                    </div>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="MeineKommentareTb" role="tabpanel">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade overflow-auto" id="LongComment" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header d-inline-flex align-items-baseline rounded-top-7">
                    <h5 id="LongCommentTitel" class="modal-title" style="color: #ffffff"></h5>
                    <button type="button" class="btn" data-dismiss="modal" id="CloseBtnLongComment">
                        <img src="images/times-solid.svg" width="14px">
                    </button>
                </div>
                <div class="modal-body rounded-bottom-7">
                    <div id="MainLongComment" class="mt-3 mr-3 ml-3 text-light"></div>
                    <p id="LongCommentNameDate" class="ml-4 mt-3" style="font-size: 0.8em; color: #c2c2c2"></p>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade overflow-auto" id="PictureSelectModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content rounded-top-7">
                <div class="modal-header d-inline-flex align-items-baseline rounded-top-7">
                    <h5 class="modal-title" style="color: white">Bild auswählen</h5>
                    <button type="button" class="btn btn-link" id="MainPictureSelectCloseButton"
                            style="margin-top:-5px">
                        <img src="images/times-solid.svg" width="14px">
                    </button>
                </div>
                <div class="modal-body" style="color: black" id="picture-select-modal-body">
                    <div class="containter">
                        <div id="MainPictureSelectorCards" class="mt-3 mr-3 ml-3 mb-3 text-light card-columns">

                        </div>
                        <input type="text" class="hidden" id="MainPictureSelectSelected" value=""/>
                        <input type="checkbox" class="hidden" id="MainPictureSelectSingleToggle"/>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="MainPictureSelectAbortButton">Abbrechen</button>
                    <button type="button" class="btn btn-primary btn-important" id="MainPictureSelectSaveButton">
                        Speichern
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade col-8 offset-2" id="CookieBannerModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered" role="document">
            <!-- because normal overflow-y: auto is displaying scrollbar next to modal and not on right side of browser window-->
            <div class="modal-content">
                <div class="modal-header modal-header-unround d-inline-flex align-items-baseline rounded-top-7">
                    <h5 class="modal-title" style="color: white">Cookies</h5>
                </div>
                <div class="modal-body modal-body-unround" id="CookieModalBody">
                    <div class="container">
                        <div class="text-light">
                            Diese Website nutzt Cookies, um die gewünschte Funktionalität zu erbringen und zu
                            verbessern. Durch Nutzung dieser Seite akzeptieren Sie Cookies. <a href="privacy-policy.php"
                                                                                               class="text-light">Weitere
                                Informationen</a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer modal-footer-unround">
                    <button type="button" class="btn btn-primary btn-important" id="CookieAcceptButton"
                            onclick="AcceptCookies();">
                        Akzeptieren
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade col-6 offset-3" id="AnnouncementModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered" role="document">
            <!-- because normal overflow-y: auto is displaying scrollbar next to modal and not on right side of browser window-->
            <div class="modal-content">
                <div class="modal-header d-inline-flex align-items-baseline rounded-top-7">
                    <h5 class="modal-title" style="color: white" id="announcementModalMainTitle"></h5>
                </div>
                <div class="modal-body modal-body-unround" style="" id="CookieModalBody">
                    <div class="container">
                        <div class="text-light" id="announcementModalMainContent">

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-important" id="CookieAcceptButton"
                            onclick="closeAnnouncement();">
                        Schließen
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * dumpes data, if debug mode is enabled and filters debug output based on config::$DEBUG_LEVEL
 * @param mixed $data data that should be printed for debug purposes
 * @param int $level If the value is greater or equal than config::$DEBUG_LEVEL the variable to debug will be printed out
 */
function dump($data, $level = -1)
{
    if ($level != -1) {
        if ($level > config::$DEBUG_LEVEL) {
            return;
        }
    }
    if (config::$DEBUG) {
        ?>
        <code style='display: block;white-space: pre-wrap;'>
            <?php
            var_dump($data);
            ?>
        </code>
        <br/>
        <br/>
        <?php
    }
}

/**
 * returns a http-status Redirect
 * @param string $url URI where user should be redirected
 * @param bool $permanent sets if redirect has http-status-code 301 (permanent redirect) or 302 (temporary redirect) if true there will be 301 in response, default it is false
 */
function Redirect($url, $permanent = false)
{
    header('Location: ' . $url, true, $permanent ? 301 : 302);
    die();
}

/**
 * Denies access to page and ends php execution, always sends http-status-code 403 (Forbidden)
 * @param string $string if string is given it will be send before php execution will be stopped
 */
function permissionDenied($string = "")
{
    if ($string !== "") {
        echo $string;
    } else {
        echo "You shall not pass!";
    }
    http_response_code(403);
    die();
}

/**
 * generates all needed header tags for each page
 * @param array $additional additional scripts or stylesheets can be integrated if this array is defined, on default it will be a initialized non filled array
 */
function generateHeaderTags($additional = array())
{
    ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="canonical"
          href="<?= $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']; ?>">

    <?php
    //map for stylesheed to be included in header
    //key -> path + name to file;
    //value -> true: toggle with debug mode, false: outputs minified version every time
    $css_map = [
        "csse/fontawesome" => false,
        "csse/all" => false,
        "csse/bootstrap" => false,
        "csse/bootstrap-slider" => false,
        "css/leaflet" => true,
        "css/main" => true,
        "css/Control.Geocoder" => true,
        "csse/jquery-ui" => true,
        "csse/jquery-ui.structure" => true,
        "csse/jquery-ui.theme" => true,
        "csse/reflow-table" => false,
    ];
    foreach ($css_map as $name => $use_debug) {
        echo '<link rel="stylesheet" type="text/css" href="' . $name . (!$use_debug || !config::$DEBUG ? '.min' : '') . '.css">';
    }

    //map for scriptfiles to be included in header
    //key -> path + name to file;
    //value -> true: toggle with debug mode, false: outputs minified version every time
    $js_map = [
        "js/polyfill" => true,
        "jse/popper" => false,
        "jse/jquery-3.4.1" => false,
        "jse/jquery-ui" => true,
        "jse/bootstrap" => false,
        "jse/bootstrap-slider" => false,
        "js/dynamicModal" => true,
        "jse/leaflet" => false,
        "jse/reflow-table" => false,
        "js/kinoMainLib" => true,
        "js/personalArea" => true,
        "js/Control.Geocoder" => true,


    ];

    foreach ($js_map as $name => $use_debug) {
        echo '<script type="text/javascript" src="' . $name . (!$use_debug || !config::$DEBUG ? '.min' : '') . '.js"></script>';
    }

    if (sizeof($additional) > 0) {
        foreach ($additional as $line) {
            $href = $line['hrefmin'] ?? $line['href'];
            if (config::$DEBUG) {
                $href = $line['href'];
            }
            switch ($line['type']) {
                case 'style':
                case 'css':
                case 'link':
                    echo '<link rel="' . $line['rel'] . '" href="' . $href . '"' . (($line['typeval'] ?? '') ? ' type="' . $line['typeval'] . '"' : '') . '>';
                    break;
                case 'js':
                case 'script':
                    echo '<script type="' . $line['typeval'] . '" src="' . $href . '" ></script>';
                    break;
            }
        }
    }
}

/**
 * decodes a given json string, parameters for json decoding can be set here
 * @param string $string json as string
 * @return array content of json in accessible format (structured array)
 */
function decode_json($string)
{
    return json_decode($string, true);
}

/**
 * checks if a point of interest is existing
 * @param int $POIID id of point of interest which should be checked
 * @return bool true if point of interest is existing otherwise false
 */
function checkPoiExists($POIID)
{
    foreach (getAllPois() as $POI) {
        if (is_string($POIID) === false) {
            $POIID = strval($POIID);
        }
        if ($POIID === $POI['poi_id']) {
            return true;
        }
    }
    return false;
}

/**
 * requests the remote login data from cosp for a given user
 * @param string $name username entered by user
 * @return array result of request from cosp
 */
function remoteLogin($name)
{
    $result = ApiCall(array('username' => $name, 'ignore' => false), "rud");
    if ($result['existent'] == 0) {
        return array(
            "result" => false,
            "password" => ""
        );
    }
    return array(
        "result" => true,
        "name" => $result['username'],
        "firstname" => $result['firstname'],
        "lastname" => $result['lastname'],
        "password" => $result['password'],
        "email" => $result['email']
    );
}

/**
 * get remote role data from cosp with a given username
 * @param string $name username of user for which details are needed
 * @param bool $ignoreDeaktivatet ignores deaktivation state of user
 * @return array role data from cosp
 */
function remoteRole($name, $ignoreDeaktivatet = false)
{
    $result = ApiCall(array('username' => $name, 'ignore' => $ignoreDeaktivatet), "rud");
    if ($result['existent'] == 0) {
        return array(
            "result" => false,
            "password" => ""
        );
    }
    return array(
        "result" => true,
        "name" => $result['username'],
        "role" => array(
            "rolevalue" => $result['role']['rolevalue'],
            "rolename" => $result['role']['rolename'],
        )
    );
}

/**
 * sends request to add new user to cosp
 * @param string $Username username which user wanted
 * @param string $EMail email from user
 * @param string $pwd clear password of user, not hashed
 * @param string $firstname firstname of user, facultative
 * @param string $lastname lastname of user, facultative
 * @return array if result is positiv
 */
function addRemoteUser($Username, $EMail, $pwd, $firstname = "", $lastname = "")
{
    $params = array(
        "username" => $Username,
        "firstname" => $firstname,
        "lastname" => $lastname,
        "password" => $pwd,
        "email" => $EMail
    );
    return ApiCall($params, "aud");
}

/**
 * requests all usernames which are known to cosp
 * @return array List of all registered usernames
 */
function getRemoteAllUsernames()
{
    $result = ApiCall(array(), "gau");
    return $result["usernames"];
}

/**
 * Uploads a picture to picture cache of cosp
 * @param string $title title user gave file which should be uploaded
 * @param string $desc description of file which should be uploaded
 * @param string $filepath path to file which should be uploaded
 * @param string $ftype filetype of file which should be uploaded
 * @param string $username username of uploading user
 * @param string $source source of picture
 * @param int $sourceType identifier of type of source
 * @return mixed
 */
function UploadPicture($title, $desc, $filepath, $ftype, $username, $source = "", $sourceType = PHP_INT_MIN)
{
    $params = array(
        "title" => $title,
        "desc" => $desc,
        "pic" => curl_file_create($filepath, $ftype),
        "username" => $username,
        "MAX_FILE_SIZE" => "209715200"
    );
    if ($sourceType != "" && $sourceType > 0) {
        $params = array(
            "title" => $title,
            "desc" => $desc,
            "pic" => curl_file_create($filepath, $ftype),
            "username" => $username,
            "source" => $source,
            "sourceid" => $sourceType,
            "MAX_FILE_SIZE" => "209715200"
        );
    }
    $result = ApiCall($params, "pul", true);
    return $result;
}

/**
 * get data to create a link for cosp-uapi to retrieve picture data
 * @param string $pictureToken token for which link data is requested
 * @return array all needed things to generate a valid cosp-uapi link
 */
function getRemoteSeccode($pictureToken)
{
    $value = getValidationValue();
    $result = ApiCall(array(
            "pictureToken" => $pictureToken,
            "rank-val" => $value,
            "username" => $_SESSION['username'])
        , "gsc");
    return $result;
}

/**
 * get a list of all pictures available for this docked in platform
 * @return array list of all available and accessible pictures
 */
function getRemotePictureList()
{
    $value = getValidationValue();
    $result = ApiCall(array(
            "rank-val" => $value,
            "username" => $_SESSION['username'])
        , "gpl");
    return $result;
}

/**
 * creates and performs request to cosp-api interface, if json parsing is not possible site php will not be executed further
 * @param array $params needed parameters for requested action
 * @param string $type defines type of api request, usually three signs long
 * @param bool $file_upload If true file upload is possible, otherwise file upload won't be possible
 * @return array result of cosp request
 */
function ApiCall($params, $type, $file_upload = false)
{
    $postData = array(
        "token" => config::$CSTOKEN,
        "type" => $type
    );
    $postData = array_merge($postData, $params);
    $ch = curl_init(config::$CSAPI);
    $standard_ar = array(
        CURLOPT_POST => TRUE,
        CURLOPT_RETURNTRANSFER => TRUE,
    );
    $specialAR = array(
        CURLOPT_POSTFIELDS => json_encode($postData),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    );
    if ($file_upload) {
        $specialAR = array(
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: multipart/form-data'
            ),
        );
    }
    foreach (array_keys($specialAR) as $key) {
        $standard_ar[$key] = $specialAR[$key];
    }
    dump($standard_ar, 8);
    curl_setopt_array($ch, $standard_ar);

// Send the request
    $response = curl_exec($ch);
    dump($response, 8);
// Check for errors
    if ($response === FALSE) {
        die(curl_error($ch));
    }
// Decode the response
    $responseData = decode_json($response);
    dump($responseData, 8);
    if (isset($responseData['code'])) {
        if ($responseData["code"] === 0) {
            return $responseData;
        }
    }
    die("Internal Error");
}

/**
 * Checks if a given string is a mail adress
 *
 * @param string $email checks if mail-address seems valid
 * @return bool true if email seems valid, otherwise false
 */
function checkMailAddress($email)
{
    $result = filter_var($email, FILTER_VALIDATE_EMAIL);
    dump("Email-Address-Checker:" . $email, 8);
    dump($result, 8);
    return $result;
}

/**
 * checks if password given by user are matching and have a certain length
 * @param string $PasswordField1Val
 * @param string $PasswordField2Val
 * @return bool
 */
function inspectPassword($PasswordField1Val, $PasswordField2Val)
{
    if ($PasswordField1Val !== $PasswordField2Val) {
        return false;
    }
    if (strlen($PasswordField1Val) < config::$PWD_LENGTH) {
        return false;
    }
    return true;
}

/**
 * checks if user has needed permission to access a page, if he does not have enough permission the permission denied will be executed
 * @param int $requiredPermission needed permission level, levels defined in config, not users permission level
 */
function checkPermission($requiredPermission)
{
    dump($_SESSION, 5);
    dump($requiredPermission, 5);
    if ($_SESSION['role'] < $requiredPermission) {
        permissionDenied("#39: There is no such thing as a coincidence.");
    }
}

/**
 * request remote rank data of user at cosp-api interface
 * @param string $username username of user whose data should be requested
 * @return array result of request
 */
function getRemoteRank($username)
{
    $result = ApiCall(array('username' => $username), "grp");
    return $result;
}

/**
 * get defined rank types from cosp
 * @return array list of all rank types with all needed values
 */
function getRanktypes()
{
    $result = ApiCall(array(), "grt");
    return $result;
}

/**
 * request list of top users and all data to display it properly
 * @return array list of top users and data for statistics
 */
function getRanklist()
{
    $result = ApiCall(array(), "grl");
    return $result;
}

/**
 * adds points to users rank point account
 * @param string $username username to whose account should receive points
 * @param int $points points user should receive
 * @param string $reason reason for received points
 * @return array result of request from cosp
 */
function addRankPoints($username, $points, $reason)
{
    $result = ApiCall(array('username' => $username, 'points' => $points, 'reason' => $reason), "arp");
    return $result;
}

/**
 * get value with which a user can validate
 * @return int value of users validate power
 */
function getValidationValue($name = "")
{
    $value = 0;
    if ($name == "") {
        $name = $_SESSION['username'];
    }
    if ($_SESSION['username'] == $name) {
        if ($_SESSION['role'] >= config::$ROLE_EMPLOYEE) {
            return 400;
        }
    }
    $role = PHP_INT_MAX;
    if ($_SESSION['username'] !== $name) {
        $role = remoteRole($name, true)['role']['rolevalue'];
    }
    if (($_SESSION['role'] >= config::$ROLE_AUTH_USER && $_SESSION['username'] == $name) || $role < config::$ROLE_EMPLOYEE) {
        $rankPoints = getRemoteRank($name)['points'];
        $ranktypesReq = getRanktypes()['ranktypes'];
        $ranktypes = array();
        ksort($ranktypesReq);
        foreach ($ranktypesReq as $r) {
            $ranktypes[] = $r;
        }
        $set = false;
        dump($ranktypes, 8);
        for ($i = sizeof($ranktypes) - 1; $i > -1; $i = $i - 1) {
            if ($rankPoints >= ($ranktypes[$i]['value'] * config::$RANK_MULTIPLIER) && $ranktypes[$i]['value'] > $value) {
                if ($set == false) {
                    $value = $ranktypes[$i]['value'];
                    $set = true;
                }
            }
        }
        if ($value == 0) {
            $minimum = PHP_INT_MAX;
            foreach ($ranktypes as $rank) {
                if ($rank['value'] < $minimum) {
                    $minimum = $rank['value'];
                }
            }
            $value = $minimum;
        }
    }
    if ($role < PHP_INT_MAX && $role >= config::$ROLE_EMPLOYEE) {
        return 400;
    }
    return $value;
}

/**
 * add validation to point of interest
 * @param int $poiid id of point of interest which should be validated by user
 */
function validatePoi($poiid)
{
    $value = getValidationValue();
    insertValidateForPOI($poiid, $value);
    if ($value >= 1) {
        addRankPoints($_SESSION['username'], 2, "Hat Interessenpunkt validiert.");
    }
    if (getValidateSumForPOI($poiid) >= 400) {
        $poi = getPoi($poiid);
        $username = $poi['username'];
        addRankPoints($username, 10, "Validierter Punkt hinzugefügt.");
    }
}

/**
 * add validation to time span of point of interest
 * @param int $poiid id of point of interest which should be validated by user
 */
function validateTimeSpanPoi($poiid)
{
    $value = getValidationValue();
    insertValidateTimespan($poiid, $value);
    if ($value >= 1) {
        addRankPoints($_SESSION['username'], 2, "Hat Zeitraum eines Interessenpunktes validiert.");
    }
    if (getValidateSumTimespan($poiid) >= 400) {
        $user = getUserDataById(getPoi($poiid)['creator_timespan'])['name'];
        addRankPoints($user, 10, "Durch Nutzer wurde eingebener Zeitraum eines Interessenpunktes validiert.");
    }
}

/**
 * add validation to current address of point of interest
 * @param int $poiid id of point of interest which should be validated by user
 */
function validateCurrentAddressPoi($poiid)
{
    $value = getValidationValue();
    insertValidateCurrentAddress($poiid, $value);
    if ($value >= 1) {
        addRankPoints($_SESSION['username'], 2, "Hat aktuelle Addresse eines Interessenpunktes validiert.");
    }
    if (getValidateSumCurAddresse($poiid) >= 400) {
        $user = getUserDataById(getPoi($poiid)['creator_currentAddress'])['name'];
        addRankPoints($user, 10, "Durch Nutzer wurde aktuelle Addresse eines Interessenpunktes validiert.");
    }
}

/**
 * add validation to history of point of interest
 * @param int $poiid id of point of interest which should be validated by user
 */
function validateHistoryPoi($poiid)
{
    $value = getValidationValue();
    insertValidateHistory($poiid, $value);
    if ($value >= 1) {
        addRankPoints($_SESSION['username'], 2, "Hat Historie eines Interessenpunktes validiert.");
    }
    if (getValidateSumHist($poiid) >= 400) {
        $user = getUserDataById(getPoi($poiid)['creator_history'])['name'];
        addRankPoints($user, 10, "Durch Nutzer wurde Historie eines Interessenpunktes validiert.");
    }
}

/**
 * add validation to name of point of interest
 * @param int $nameid id of name which should be validated by user
 */
function validatePoiName($nameid)
{
    $value = getValidationValue();
    insertValidateName($nameid, $value);
    if ($value >= 1) {
        addRankPoints($_SESSION['username'], 2, "Hat einen Namen eines Interessenpunktes validiert.");
    }
    if (getValidateSumName($nameid) >= 400) {
        $user = getUsernameByNameId($nameid);
        addRankPoints($user, 10, "Durch Nutzer wurde Name eines Interessenpunktes validiert.");
    }
}

/**
 * add validation to operator of point of interest
 * @param int $opertorid id of name which should be validated by user
 */
function validatePoiOperator($opertorid)
{
    $value = getValidationValue();
    insertValidateOperator($opertorid, $value);
    if ($value >= 1) {
        addRankPoints($_SESSION['username'], 2, "Hat einen Betreiber eines Interessenpunktes validiert.");
    }
    if (getValidateSumOperator($opertorid) >= 400) {
        $user = getCreatorByOperatorID($opertorid);
        addRankPoints($user, 10, "Durch Nutzer wurde Name eines Interessenpunktes validiert.");
    }
}

/**
 * add validation to operator of point of interest
 * @param int $histAddrId id of name which should be validated by user
 */
function validatePoiHistAddress($histAddrId)
{
    $value = getValidationValue();
    insertValidateHistAddress($histAddrId, $value);
    if ($value >= 1) {
        addRankPoints($_SESSION['username'], 2, "Hat eine historische Addresse eines Interessenpunktes validiert.");
    }
    if (getValidateSumHistAddress($histAddrId) >= 400) {
        $user = getCreatorByHistoricalAddressesId($histAddrId);
        addRankPoints($user, 10, "Durch Nutzer wurde historische Addresse eines Interessenpunktes validiert.");
    }
}

/**
 * add validation to operator of point of interest
 * @param int $story_poi_id id of link between point of interest and story token
 */
function validatePoiStory($story_poi_id)
{
    $value = getValidationValue();
    insertValidatePoiStory($story_poi_id, $value);
    if ($value >= 1) {
        addRankPoints($_SESSION['username'], 2, "Hat einen Link zwischen einer Geschichte und einem Interessenpunkt validiert.");
    }
    if (getValidateSumHistAddress($story_poi_id) >= 400) {
        $user = getCreatorByPoiStoryId($story_poi_id);
        addRankPoints($user, 10, "Durch Nutzer wurde Verbindung zwischen Geschichte und Interessenpunkt validiert.");
    }
}

/**
 * get all validation values from all points of interest
 * @return array list with points of interest and according validation value
 */
function getValidatedByPOI()
{
    $validated = getAllValidatedForPOI();
    $result = array();
    foreach ($validated as $val) {
        if (key_exists($val['poi_id'], $result)) {
            $result[$val['poi_id']]['value'] = $result[$val['poi_id']]['value'] + $val['value'];
        } else {
            $result[$val['poi_id']] = array(
                "id" => $val['id'],
                "uid" => $val['uid'],
                "value" => $val['value'],
                "date" => $val['date'],
            );
        }
    }
    return $result;
}

/**
 * select poi which should be displayed for a user based on users rank and role
 * @return array list of points of interest user is allowed to see
 */
function getPoisForUser()
{
    $pois = getAllPois();
    $result = array();
    foreach ($pois as $poi) {
        if (getValidateSumForPOI($poi['poi_id']) >= 400 || $_SESSION['role'] >= config::$ROLE_AUTH_USER || $poi['username'] == $_SESSION['username']) {
            $poi['validated'] = getValidateSumForPOI($poi['poi_id']) >= 400;
            if ($_SESSION['username'] !== 'gast') {
                $poi['validatedByUser'] = in_array($poi['poi_id'], getValidationsByUserForPOI());
            }
            $poi['deleted'] = $poi['deleted'] == 1;
            $poi['duty'] = $poi['duty'] == 1;
            $poi['bloglink'] = $poi['blog'];
            $poi['blog'] = $poi['blog'] != null && $poi['blog'] != "";
            $result[] = $poi;
        }
    }
    return $result;
}

/**
 * select poi which should be send for a user based on users rank and role
 * @return array list of points of interest user is allowed to see
 */
function getPoisTitleForUser()
{
    $pois = getAllPoisTitle();
    $result = array();
    foreach ($pois as $poi) {
        if (getValidateSumForPOI($poi['poi_id']) >= 400 || $_SESSION['role'] >= config::$ROLE_AUTH_USER) {
            $poi['validated'] = getValidateSumForPOI($poi['poi_id']) >= 400;
            if ($_SESSION['username'] !== 'gast') {
                $poi['validatedByUser'] = in_array($poi['poi_id'], getValidationsByUserForPOI());
            }
            $result[] = $poi;
        }
    }
    return $result;
}

/**
 * get all validated points of interest based on user id
 * @param int $uid unique user identifier (user-id or uid) by default id of current caller
 * @return array list of points of interests validated by user
 */
function getValidationsByUserForPOI($uid = -1)
{
    if ($uid === -1) {
        $uid = getUserData($_SESSION['username']);
        if (isset($uid['id'])) {
            $uid = $uid['id'];
        }
    }
    if (isset($uid) == false) {
        return array();
    }
    $validates = getAllValidatedForPOI();
    $result = array();
    dump($validates, 8);
    foreach ($validates as $val) {
        if ($val['uid'] == $uid) {
            if (in_array($val['poi_id'], $result)) {
                continue;
            }
            $result[] = $val['poi_id'];
        }
    }
    dump($result, 3);
    return $result;
}

/**
 * get all points of interest ids of validated time spans based on user id
 * @param int $uid unique user identifier (user-id or uid) by default id of current caller
 * @return array list of points of interests validated by user
 */
function getValidationsByUserForTimeSpans($uid = -1)
{
    if ($uid === -1) {
        $uid = getUserData($_SESSION['username']);
        if (isset($uid['id'])) {
            $uid = $uid['id'];
        }
    }
    $validates = getAllValidatedForTimeSpan();
    $result = array();
    dump($validates, 8);
    foreach ($validates as $val) {
        if ($val['uid'] == $uid) {
            if (in_array($val['poi_id'], $result)) {
                continue;
            }
            $result[] = $val['poi_id'];
        }
    }
    dump($result, 3);
    return $result;
}

/**
 * get all points of interest ids of validated time spans based on user id
 * @param int $uid unique user identifier (user-id or uid) by default id of current caller
 * @return array list of points of interests validated by user
 */
function getValidationsByUserForCurrentAddress($uid = -1)
{
    if ($uid === -1) {
        $uid = getUserData($_SESSION['username']);
        if (isset($uid['id'])) {
            $uid = $uid['id'];
        }
    }
    $validates = getAllValidatedForCurAddress();
    $result = array();
    dump($validates, 8);
    foreach ($validates as $val) {
        if ($val['uid'] == $uid) {
            if (in_array($val['poi_id'], $result)) {
                continue;
            }
            $result[] = $val['poi_id'];
        }
    }
    dump($result, 3);
    return $result;
}

/**
 * get all points of interest ids of validated time spans based on user id
 * @param int $uid unique user identifier (user-id or uid) by default id of current caller
 * @return array list of points of interests validated by user
 */
function getValidationsByUserForHistory($uid = -1)
{
    if ($uid === -1) {
        $uid = getUserData($_SESSION['username']);
        if (isset($uid['id'])) {
            $uid = $uid['id'];
        }
    }
    $validates = getAllValidatedForHistory();
    $result = array();
    dump($validates, 8);
    foreach ($validates as $val) {
        if ($val['uid'] == $uid) {
            if (in_array($val['poi_id'], $result)) {
                continue;
            }
            $result[] = $val['poi_id'];
        }
    }
    dump($result, 3);
    return $result;
}

/**
 * get all ids of validated names based on user id
 * @param int $uid unique user identifier (user-id or uid) by default id of current caller
 * @return array list of points of interests validated by user
 */
function getValidationsByUserForPoiNames($uid = -1)
{
    if ($uid === -1) {
        $uid = getUserData($_SESSION['username'])['id'];
    }
    $validates = getAllValidatedForPoiNames();
    $result = array();
    dump($validates, 8);
    foreach ($validates as $val) {
        if ($val['uid'] == $uid) {
            if (in_array($val['name_id'], $result)) {
                continue;
            }
            $result[] = $val['name_id'];
        }
    }
    dump($result, 3);
    return $result;
}

/**
 * get all ids of validated names based on user id
 * @param int $uid unique user identifier (user-id or uid) by default id of current caller
 * @return array list of points of interests validated by user
 */
function getValidationsByUserForPoiOperators($uid = -1)
{
    if ($uid === -1) {
        $uid = getUserData($_SESSION['username'])['id'];
    }
    $validates = getAllValidatedForPoiOperators();
    $result = array();
    dump($validates, 8);
    foreach ($validates as $val) {
        if ($val['uid'] == $uid) {
            if (in_array($val['operator_id'], $result)) {
                continue;
            }
            $result[] = $val['operator_id'];
        }
    }
    dump($result, 3);
    return $result;
}

/**
 * get all ids of validated names based on user id
 * @param int $uid unique user identifier (user-id or uid) by default id of current caller
 * @return array list of points of interests validated by user
 */
function getValidationsByUserForPoiHistAddresses($uid = -1)
{
    if ($uid === -1) {
        $uid = getUserData($_SESSION['username']);
        if (isset($uid['id'])) {
            $uid = $uid['id'];
        }
    }
    $validates = getAllValidatedForPoiHistAddresses();
    $result = array();
    dump($validates, 8);
    foreach ($validates as $val) {
        if ($val['uid'] == $uid) {
            if (in_array($val['address_id'], $result)) {
                continue;
            }
            $result[] = $val['address_id'];
        }
    }
    dump($result, 3);
    return $result;
}

/**
 * get all ids of validated names based on user id
 * @param int $uid unique user identifier (user-id or uid) by default id of current caller
 * @return array list of points of interests validated by user
 */
function getValidationsByUserForLinkPoiStory($uid = -1)
{
    if ($uid === -1) {
        $uid = getUserData($_SESSION['username']);
        if (isset($uid['id'])) {
            $uid = $uid['id'];
        }
    }
    $validates = getAllValidatedForPoiStory();
    $result = array();
    dump($validates, 8);
    foreach ($validates as $val) {
        if ($val['uid'] == $uid) {
            if (in_array($val['story_poi_link_id'], $result)) {
                continue;
            }
            $result[] = $val['story_poi_link_id'];
        }
    }
    dump($result, 3);
    return $result;
}

/**
 * requests data to load all stories stored for this docked in platform at cosp-uapi interface
 * @return array required data for link
 */
function getAllStoriesData()
{
    $value = getValidationValue();
    $data = ApiCall(array(
        "rank-val" => $value,
        "username" => $_SESSION['username'],
        "unvalidated" => $_SESSION['role'] >= config::$ROLE_AUTH_USER,
        "nonapproved" => $_SESSION['role'] >= config::$ROLE_EMPLOYEE,
        "deleted" => $_SESSION['role'] >= config::$ROLE_ADMIN,
    ), "gas")['data'];
    dump($data, 8);
    $parameters = array();
    if (count($data) > 0) {
        $parameters = array(
            'url' => config::$USAPI,
            'type' => "gas",
            "data" => $data["token"],
            "seccode" => $data["seccode"],
            "time" => $data["time"],
        );
    }
    $result = array(
        'result' => $parameters,
        'guest' => $_SESSION['role'] < config::$ROLE_AUTH_USER,
        'approver' => $_SESSION['role'] >= config::$ROLE_EMPLOYEE,
        'admin' => $_SESSION['role'] >= config::$ROLE_ADMIN,
    );
    return $result;
}

/**
 * uploads a story to cosp-api interface
 * @param array $json needed data to upload storie such as title and story itself
 * @return array result of cosp-api request
 */
function addUserStoryRemote($json)
{
    $data = ApiCall($json, "aus")['data'];
    return $data;
}

/**
 * deletes all data for a given poi and related informations
 * @param int $poiid ID of given poiid
 * @param bool $overwrite force direkt delete
 */
function deletePOIComplete($poiid, $overwrite = false)
{
    if (checkPoiExists($poiid) || $overwrite) {
        if ($overwrite || config::$DIRECT_DELETE) {
            deletevalidateByPOI($poiid);
            deleteValidateType($poiid);
            deleteValidateHistory($poiid);
            deleteValidateCurAddress($poiid);
            deleteValidateTimeSpan($poiid);
        }
        deleteCommentsByPoiidDBWrap($poiid, $overwrite);
        deletePoiPicLinkByPoi($poiid, $overwrite);
        deleteOperatorsByPoi($poiid, $overwrite);
        deleteHistAddressByPoi($poiid, $overwrite);
        deleteSeatsByPoi($poiid, $overwrite);
        deletePoiStoryByPoi($poiid, $overwrite);
        deleteNamesByPoi($poiid, $overwrite);
        deleteCinemasByPoi($poiid, $overwrite);
        deleteSourceByPoi($poiid, $overwrite);
        deletePoiDBWrap($poiid, $overwrite);
    }
}

/**
 * restores all data for a given poi and related informations
 * @param int $poiid ID of given poiid
 */
function restorePOI($poiid)
{
    updateDeletionStateCommentByPoiid($poiid, false);
    restorePoiPicLinkByPoi($poiid);
    restoreOperatorsByPoi($poiid);
    restoreHistAddressByPoi($poiid);
    restoreSeatsByPoi($poiid);
    restorePoiStoryByPoi($poiid);
    restoreNamesByPoi($poiid);
    restoreCinemasByPoi($poiid);
    restoreSourceByPoi($poiid);
    updateDeletionStatePoiByPoiid($poiid, false);
}

/**
 * delete all sources for a ceratin poi
 * @param int $poiid unique identifier of poi
 * @param bool $overwrite force direkt delete
 */
function deleteSourceByPoi($poiid, $overwrite = false)
{
    $sources = getSourceOfPoi($poiid, true);
    foreach ($sources as $source) {
        deleteSourceDBWrap($source['id'], $overwrite);
    }
}

/**
 * restore all sources for a ceratin poi
 * @param int $poiid unique identifier of poi
 */
function restoreSourceByPoi($poiid)
{
    $sources = getSourceOfPoi($poiid, true);
    foreach ($sources as $source) {
        updateSourceDeletionState($source['id'], false);
    }
}

/**
 * delete all picture poi links for a ceratin poi
 * @param int $poiid unique identifier of poi
 * @param bool $overwrite force direkt delete
 */
function deleteCinemasByPoi($poiid, $overwrite = false)
{
    $cinemaCounters = getCinemasByPoiId($poiid);
    foreach ($cinemaCounters as $cinemaCounter) {
        deleteCinemasDBWrap($cinemaCounter['ID'], $overwrite);
    }
}

/**
 * restore all picture poi links for a ceratin poi
 * @param int $poiid unique identifier of poi
 */
function restoreCinemasByPoi($poiid)
{
    $cinemaCounters = getCinemasByPoiId($poiid);
    foreach ($cinemaCounters as $cinemaCounter) {
        updateDeletionStateCinemasById($cinemaCounter['ID'], false);
    }
}

/**
 * delete all picture poi links for a ceratin poi
 * @param int $poiid unique identifier of poi
 * @param bool $overwrite force direkt delete
 */
function deleteNamesByPoi($poiid, $overwrite = false)
{
    $names = getNamesByPoiId($poiid);
    foreach ($names as $name) {
        deleteNamesDBWrap($name['ID'], $overwrite);
    }
}

/**
 * restore all picture poi links for a ceratin poi
 * @param int $poiid unique identifier of poi
 */
function restoreNamesByPoi($poiid)
{
    $names = getNamesByPoiId($poiid);
    foreach ($names as $name) {
        updateDeletionStateNamesByID($name['ID'], false);
    }
}

/**
 * delete all story poi links for a ceratin poi
 * @param int $poiid unique identifier of poi
 * @param bool $overwrite force direkt delete
 */
function deletePoiStoryByPoi($poiid, $overwrite = false)
{
    $links = getPoiForStoryByPoiId($poiid);
    foreach ($links as $link) {
        deletePoiStoryLinkByIDDBWrap($link['id'], $overwrite);
        if ($overwrite == false) {
            updateDeletionPoiStateLinkPoiStoryByID($link['id'], true);
        }
    }
}

/**
 * restore all story poi links for a ceratin poi
 * @param int $poiid unique identifier of poi
 */
function restorePoiStoryByPoi($poiid)
{
    $links = getPoiForStoryByPoiId($poiid);
    foreach ($links as $link) {
        updateDeletionPoiStateLinkPoiStoryByID($link['id'], false);
        updateDeletionStateLinkPoiStoryByID($link['id'], false);
    }
}

/**
 * delete all picture poi links for a ceratin poi
 * @param int $poiid unique identifier of poi
 * @param bool $overwrite force direkt delete
 */
function deleteSeatsByPoi($poiid, $overwrite = false)
{
    $SeatsCounters = getSeatsByPoiId($poiid);
    foreach ($SeatsCounters as $SeatsCounter) {
        deleteSeatsDBWrap($SeatsCounter['ID'], $overwrite);
    }
}

/**
 * restore all picture poi links for a ceratin poi
 * @param int $poiid unique identifier of poi
 */
function restoreSeatsByPoi($poiid)
{
    $SeatsCounters = getSeatsByPoiId($poiid);
    foreach ($SeatsCounters as $SeatsCounter) {
        updateDeletionStateSeatsById($SeatsCounter['ID'], false);
    }
}

/**
 * delete all picture poi links for a ceratin poi
 * @param int $poiid unique identifier of poi
 * @param bool $overwrite force direkt delete
 */
function deleteHistAddressByPoi($poiid, $overwrite = false)
{
    $histAddresses = getHistoricalAddressesByPoiId($poiid);
    foreach ($histAddresses as $histAddresse) {
        deleteHistAddressDBWrap($histAddresse['ID'], $overwrite);
    }
}

/**
 * restore all picture poi links for a ceratin poi
 * @param int $poiid unique identifier of poi
 */
function restoreHistAddressByPoi($poiid)
{
    $histAddresses = getHistoricalAddressesByPoiId($poiid);
    foreach ($histAddresses as $histAddresse) {
        updateDeletionStateHistAddressById($histAddresse['ID'], false);
    }
}

/**
 * delete all picture poi links for a ceratin poi
 * @param int $poiid unique identifier of poi
 * @param bool $overwrite force direkt delete
 */
function deleteOperatorsByPoi($poiid, $overwrite = false)
{
    $operators = getOpertorsByPoiId($poiid);
    foreach ($operators as $operator) {
        deleteOperatorsDBWrap($operator['ID'], $overwrite);
    }
}

/**
 * restore all picture poi links for a ceratin poi
 * @param int $poiid unique identifier of poi
 */
function restoreOperatorsByPoi($poiid)
{
    $operators = getOpertorsByPoiId($poiid);
    foreach ($operators as $operator) {
        updateDeletionStateOperatorsById($operator['ID'], false);
    }
}

/**
 * delete all picture poi links for a ceratin poi
 * @param int $poiid unique identifier of poi
 * @param bool $overwrite force direkt delete
 */
function deletePoiPicLinkByPoi($poiid, $overwrite = false)
{
    $links = getLinkIdsForPoi($poiid);
    foreach ($links as $link) {
        deletePoiPicLinkByIDDBWrap($link['id'], $overwrite);
        if ($overwrite == false) {
            updateDeletionPoiStateLinkPoiPicByID($link['id'], true);
        }
    }
}

/**
 * restores all picture poi links for a ceratin poi
 * @param int $poiid unique identifier of poi
 * @param bool $overwrite force direkt delete
 */
function restorePoiPicLinkByPoi($poiid, $overwrite = false)
{
    $links = getLinkIdsForPoi($poiid);
    foreach ($links as $link) {
        updateDeletionPoiStateLinkPoiPicByID($link['id'], false);
        updateDeletionStateLinkPoiPicByID($link['id'], false);
    }
}

/**
 * Get Data for certain material from Cosp
 * @param string $token unique Identifier of Material
 * @return array result transmitted by cosp
 */
function GetDataForSingleMaterial($token)
{
    $value = getValidationValue();
    $result = ApiCall(array(
            "rank-val" => $value,
            "username" => $_SESSION['username'],
            "pictureToken" => $token)
        , "gsm");
    return $result;
}

/**
 * Saves Data from a Single material to cosp
 * @param string $token unique identifier of Material
 * @param string $title title of Material
 * @param string $description description  of Material
 * @param string $source source of picture
 * @param int $sourceType identifier of type of source
 * @return array result from api request to cosp
 */
function SaveDataForSingleMaterial($token, $title, $description, $source = "", $sourceType = PHP_INT_MIN)
{
    $value = getValidationValue();
    if ($sourceType > 0 && $source != "") {
        $result = ApiCall(array(
            "rank-val" => $value,
            "username" => $_SESSION['username'],
            "pictureToken" => $token,
            "title" => $title,
            "description" => $description,
            "source" => $source,
            "sourceid" => $sourceType
        ), "ssm");
        return $result;
    }
    $result = ApiCall(array(
            "rank-val" => $value,
            "username" => $_SESSION['username'],
            "pictureToken" => $token,
            "title" => $title,
            "description" => $description)
        , "ssm");
    return $result;
}

/**
 * Saves edited Story to Cosp
 * @param string $token unique identifier of Story
 * @param string $title title of story
 * @param string $story content of Story
 * @return array result from save to cosp
 */
function saveStoryEditedDataToCose($token, $title, $story)
{
    $result = ApiCall(array(
            "username" => $_SESSION['username'],
            "storytoken" => $token,
            "title" => $title,
            "story" => $story)
        , "eus");
    return $result;
}

/**
 * reset user password by username
 * @param string $username username of user which password should be reseted
 * @return array result from password change request to cosp
 */
function resetUserPassword($username)
{
    $result = ApiCall(array(
            "username" => $username,
        )
        , "rup");
    return $result;
}

/**
 * completes Information about POI Names
 * @param int $poiid ID of selected POI
 * @param string $main_name Name which is displayed as header
 * @return array complete information about all POI names
 */
function getCompleteInformationOfPoiNames($poiid, $main_name)
{
    $list = getNamesByPoiId($poiid);
    for ($i = 0; $i < count($list); $i++) {
        $list[$i]['validatable'] = (getValidateSumName($list[$i]['ID']) >= 400) || (in_array($list[$i]['ID'], getValidationsByUserForPoiNames())) || ($_SESSION['role'] <= config::$ROLE_UNAUTH_USER) ? false : true;
        $list[$i]['editable'] = (getValidateSumName($list[$i]['ID']) < 400) || ($_SESSION['role'] >= config::$ROLE_ADMIN);
        $list[$i]['deleted'] = $list[$i]['deleted'] == 1;
    }
    $result = array_merge(array(array('name' => $main_name, 'start' => '', 'end' => '')), $list);
    return $result;
}

/**
 * completes Information about POI Operators
 * @param int $poiid ID of selected POI
 * @return array complete information about all POI operators
 */
function getCompleteInformationOfPoiOperators($poiid)
{
    $list = getOpertorsByPoiId($poiid);
    for ($i = 0; $i < count($list); $i++) {
        if ((getValidateSumOperator($list[$i]['ID']) >= 400) || ($_SESSION["role"] >= config::$ROLE_AUTH_USER)) {
            $list[$i]['validatable'] = (getValidateSumOperator($list[$i]['ID']) >= 400) || (in_array($list[$i]['ID'], getValidationsByUserForPoiOperators())) || ($_SESSION['role'] <= config::$ROLE_UNAUTH_USER) ? false : true;
            $list[$i]['editable'] = (getValidateSumOperator($list[$i]['ID']) < 400) || ($_SESSION['role'] >= config::$ROLE_EMPLOYEE);
            $list[$i]['deleted'] = $list[$i]['deleted'] == 1;
        }
    }
    return $list;
}

/**
 * completes Information about POI Operators
 * @param int $poiid ID of selected POI
 * @return array complete information about all POI operators
 */
function getCompleteInformationOfPoiHistAddress($poiid)
{
    $list = getHistoricalAddressesByPoiId($poiid);
    for ($i = 0; $i < count($list); $i++) {
        if ((getValidateSumHistAddress($list[$i]['ID']) >= 400) || ($_SESSION["role"] >= config::$ROLE_AUTH_USER)) {
            $list[$i]['validatable'] = (getValidateSumHistAddress($list[$i]['ID']) >= 400) || (in_array($list[$i]['ID'], getValidationsByUserForPoiHistAddresses())) || ($_SESSION['role'] <= config::$ROLE_UNAUTH_USER) ? false : true;
            $list[$i]['editable'] = (getValidateSumHistAddress($list[$i]['ID']) < 400) || ($_SESSION['role'] >= config::$ROLE_ADMIN);
            $list[$i]['deleted'] = $list[$i]['deleted'] == 1;
        }
    }
    return $list;
}

/**
 * sends request to delete picture
 * @param string $picToken unique identifier of token as sha512
 * @return array state of request
 */
function deleteMaterial($picToken)
{
    $result = ApiCall(array(
            "pictureToken" => $picToken,
        )
        , "dsp");
    return $result;
}

/**
 * requests multiple stories from cosp
 * @param array $tokenList array with required Information
 * @return array result of request
 */
function getStoriesAsListFromCose($tokenList)
{
    $result = ApiCall(array(
            "tokenList" => $tokenList,
        )
        , "gsl");
    return $result;
}

/**
 * sends delete Request to Cosp
 * @param array $data structures required Information
 * @return array state of request as result
 */
function deleteUserstoryInCose($data)
{
    $result = ApiCall(array(
            "data" => $data,
        )
        , "dus");
    return $result;
}

/**
 * gets all validators of a certain poi pic link
 * @param int $lid poi pic link id
 * @return array structured result
 */
function GetPoiPicLinkValidators($lid)
{
    $link = getAllValidatedForPoiPicLink($lid);
    $result = array();
    foreach ($link as $l) {
        $result[] = $l['name'];
    }
    return $result;
}

/**
 * completes Information about POI Seats
 * @param int $poiid ID of selected POI
 * @return array complete information about all POI names
 */
function getCompleteInformationOfPoiSeats($poiid)
{
    $list = getSeatsByPoiId($poiid);
    for ($i = 0; $i < count($list); $i++) {
        $list[$i]['validatable'] = (getValidateSumSeats($list[$i]['ID']) >= 400) || (in_array($list[$i]['ID'], getValidationsByUserForPoiSeats())) || ($_SESSION['role'] <= config::$ROLE_UNAUTH_USER) ? false : true;
        $list[$i]['editable'] = (getValidateSumSeats($list[$i]['ID']) < 400) || ($_SESSION['role'] >= config::$ROLE_EMPLOYEE);
        $list[$i]['deleted'] = $list[$i]['deleted'] == 1;
    }
    $result = $list;
    return $result;
}

/**
 * get all ids of validated names based on user id
 * @param int $uid unique user identifier (user-id or uid) by default id of current caller
 * @return array list of points of interests validated by user
 */
function getValidationsByUserForPoiSeats($uid = -1)
{
    if ($_SESSION['username'] === 'gast') {
        return array();
    }
    if ($uid === -1) {
        $uid = getUserData($_SESSION['username'])['id'];
    }
    $validates = getAllValidatedForPoiSeats();
    $result = array();
    dump($validates, 8);
    foreach ($validates as $val) {
        if ($val['uid'] == $uid) {
            if (in_array($val['seats_id'], $result)) {
                continue;
            }
            $result[] = $val['seats_id'];
        }
    }
    dump($result, 3);
    return $result;
}

/**
 * add validation to seats of point of interest
 * @param int $seatid id of seats which should be validated by user
 */
function validatePoiSeats($seatid)
{
    $value = getValidationValue();
    insertValidateSeats($seatid, $value);
    if ($value >= 1) {
        addRankPoints($_SESSION['username'], 2, "Hat die Sitzplatzanzahl eines Interessenpunktes validiert.");
    }
    if (getValidateSumSeats($seatid) >= 400) {
        $user = getCreatorBySeatsID($seatid);
        addRankPoints($user, 10, "Durch Nutzer wurde Sitzplatzanzahl eines Interessenpunktes validiert.");
    }
}

/**
 * completes Information about POI Seats
 * @param int $poiid ID of selected POI
 * @return array complete information about all POI names
 */
function getCompleteInformationOfPoiCinemas($poiid)
{
    $list = getCinemasByPoiId($poiid);
    for ($i = 0; $i < count($list); $i++) {
        $list[$i]['validatable'] = (getValidateSumCinemas($list[$i]['ID']) >= 400) || (in_array($list[$i]['ID'], getValidationsByUserForPoiCinemas())) || ($_SESSION['role'] <= config::$ROLE_UNAUTH_USER) ? false : true;
        $list[$i]['editable'] = (getValidateSumCinemas($list[$i]['ID']) < 400) || ($_SESSION['role'] >= config::$ROLE_EMPLOYEE);
        $list[$i]['deleted'] = $list[$i]['deleted'] == 1;
    }
    $result = $list;
    return $result;
}

/**
 * get all ids of validated names based on user id
 * @param int $uid unique user identifier (user-id or uid) by default id of current caller
 * @return array list of points of interests validated by user
 */
function getValidationsByUserForPoiCinemas($uid = -1)
{
    if ($_SESSION['username'] === 'gast') {
        return array();
    }
    if ($uid === -1) {
        $uid = getUserData($_SESSION['username'])['id'];
    }
    $validates = getAllValidatedForPoiCinemas();
    $result = array();
    dump($validates, 8);
    foreach ($validates as $val) {
        if ($val['uid'] == $uid) {
            if (in_array($val['cinemas_id'], $result)) {
                continue;
            }
            $result[] = $val['cinemas_id'];
        }
    }
    dump($result, 3);
    return $result;
}

/**
 * add validation to cinemas of point of interest
 * @param int $cinemasid id of cinemas which should be validated by user
 */
function validatePoiCinemas($cinemasid)
{
    $value = getValidationValue();
    insertValidateCinemas($cinemasid, $value);
    if ($value >= 1) {
        addRankPoints($_SESSION['username'], 2, "Hat die Saalanzahl eines Interessenpunktes validiert.");
    }
    if (getValidateSumCinemas($cinemasid) >= 400) {
        $user = getCreatorByCinemasID($cinemasid);
        addRankPoints($user, 10, "Durch Nutzer wurde Saalanzahl eines Interessenpunktes validiert.");
    }
}

/**
 * get all points of interest ids of validated time spans based on user id
 * @param int $uid unique user identifier (user-id or uid) by default id of current caller
 * @return array list of points of interests validated by user
 */
function getValidationsByUserForCinemaType($uid = -1)
{
    if ($uid === -1) {
        $uid = getUserData($_SESSION['username']);
        if (isset($uid['id'])) {
            $uid = $uid['id'];
        }
    }
    $validates = getAllValidatedForCinemaTypes();
    $result = array();
    dump($validates, 8);
    foreach ($validates as $val) {
        if ($val['uid'] == $uid) {
            if (in_array($val['poi_id'], $result)) {
                continue;
            }
            $result[] = $val['poi_id'];
        }
    }
    dump($result, 3);
    return $result;
}

/**
 * add validation to type of point of interest
 * @param int $poiid id of point of interest which should be validated by user
 */
function validateTypePoi($poiid)
{
    $value = getValidationValue();
    insertValidateType($poiid, $value);
    if ($value >= 1) {
        addRankPoints($_SESSION['username'], 2, "Hat Art des Kinos eines Interessenpunktes validiert.");
    }
    if (getValidateSumType($poiid) >= 400) {
        $user = getUserDataById(getPoi($poiid)['creator_type'])['name'];
        addRankPoints($user, 10, "Durch Nutzer wurde Art des Kinos eines Interessenpunktes validiert.");
    }
}

/**
 * Redirects to Index.php if you where already inside
 */
function RedirectMainBetaIndex()
{
    if (config::$MAINTENANCE) {
        Redirect('index.php?m=0');
    } else if (config::$BETA) {
        Redirect('index.php?b=0');
    }
}

/**
 * approves or disapproves story
 * @param string $story_token unique story identifier
 * @param bool $state state of approval, true --> approved, false --> disapproved
 *
 */
function SendStoryApprovalChange($story_token, $state)
{
    $type = $state ? "asa" : "das";
    ApiCall(array('username' => $_SESSION['username'], 'storie_token' => $story_token), $type);
}

/**
 * requests a capture code from COSP
 * @return array structured result
 */
function GetCaptchaFromCose()
{
    return ApiCall(array("special" => config::$SPECIAL_CHARS_CAPTCHA), "gca")['data'];
}

/**
 * Calls Cosp API function to send Mail to Admins
 * @param string $mail mailadress for reply-to header
 * @param string $subject subject of mail
 * @param string $msg content of mail
 * @return array structured result data from cosp
 */
function sendContactMail($mail, $subject, $msg)
{

    $params = array(
        "username" => $_SESSION['username'],
        "title" => $subject,
        "msg" => $msg,
        "email" => $mail,
        "receiver" => config::$ZENTRAL_MAIL,
        "ip" => getUserIp()
    );
    return ApiCall($params, "scm");
}

/**
 * requests all source types from cosp
 * @return array structured result
 */
function getAllSourceTypesCose()
{
    return ApiCall(array(), "gts")['data'];
}

/**
 * get ip adress of user
 * @return string ip adress of user
 */
function getUserIp()
{
    $ip = "0.0.0.0";
    if (isset($_SERVER['HTTP_X_REAL_IP'])) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

/**
 * checks if cosp knows a certain mailadress
 * @param string $mail mailadress to check
 * @return boolean result of request
 */
function checkMailAddressExistent($mail)
{
    $request = array(
        "mail" => $mail
    );
    $return = ApiCall($request, "cma");
    return $return['data'];
}

/**
 * sends username to user via mail
 * @param string $mail mailadress of user
 */
function sendusernameByMail($mail)
{
    $request = array(
        "mail" => $mail
    );
    ApiCall($request, "sua");
}

/**
 * get data for single story
 * @return array structured array with story data
 */
function getSingleUserStoryData()
{
    return ApiCall(array('username' => $_SESSION['username'], 'rank-val' => getValidationValue()), 'gus');
}

/**
 * generates random string
 * @param int $length sets length of random string
 * @param bool $special if true special chars are added
 * @return string random string with certain length
 */
function generateRandomString($length, $special = false)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    if ($special) {
        $characters = $characters . ",.;:-_+#/*";
    }
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/**
 * generates a hmac string from a given string with the hmac secret from config.php
 * @param string $string inout string
 * @return string finished hmac
 */
function generateStringHmac($string)
{
    if (!is_string($string)) {
        die("No String!");
    }
    $hmac = hash_hmac("sha512", $string, config::$HMAC_SECRET);
    return $hmac;
}
