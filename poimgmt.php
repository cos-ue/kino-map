<?php
/**
 * This file creates a overview over all poi's and provide access to some basic funtions.
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
$redirect = false;
if (count($_GET) > 0) {
    if (isset($_GET['poi'], $_GET['type']) === false) {
        die("wrong keys in array");
    }
    dump($_GET, 1);
    $GetData = array(
        "type" => filter_input(INPUT_GET, 'type'),
        "poi" => filter_input(INPUT_GET, 'poi')
    );
    dump($GetData, 3);
    if (checkPoiExists($GetData['poi'])) {
        switch ($GetData['type']) {
            case 'del':
                if ($_SESSION['role'] >= config::$ROLE_EMPLOYEE || ($_SESSION['role'] >= config::$ROLE_AUTH_USER && $_SESSION['username'] == getPoi($GetData['poi'])['username'] && (getValidateSumForPOI($GetData['poi']) < 400 || getValidateSumTimespan($GetData['poi']) < 400 || getValidateSumCurAddresse($GetData['poi']) < 400 || getValidateSumHist($GetData['poi']) < 400 || getValidateSumType($GetData['poi']) < 400 || getValidateSumForPOI($GetData['poi']) < 400))) {
                    deletePOIComplete($GetData['poi']);
                    $redirect = true;
                }
                break;
            case 'val':
                $validationsByUser = getValidationsByUserForPOI();
                $validation = getValidateSumForPOI($GetData['poi']);
                dump($validation, 3);
                if ($_SESSION['role'] >= config::$ROLE_AUTH_USER && $validation < 400 && in_array($GetData['poi'], $validationsByUser) == false) {
                    validatePoi($GetData['poi']);
                }
                break;
            case 'res':
                if ($_SESSION['role'] >= config::$ROLE_ADMIN) {
                    restorePOI($GetData['poi']);
                }
                break;
            case 'fdo':
                if ($_SESSION['role'] >= config::$ROLE_ADMIN) {
                    deletePOIComplete($GetData['poi'], true);
                }
                break;
        }
    }
}
if ($redirect) {
    Redirect('poimgmt.php');
}
$validationsByUser = getValidationsByUserForPOI();
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Kino Karte</title>
    <?php
    generateHeaderTags();
    ?>
</head>

<body style="height: auto">
<?php
generateHeader(true, $lang);
$ApiKey = getPoisForUser();
dump($ApiKey,2);
?>
<div class="container mx-auto mt-4 text-light pt-5">
    <div class="justify-content-center mx-auto">
        <h1>Eintragsverwaltung</h1>
        <table class="table table-dark do-reflow" style="height:fit-content;">
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Namen</th>
                <th scope="col">Aktuelle Adresse</th>
                <th scope="col">Ersteller</th>
                <th scope="col"></th>
            </tr>
            <?php
            foreach ($ApiKey as $API) {
                ?>
                <tr class="<?php echo $API['deleted'] ? 'deleted-row' : ''; ?>">
                    <td class="align-middle">
                        <?php
                        echo $API["poi_id"];
                        ?>
                    </td>
                    <td class="align-middle">
                        <?php
                        echo $API["name"];
                        ?>
                    </td>
                    <td class="align-middle">
                        <?php
                        if (($API["Streetname"] !== "") && ($API["Housenumber"] !== "") && ($API["Streetname"] !== null) && ($API["Housenumber"] !== null) && ($API["Postalcode"] !== "") && ($API["City"] !== "") && ($API["Postalcode"] !== null) && ($API["City"] !== null)) {
                            echo $API["Streetname"] . " " . $API["Housenumber"] . ", " . $API["Postalcode"] . " " . $API["City"];
                        } else if (($API["Housenumber"] !== "") && ($API["Housenumber"] !== null) && ($API["Postalcode"] !== "") && ($API["City"] !== "") && ($API["Postalcode"] !== null) && ($API["City"] !== null)) {
                            echo $API["Streetname"] . " " . $API["Housenumber"] . ", " . $API["Postalcode"] . " " . $API["City"];
                        } else if (($API["Streetname"] !== "") && ($API["Streetname"] !== null) && ($API["Postalcode"] !== "") && ($API["City"] !== "") && ($API["Postalcode"] !== null) && ($API["City"] !== null)) {
                            echo $API["Streetname"] . " " . $API["Housenumber"] . ", " . $API["Postalcode"] . " " . $API["City"];
                        } else if (($API["Streetname"] !== "") && ($API["Housenumber"] !== "") && ($API["Streetname"] !== null) && ($API["Housenumber"] !== null) && ($API["Postalcode"] !== "") && ($API["Postalcode"] !== null)) {
                            echo $API["Streetname"] . " " . $API["Housenumber"] . ", " . $API["Postalcode"] . " " . $API["City"];
                        } else if (($API["Streetname"] !== "") && ($API["Housenumber"] !== "") && ($API["Streetname"] !== null) && ($API["Housenumber"] !== null) && ($API["City"] !== "") && ($API["City"] !== null)) {
                            echo $API["Streetname"] . " " . $API["Housenumber"] . ", " . $API["Postalcode"] . " " . $API["City"];
                        } else if ((($API["Streetname"] !== "") && ($API["Housenumber"] !== "") && ($API["Streetname"] !== null) && ($API["Housenumber"] !== null)) && ((($API["Postalcode"] === "") && ($API["City"] === "")) | (($API["Postalcode"] === null) && ($API["City"] === null)))) {
                            echo $API["Streetname"] . " " . $API["Housenumber"];
                        } else if ((($API["Postalcode"] !== "") && ($API["City"] !== "") && ($API["Postalcode"] !== null) && ($API["City"] !== null)) && ((($API["Streetname"] === "") && ($API["Housenumber"] === "")) | (($API["Streetname"] === null) && ($API["Housenumber"] === null)))) {
                            echo $API["Postalcode"] . " " . $API["City"];
                        } else {
                            echo "";
                        }
                        ?>
                    </td>
                    <td class="align-middle">
                        <?php
                        echo $API["username"];
                        ?>
                    </td>
                    <td class="reflow-hide-head reflow-center reflow-highlight-bg">
                        <?php
                        if (!$API['deleted']) {
                            if ($_SESSION['role'] >= config::$ROLE_EMPLOYEE || ($_SESSION['role'] >= config::$ROLE_AUTH_USER && $_SESSION['username'] == $API['username'] && (getValidateSumForPOI($API["poi_id"]) < 400 || getValidateSumTimespan($API["poi_id"]) < 400 || getValidateSumCurAddresse($API["poi_id"]) < 400 || getValidateSumHist($API["poi_id"]) < 400 || getValidateSumType($API["poi_id"]) < 400 || getValidateSumForPOI($API["poi_id"]) < 400))) {
                                ?>
                                <button onclick="if (confirm('Eintrag wirklich löschen?')){location.href='poimgmt.php?<?php echo http_build_query(array('type' => "del", "poi" => $API["poi_id"]), '', '&amp;'); ?>';};"
                                        class="btn btn-sq btn-secondary m-1" data-toggle="tooltip" data-placement="top"
                                        title="Löschen">
                                    <img src="images/trash-alt-solid.svg" width="15px" style="margin-top: -2px">
                                </button>
                                <?php
                            }
                            if ($_SESSION['role'] >= config::$ROLE_EMPLOYEE || ($_SESSION['role'] >= config::$ROLE_AUTH_USER && (getValidateSumForPOI($API["poi_id"]) < 400 || getValidateSumTimespan($API["poi_id"]) < 400 || getValidateSumCurAddresse($API["poi_id"]) < 400 || getValidateSumHist($API["poi_id"]) < 400 || getValidateSumType($API["poi_id"]) < 400 || getValidateSumForPOI($API["poi_id"]) < 400))) {
                                ?>
                                <button onclick="location.href='editPoi.php?<?php echo http_build_query(array("poi" => $API["poi_id"]), '', '&amp;'); ?>'"
                                        class="btn btn-sq btn-secondary m-1" data-toggle="tooltip" data-placement="top"
                                        title="Bearbeiten">
                                    <img src="images/pencil-alt-solid.svg" width="15px" style="margin-top: -2px">
                                </button>
                                <?php
                            }
                            $validation = getValidateSumForPOI($API["poi_id"]);
                            dump($validation, 3);
                            if ($_SESSION['role'] >= config::$ROLE_AUTH_USER && $validation < 400 && in_array($API["poi_id"], $validationsByUser) == false) {
                                $validate = "if (confirm('Interessenpunkt wirklich validieren?')){location.href='poimgmt.php?";
                                $validate .= http_build_query(array('type' => "val", "poi" => $API["poi_id"]), '', '&amp;');
                                $validate .= "'}";
                                ?>
                                <button onclick="<?php echo $validate ?>" class="btn btn-sq btn-secondary m-1"
                                        data-toggle="tooltip" data-placement="top" title="Validieren">
                                    <img src="images/check-solid.svg" width="15px" style="margin-top: -2px">
                                </button>
                                <?php
                            } else if ($validation >= 400 || in_array($API["poi_id"], $validationsByUser)) {
                                ?>
                                <button class="btn btn-sq btn-secondary disabled-ng disabled m-1" data-toggle="tooltip"
                                        data-placement="top" title="Validiert">
                                    <img src="images/check-solid-green.svg" width="15px" style="margin-top: -2px">
                                </button>
                                <?php
                            }
                            ?>
                            <button onclick="setCookie('LatPoi', <?php echo $API['lat'] ?>, 5);setCookie('LngPoi', <?php echo $API['lng'] ?>, 5); location.href = 'map.php'" class="btn btn-sq btn-secondary disabled-ng disabled m-1" data-toggle="tooltip" data-placement="top" title="Auf Karte Anzeigen">
                                <img src="images/map-marker-alt-solid.svg" width="15px" style="margin-top: -2px"></button>
                            </button>
                            <?php
                        } else {
                            $restore = "if (confirm('Interessenpunkt wirklich wiederherstellen?')){location.href='poimgmt.php?";
                            $restore .= http_build_query(array('type' => "res", "poi" => $API["poi_id"]), '', '&amp;') . "'}";
                            $delete = "if (confirm('Interessenpunkt wirklich endgültig löschen?')){location.href='poimgmt.php?";
                            $delete .= http_build_query(array('type' => "fdo", "poi" => $API["poi_id"]), '', '&amp;') . "'}";
                            ?>
                            <button onclick="<?php echo $delete ?>" class="btn btn-sq btn-secondary m-1"
                                    data-toggle="tooltip" data-placement="top" title="Löschen">
                                <img src="images/trash-alt-solid-red.svg" width="15px" style="margin-top: -2px">
                            </button>
                            <button onclick="<?php echo $restore ?>" class="btn btn-sq btn-secondary m-1"
                                    data-toggle="tooltip" data-placement="top" title="Wiederherstellen">
                                <img src="images/trash-restore-solid-dark-green.svg" width="15px"
                                     style="margin-top: -2px">
                            </button>
                            <?php
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>
    </div>
</div>
</body>

</html>