<?php
/**
 * Page to upload user story
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
if (config::$ENABLE_STORIES == false) {
    Redirect("index.php");
}
header('Access-Control-Allow-Origin: ' . config::$USAPI);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Kino Karte - Geschichten</title>
    <?php
    generateHeaderTags(
        array(
            array(
                "type" => "script",
                "typeval" => "text/javascript",
                "href" => "js/StoryUpload.js",
                "hrefmin" => "js/StoryUpload.min.js"
            ),
        )
    );
    ?>
</head>

<body style="height: auto">
<?php
generateHeader(true, $lang);
?>
<div class="container mx-auto mt-4 text-light pt-5">
    <div class="row">
        <h1 class="col-auto">Erfahrungsberichte</h1>
        <button type="button" class="btn btn-sq btn-success" data-toggle="modal"
                data-target="#AddStoryWarningModal"
                style="margin-left: 10px;" title="Hinzufügen">
            <img src="images/plus-solid-black.svg" width="15px" style="margin-top: -2px">
        </button>
        <div class="col d-flex justify-content-end">
            <div>
                <input type="checkbox" id="approved_story" class="form-check-input hidden" onclick="FilterStorys();">
                <div class="d-inline-flex justify-content-center align-items-center checkbox-green"
                     onclick="$(this).prev(':checkbox').click();">
                    <img class="checkbox-check" src="images/check-solid.svg">
                </div>
                <label for="approved_story" class="form-check-label mr-4">Freischaltbare anzeigen</label>
            </div>
            <div>
                <input type="checkbox" id="unvalidated_story" class="form-check-input hidden" onclick="FilterStorys();">
                <div class="d-inline-flex justify-content-center align-items-center checkbox-green"
                     onclick="$(this).prev(':checkbox').click();">
                    <img class="checkbox-check" src="images/check-solid.svg">
                </div>
                <label for="unvalidated_story" class="form-check-label mr-4">Validierbare anzeigen</label>
            </div>
            <div>
                <button class="btn btn-sq-sm btn-success" data-toggle="tooltip"
                        data-placement="top" title="Absteigend"
                        onclick="updateSortType(true);">
                    <img src="images/chevron-down-solid.svg" width="15px" style="margin-top: -2px"></button>
                <button class="btn btn-sq-sm btn-success" data-toggle="tooltip" data-placement="top"
                        title="Aufsteigend" onclick="updateSortType(false);"><img
                            src="images/chevron-up-solid.svg"
                            width="15px"
                            style="margin-top: -2px"></button>
                <label class="form-check-label ml-1">Sortierung</label>
            </div>
        </div>
    </div>
    <div id="stories" class="mt-5">
    </div>
</div>
<div class="modal fade col-6 offset-3" id="LongStory" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header d-inline-flex align-items-baseline rounded-top-7">
                <h5 class="modal-title" style="color: #ffffff" id="StoryTitle"></h5>
                <button type="button" class="btn btn-link" data-dismiss="modal">
                    <img src="images/times-solid.svg" width="14px">
                </button>
            </div>
            <div class="modal-body rounded-bottom-7" style="color: black">
                <div id="StoryLongText" class="mt-3 mr-5 ml-3 text-light">

                </div>
                <p id="StoryLongNameDate" class="ml-4 mt-3" style="font-size: 0.8em; color: #c2c2c2"></p>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="MeineKommentareTb" role="tabpanel">
                    </div>
                </div>
                <hr>
                <div class="mt-3 mr-5 ml-3 text-light" id="ButtonsLongStoryShowMoreUl">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade overflow-auto" id="AddStoryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header d-inline-flex align-items-baseline rounded-top-7">
                <h5 class="modal-title" id="AddPOI" style="color: #ffffff">Geschichte Hinzufügen</h5>
                <button type="button" class="btn btn-link" data-dismiss="modal">
                    <img src="images/times-solid.svg" width="14px">
                </button>
            </div>
            <div class="modal-body rounded-bottom-7" style="overflow: auto;">
                <div class="tab-content" id="v-pills-tabContent">
                    <div class="tab-pane fade show active" id="v-pills-cinema" role="tabpanel"
                         style="color: black;">
                        <form name="cinema" id="formAddStoryModal">
                            <div style="width: 760px; display: inline-block;">

                                <div class="form-group">
                                    <label for="titleInput"
                                           style="color: #d2d2d2">Titel*</label>
                                    <input type="text" class="form-control textinput-formular" id='storyAddStoryTitle'
                                           name='titleInput'
                                           required="required"
                                           style="width: 760px; background-color: #3b3b3b; color: #ffffff">
                                </div>
                                <div class="form-group">
                                    <label for="storyInput"
                                           style="color: #d2d2d2">Geschichte*</label>
                                    <textarea class="form-control border textinput-formular"
                                              name='storyAddStoryInput' id='storyAddStoryInput' required
                                              style="border: 3px; width: 760px; height: 300px; max-height: 550px; background-color: #3b3b3b; color: #ffffff"></textarea>
                                </div>
                                <div class="form-group">
                                    <input type="checkbox" id="storyAddRightsCheckbox" name="storyRightsCheckbox"
                                           class="hidden" required>
                                    <div class="d-inline-flex justify-content-center align-items-center checkbox-green"
                                         onclick="$(this).prev(':checkbox').click();">
                                        <img class="checkbox-check" src="images/check-solid.svg">
                                    </div>
                                    <label for="storyAddRightsCheckbox" style="color: #d2d2d2">Hiermit gebe ich Rechte
                                        ab.</label>
                                </div>
                                <p style="color: #d2d2d2">
                                    Alle mit * markierten Felder sind Pflichtfelder.
                                </p>
                                <input type="button" class="btn btn-warning btn-important" name="speichern"
                                       onclick="addStory();"
                                       value="Speichern"
                                       style="float: right; position: relative;" id="poisubmit">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="AddStoryWarningModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header d-inline-flex align-items-baseline rounded-top-7">
                <h5 class="modal-title" id="AddPOI" style="color: #ffffff">Geschichte Hinzufügen</h5>
                <button type="button" class="btn btn-link" data-dismiss="modal">
                    <img src="images/times-solid.svg" width="14px">
                </button>
            </div>
            <div class="modal-body rounded-bottom-7" style="overflow: auto;">
                <h5 style="color: white">Lieber Hobbyforscher,</h5><br>
                <p class="weiß2">
                    diese Funktion dient zum Hochladen von kurzen Geschichten, weswegen die Zeichenanzahl auf 3000
                    beschränkt ist. Wenn Sie mehr zu erzählen haben, sprechen Sie uns bitte an. Sollten Sie etwas länger
                    dafür brauchen, Ihre Geschichte abzutippen, so kann es leider vorkommen, dass alles, was Sie bisher
                    geschrieben haben plötzlich gelöscht wird. Deshalb raten wir dazu, alles in einem externen Programm
                    wie Word oder einem Texteditor zu schreiben. Wenn Sie fertig sind, können Sie den Text in das
                    Formular kopieren.
                </p>
                <h5 style="color: white">Gutes Gelingen!
                    <span data-toggle="tooltip" data-placement="top" title="Weiter">
                        <button type="button" class="btn btn-sq-sm btn-success ml-3 btn-important" data-toggle="modal"
                                data-target="#AddStoryModal"
                                onclick="$('#AddStoryWarningModal').modal('hide')">
                            <img src="images/chevron-right-solid-white.svg" width="10px">
                        </button>
                    </span>
                </h5>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="poiLinks" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header d-inline-flex align-items-baseline rounded-top-7">
                <h5 class="modal-title" id="StoryTitleLinks" style="color: #ffffff"></h5>
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
                    <tbody id="poiLinkTabelBody">

                    </tbody>
                    <?php if ($_SESSION['role'] >= config::$ROLE_AUTH_USER) {
                        ?>
                        <tfoot>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <select id="LinkPoiStorySelect" name="LinkPoiStorySelect"
                                            class="form-control selectinput-formular"></select>
                                </div>
                                <input id="LinkPoiStoryToken" name="LinkPoiStoryToken" value="-1" class="hidden">
                            </td>
                            <td>
                                <button class="btn btn-sq btn-secondary" data-toggle="tooltip" data-placement="top"
                                        title=""
                                        data-original-title="Speichern" onclick="saveLinkedPoi();"><img
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
<div class="modal fade" id="EditStories" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header d-inline-flex align-items-baseline rounded-top-7">
                <h5 class="modal-title" style="color: white">Geschichte bearbeiten</h5>
                <button type="button" class="btn btn-link" data-dismiss="modal">
                    <img src="images/times-solid.svg" width="14px">
                </button>
            </div>
            <div class="modal-body rounded-bottom-7">
                <div id="wichtig">
                    <form name="formEditStorie" action="#" id="formStorieEdit"
                          enctype="multipart/form-data" accept-charset="utf-8">
                        <label for="title" style="color: #d2d2d2">Titel</label>
                        <input type="text" class="form-control textinput-formular"
                               id='StorieEditTitelField' name='title'
                               required="required"
                               style="background-color: #3b3b3b; color: #ffffff">
                        <label class="mt-3" for="comment" style="color: #d2d2d2">Geschichte</label>
                        <textarea class="form-control textinput-formular" id="StorieTBcommentField" name="comment"
                                  rows="5"
                                  style="background-color: #3b3b3b; color: #ffffff"></textarea>
                        <input type="hidden" name="cid" id="StorieEditTokenField">
                        <button type="button" class="btn btn-success mt-3 btn-important"
                                style="float: right; margin-top:6px; "
                                onclick="saveEditStorie();">
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
</body>
</html>