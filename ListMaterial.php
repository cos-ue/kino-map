<?php
/**
 * Page to list all pictures of certain module
 *
 * @package default
 */

/**
 * @const enables loading of other files without dying to improve security
 */
define('NICE_PROJECT', true);
require_once "bin/inc.php";
if (isset($_SESSION["username"]) == false) {
    Redirect("index.php");
    permissionDenied();
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Kino Karte - Materialübersicht</title>
    <?php
    generateHeaderTags(
        array(
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
                "hrefmin" => "csse/lightbox.min.css"
            ),
            array(
                "type" => "script",
                "typeval" => "text/javascript",
                "href" => "js/archive.js",
                "hrefmin" => "js/archive.min.js"
            ),
            array(
                "type" => "script",
                "typeval" => "text/javascript",
                "href" => "tjs/materialList.js",
                "hrefmin" => "tjs/materialList.min.js"
            ),
        )
    );
    ?>
</head>

<body style="height: auto">
<?php
generateHeader(true, $lang);
?>
<!--<script type="application/javascript">
    showSinglePicSelect();
</script>-->
<div class="container mx-auto mt-4 text-light pt-5 width-unset">
    <?php
    $Pictures = getRemotePictureList()['pics'];
    ?>
    <table class="table table-dark" style="width:device-width, height:fit-content">
        <tr>
            <th scope="col">
                Vorschaubild
            </th>
            <th scope="col">
                ID
            </th>
            <th scope="col">
                Titel
            </th>
            <th scope="col">
                Beschreibung
            </th>
            <th>
                Quelle
            </th>
            <th scope="col">
                Nutzer
            </th>
            <th scope="col">

            </th>
        </tr>
        <?php
        $validationValueUser = getValidationValue();
        foreach ($Pictures as $pic) {
            $pic['deleted'] = $pic['deleted'] == 1;
            if (($pic["validationValue"] < 400) && ($_SESSION['role'] < config::$ROLE_AUTH_USER || $validationValueUser == 0)) {
                continue;
            }
            if ($pic['deleted'] && $_SESSION['role'] < config::$ROLE_ADMIN) {
                continue;
            }
            ?>
            <tr class="<?php echo $pic['deleted'] ? 'deleted-row' : ''; ?>">
                <td>
                    <?php
                    $title = "";
                    if ($pic['title'] !== "") {
                        $title = $pic['title'];
                    }
                    if ($pic['description'] !== "") {
                        if ($pic['title'] !== "") {
                            $title = "<u><b>" . $title . "</u></b> - ";
                        }
                        $title = $title . $pic['description'];
                        if ($pic['source'] !== null && $pic['source'] !== "") {
                            $title = $title . ' Quelle: (' . $pic['sourcename'] . ') ' . $pic['source'];
                        }
                    }
                    $gpf = config::$USAPI . "?" . http_build_query(array('type' => "gpf", "data" => $pic["token"]["token"], "seccode" => $pic["token"]["seccode"], "time" => $pic["token"]["time"]), '', '&amp;');
                    $gpp = config::$USAPI . "?" . http_build_query(array('type' => "gpp", "data" => $pic["token"]["token"], "seccode" => $pic["token"]["seccode"], "time" => $pic["token"]["time"]), '', '&amp;');
                    echo '<a href="' . $gpf . '" class="d-flex" data-lightbox="ListMaterial" data-title="' . $title . '" title="Größer anzeigen" data-toggle="tooltip" data-placement="top"><img src="' . $gpp . '"></a>';
                    ?>
                </td>
                <td class="align-middle">
                    <?php
                    echo $pic['id'];
                    ?>
                </td>
                <td class="align-middle">
                    <?php
                    echo $pic['title'];
                    ?>
                </td>
                <td class="align-middle">
                    <?php
                    echo $pic['description'];
                    ?>
                </td>
                <td class="align-middle">
                    <?php
                    if ($pic['source'] !== null && $pic['source'] !== "") {
                        echo $pic['sourcename'] . ': ' . $pic['source'];
                    }
                    ?>
                </td>
                <td class="align-middle">
                    <?php
                    echo $pic['username'];
                    ?>
                </td>
                <td class="align-middle">
                    <?php
                    if (!$pic['deleted']) {
                        if ($_SESSION['role'] >= config::$ROLE_EMPLOYEE || ($pic['validationValue'] < 400 && $_SESSION['username'] == $pic['username'] && $_SESSION['role'] >= config::$ROLE_AUTH_USER)) {
                            ?>
                            <button class="btn btn-sq btn-secondary m-1"
                                    onclick="if (confirm('Material wirklich löschen?')){deleteMaterial('<?php echo explode(";", $pic["token"]["token"])[0]; ?>')}"
                                    data-toggle="tooltip" data-placement="top" title="Löschen">
                                <img src="images/trash-alt-solid.svg" width="15px" style="margin-top: -2px">
                            </button>
                            <?php
                        }
                        if ($_SESSION['role'] >= config::$ROLE_EMPLOYEE || ($pic['validationValue'] < 400 && $_SESSION['username'] == $pic['username'] && $_SESSION['role'] >= config::$ROLE_AUTH_USER)) {
                            ?>
                            <button class="btn btn-sq btn-secondary m-1"
                                    onclick="openEditMaterial('<?php echo explode(";", $pic["token"]["token"])[0]; ?>');"
                                    data-toggle="tooltip" data-placement="top" title="Bearbeiten">
                                <img src="images/pencil-alt-solid.svg" width="15px" style="margin-top: -2px">
                            </button>
                            <?php
                        }
                        if ($validationValueUser != 0) {
                            $validation = $pic['validationValue'];
                            dump($validation, 3);
                            if ($_SESSION['role'] >= config::$ROLE_AUTH_USER && $validation < 400 && in_array($_SESSION["username"], $pic["valUsers"]) == false) {
                                $valLink = config::$USAPI . "?" . http_build_query(array('type' => "vap", "data" => $pic["token"]["token"], "seccode" => $pic["token"]["seccode"], "time" => $pic["token"]["time"]), '', '&');
                                ?>
                                <button onclick="if (confirm('Bild wirklich validieren?')){validatePicture('<?php echo $valLink; ?>')}"
                                        class="btn btn-sq btn-secondary m-1" data-toggle="tooltip" data-placement="top"
                                        title="Validieren">
                                    <img src="images/check-solid.svg" width="15px" style="margin-top: -2px">
                                </button>
                                <?php
                            } else if ($validation >= 400 || in_array($_SESSION["username"], $pic["valUsers"])) {
                                ?>
                                <span class="btn btn-sq btn-secondary disabled-ng disabled m-1" data-toggle="tooltip"
                                      data-placement="top" title="Validiert">
                                    <img src="images/check-solid-green.svg" style="margin-top: 10px" width="15px">
                                </span>
                                <?php
                            }
                        }
                        ?>
                        <button class="btn btn-sq btn-secondary m-1" data-toggle="tooltip" data-placement="top"
                                title="Verknüpfte Einträge anzeigen"
                                onclick="showPoiPicLinks('<?php echo explode(';', $pic["token"]["token"])[0]; ?>', '<?php echo $pic['title']; ?> ')">
                            <img src="images/map-marker-alt-solid.svg" style="margin-top: -3px" width="13px"></button>
                        <?php
                    } else if ($_SESSION['role'] >= config::$ROLE_ADMIN && $pic['deleted']) {
                        ?>
                        <button onclick="finalDeletePicture('<?php echo $pic["token"]["token"]; ?>')" class="btn btn-sq btn-secondary mr-2" data-toggle="tooltip"
                                data-placement="top" title="Endgültig Löschen"><img src="images/trash-alt-solid-red.svg"
                                                                                    width="15px"
                                                                                    style="margin-top: -2px"></button>
                        <button onclick="restorePicture('<?php echo $pic["token"]["token"]; ?>')" class="btn btn-sq btn-secondary mr-2" data-toggle="tooltip"
                                data-placement="top" title="Wiederherstellen"><img
                                    src="images/trash-restore-solid-dark-green.svg" width="15px"
                                    style="margin-top: -2px"></button>
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
<div class="modal fade" id="EditMaterial" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header d-inline-flex align-items-baseline rounded-top-7">
                <h5 class="modal-title" style="color: white">Material bearbeiten</h5>
                <button type="button" class="btn btn-link" data-dismiss="modal">
                    <img src="images/times-solid.svg" width="14px">
                </button>
            </div>
            <div class="modal-body rounded-bottom-7">
                <div id="wichtig">
                    <div class="d-flex justify-content-center mt-2">
                        <img src="" id="ImageEditMaterialModal" style="max-width: 100%;">
                    </div>
                    <form name="formEditMaterial" action="#" id="formEditMaterial"
                          enctype="multipart/form-data" accept-charset="utf-8">
                        <label class="mt-4" for="MaterialTitelField" style="color: #d2d2d2">Titel</label>
                        <input type="text" class="form-control textinput-formular"
                               id='MaterialTitelField' name='MaterialTitelField'
                               required="required"
                               style="background-color: #3b3b3b; color: #ffffff">
                        <label class="mt-3" for="comment" style="color: #d2d2d2">Beschreibung</label>
                        <textarea class="form-control textinput-formular" id="MaterialTBcommentField" name="comment"
                                  rows="5"
                                  style="background-color: #3b3b3b; color: #ffffff"></textarea>
                        <input type="hidden" name="cid" id="MaterialTokenField">
                        <label class="mt-4" for="MaterialEditSourceTypeField" style="color: #d2d2d2">Quellentyp</label>
                        <select name="MaterialEditSourceTypeField" id="MaterialEditSourceTypeField"
                                class="form-control dropdown-list">
                            <?php
                            $srcTypes = getAllSourceTypesCose();
                            foreach ($srcTypes as $srcType){
                                ?>
                                <option value="<?php echo $srcType['id'] ?>"><?php echo $srcType['name'] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                        <label class="mt-4" for="MaterialEditSourceField" style="color: #d2d2d2">Quelle</label>
                        <input type="text" class="form-control textinput-formular"
                               id='MaterialEditSourceField' name='MaterialEditSourceField'
                               required="required"
                               style="background-color: #3b3b3b; color: #ffffff">
                        <button type="button" class="btn btn-success mt-3 btn-important"
                                style="float: right; margin-top:6px; "
                                onclick="saveEditedMaterial()">
                            Speichern
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
<div class="modal fade" id="poiPicLinks" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header d-inline-flex align-items-baseline rounded-top-7">
                <h5 class="modal-title" id="PicPoiLinksTitle" style="color: #ffffff"></h5>
                <button type="button" class="btn btn-link" data-dismiss="modal">
                    <img src="images/times-solid.svg" width="14px">
                </button>
            </div>
            <div class="modal-body rounded-bottom-7" style="color: black">
                <table class="table table-dark">
                    <thead>
                    <tr>
                        <th scope="col">
                            Namen
                        </th>
                        <th scope="col">

                        </th>
                    </tr>
                    </thead>
                    <tbody id="poiPicLinkTabelBody">

                    </tbody>
                    <?php if ($_SESSION['role'] >= config::$ROLE_AUTH_USER) {
                        ?>
                        <tfoot>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <select id="LinkPoiPicSelect" name="LinkPoiPicSelect"
                                            class="form-control selectinput-formular"></select>
                                </div>
                            </td>
                            <td>
                                <button class="btn btn-sq btn-secondary" id='LinkPoiPicSelectSavebtn'
                                        data-toggle="tooltip" data-placement="top"
                                        title=""
                                        data-original-title="Speichern"><img
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
</div>
</body>
</html>
