<?php
/**
 * Contact formular, to get in contact with users
 *
 * @package default
 */

/**
 * @const enables loading of other files without dying to improve security
 */
define('NICE_PROJECT', true);
require_once "bin/inc.php";
if (isset($_SESSION['username']) == false) {
    Redirect('index.php');
}
if ($_SESSION['role'] < config::$ROLE_EMPLOYEE) {
    Redirect('index.php');
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Ankündigungen</title>
    <?php
    generateHeaderTags(
        array(
            array(
                "type" => "link",
                "rel" => "stylesheet",
                "href" => "css/announcement.css",
                "hrefmin" => "css/announcement.min.css"
            ),
            array(
                "type" => "script",
                "typeval" => "text/javascript",
                "href" => "js/announcement.js",
                "hrefmin" => "js/announcement.min.js",
            ),
            array(
                "type" => "script",
                "typeval" => "text/javascript",
                "href" => "jse/jtsage-datebox.js",
                "hrefmin" => "jse/jtsage-datebox.min.js",
            ),
            array(
                "type" => "script",
                "typeval" => "text/javascript",
                "href" => "jse/jtsage-datebox.i18n.de.utf8.js",
                "hrefmin" => "jse/jtsage-datebox.i18n.de.utf8.min.js",
            )
        )
    );
    ?>
</head>
<body style="height: auto">
<?php
generateHeader(isset($_SESSION['username']), $lang);
?>
<div class="container mx-auto mt-4 text-light pt-5">
    <div class="justify-content-center mx-auto">
        <h1>Ankündigungen</h1>
        <div class="add-an-btn show-ratio show-736"><button onclick="$('#AddAnnouncementModal').modal();"
                                      class="btn btn-sq btn-secondary" data-toggle="tooltip" data-placement="top"
                                      title="Hinzufügen">
                <img src="images/plus-solid.svg" width="15px" style="margin-top: -2px">
            </button></div>
        <table class="table table-dark do-reflow reflow-ratio reflow-20">
            <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Titel</th>
                <th scope="col">Text</th>
                <th scope="col">Start</th>
                <th scope="col">Ende</th>
                <th scope="col">Ersteller</th>
                <th scope="col">
                    <button onclick="$('#AddAnnouncementModal').modal();"
                            class="btn btn-sq btn-secondary ml-1 mr-1" data-toggle="tooltip" data-placement="top"
                            title="Hinzufügen">
                        <img src="images/plus-solid.svg" width="15px" style="margin-top: -2px">
                    </button>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php
            $announcements = getAllAnnouncements();
            foreach ($announcements as $announcement) {
                ?>
                <tr>
                    <td><?php echo $announcement['id'] ?></td>
                    <td><?php echo $announcement['title'] ?></td>
                    <td><?php
                        $content = $announcement['content'];
                        if (strlen($content) > 50) {
                            $content = substr($content, 0, 47) . "...";
                        }
                        echo $content;
                        ?></td>
                    <td><?php
                        $starttime = strtotime($announcement['start']);
                        echo date('d.m.Y', $starttime);
                        ?></td>
                    <td><?php
                        $endtime = strtotime($announcement['end']);
                        echo date('d.m.Y', $endtime);
                        ?></td>
                    <td><?php
                        echo $announcement['creator'];
                        ?></td>
                    <td class="reflow-highlight-bg reflow-center reflow-hide-head">
                        <button onclick="openAnnouncementPreview(<?php echo $announcement['id'] ?>);"
                                class="btn btn-sq btn-secondary m-1" data-toggle="tooltip" data-placement="top"
                                title="Vorschau">
                            <img src="images/eye-solid.svg" width="25px" style="margin-top: -2px">
                        </button>
                        <button onclick="openEditAnnouncement(<?php echo $announcement['id'] ?>);"
                                class="btn btn-sq btn-secondary m-1" data-toggle="tooltip" data-placement="top"
                                title="Bearbeiten">
                            <img src="images/pencil-alt-solid.svg" width="15px" style="margin-top: -2px">
                        </button>
                        <button onclick="deleteAnnouncement(<?php echo $announcement['id'] ?>, this);"
                                class="btn btn-sq btn-secondary m-1" data-toggle="tooltip" data-placement="top"
                                title="Löschen">
                            <img src="images/trash-alt-solid.svg" width="15px" style="margin-top: -2px">
                        </button>
                        <?php
                        if ($announcement['enable'] == 0) {
                            ?>
                            <button onclick="aktivateAnnouncement(<?php echo $announcement['id'] ?>);"
                                    class="btn btn-sq btn-secondary m-1" data-toggle="tooltip" data-placement="top"
                                    title="Aktivieren">
                                <img src="images/check-solid.svg" width="15px" style="margin-top: -2px">
                            </button>
                            <?php
                        } else {
                            ?>
                            <button onclick="deaktivateAnnouncement(<?php echo $announcement['id'] ?>);"
                                    class="btn btn-sq btn-secondary m-1" data-toggle="tooltip" data-placement="top"
                                    title="Deaktivieren">
                                <img src="images/check-solid-green.svg" width="15px" style="margin-top: -2px">
                            </button>
                            <?php
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade overflow-auto" id="AddAnnouncementModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header d-inline-flex align-items-baseline  rounded-top-7">
                <h5 class="modal-title" id="AddPOI" style="color: #ffffff">Ankündigung Hinzufügen</h5>
                <button type="button" class="btn btn-link" data-dismiss="modal">
                    <img src="images/times-solid.svg" width="14px">
                </button>
            </div>
            <div class="modal-body rounded-bottom-7" style="overflow: auto;">
                <div class="tab-content" id="v-pills-tabContent">
                    <div class="tab-pane fade show active" id="v-pills-cinema" role="tabpanel"
                         style="color: black;">
                        <div style="width: 760px; display: inline-block;">

                            <div class="form-group">
                                <label for="titleAnnouncementAddInput"
                                       style="color: #d2d2d2">Titel</label>
                                <input type="text" class="form-control textinput-formular"
                                       id='titleAnnouncementAddInput'
                                       name='titleAnnouncementAddInput'
                                       required="required"
                                       style="width: 760px; background-color: #3b3b3b; color: #ffffff">
                            </div>
                            <div class="form-group">
                                <label for="contentAnnouncementAddInput"
                                       style="color: #d2d2d2">Inhalt</label>
                                <textarea class="form-control border textinput-formular"
                                          name='contentAnnouncementAddInput' id='contentAnnouncementAddInput' required
                                          style="border: 3px; width: 760px; height: 300px; max-height: 550px; background-color: #3b3b3b; color: #ffffff"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="startAnnouncementAddInput"
                                       style="color: #d2d2d2">Start der Ankündigung</label>
                                <input type="date" class="form-control textinput-formular dateinput"
                                       id='startAnnouncementAddInput'
                                       name='startAnnouncementAddInput' onclick='$(this).datebox( "open" );'
                                       required="required" data-role="datebox"
                                       data-options='{"mode":"calbox", "displayMode":"modal", "theme_cal_Selected":"important", "theme_cal_Today":"secondary", "useLang":"de", "overrideDateFormat": "%Y-%m-%d"}'>
                            </div>
                            <div class="form-group">
                                <label for="EndAnnouncementAddInput"
                                       style="color: #d2d2d2">Ende der Ankündigung</label>
                                <input type="date" class="form-control textinput-formular dateinput"
                                       id='EndAnnouncementAddInput'
                                       name='EndAnnouncementAddInput' onclick='$(this).datebox( "open" );'
                                       required="required" data-role="datebox"
                                       data-options='{"mode":"calbox", "displayMode":"modal", "theme_cal_Selected":"important", "theme_cal_Today":"secondary", "useLang":"de", "overrideDateFormat": "%Y-%m-%d"}'>
                            </div>
                            <input type="button" class="btn btn-warning btn-important ml-1 mt-1 mb-1" name="speichern"
                                   onclick="addAnnouncement();"
                                   value="Speichern"
                                   style="float: right; position: relative;" id="AddAnnouncementSubmit">
                            <input type="button" class="btn btn-secondary m-1" name="speichern"
                                   onclick="addPreview();"
                                   value="Vorschau"
                                   style="float: right; position: relative;" id="AddAnnouncementSubmit">
                        </div>
                        <div id="previewAddTextDiv" hidden>
                            <hr>
                            <div class="text-light" id="previewAddText">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade overflow-auto" id="EditAnnouncementModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header d-inline-flex align-items-baseline rounded-top-7">
                <h5 class="modal-title" id="AddPOI" style="color: #ffffff">Ankündigung Ändern</h5>
                <button type="button" class="btn btn-link" data-dismiss="modal">
                    <img src="images/times-solid.svg" width="14px">
                </button>
            </div>
            <div class="modal-body rounded-bottom-7" style="overflow: auto;">
                <div class="tab-content" id="v-pills-tabContent">
                    <div class="tab-pane fade show active" id="v-pills-cinema" role="tabpanel"
                         style="color: black;">
                        <div style="width: 760px; display: inline-block;">

                            <div class="form-group">
                                <label for="titleAnnouncementEditInput"
                                       style="color: #d2d2d2">Titel</label>
                                <input type="text" class="form-control textinput-formular"
                                       id='titleAnnouncementEditInput'
                                       name='titleAnnouncementEditInput'
                                       required="required"
                                       style="width: 760px; background-color: #3b3b3b; color: #ffffff">
                                <input type="text" class="hidden" id='IdAnnouncementEditInput'
                                       name='IdAnnouncementEditInput'
                                       required="required"
                                       style="width: 760px; background-color: #3b3b3b; color: #ffffff">
                            </div>
                            <div class="form-group">
                                <label for="contentAnnouncementEditInput"
                                       style="color: #d2d2d2">Inhalt</label>
                                <textarea class="form-control border textinput-formular"
                                          name='contentAnnouncementEditInput' id='contentAnnouncementEditInput' required
                                          style="border: 3px; width: 760px; height: 300px; max-height: 550px; background-color: #3b3b3b; color: #ffffff"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="startAnnouncementEditInput"
                                       style="color: #d2d2d2">Start der Ankündigung</label>
                                <input type="date" class="form-control textinput-formular dateinput"
                                       id='startAnnouncementEditInput'
                                       name='startAnnouncementEditInput' onclick='$(this).datebox( "open" );'
                                       required="required" data-role="datebox"
                                       data-options='{"mode":"calbox", "displayMode":"modal", "theme_cal_Selected":"important", "theme_cal_Today":"secondary", "useLang":"de", "overrideDateFormat": "%Y-%m-%d"}'
                                       style="background-color: #3b3b3b; color: #ffffff">
                            </div>
                            <div class="form-group">
                                <label for="EndAnnouncementEditInput"
                                       style="color: #d2d2d2">Ende der Ankündigung</label>
                                <input type="date" class="form-control textinput-formular dateinput"
                                       id='EndAnnouncementEditInput'
                                       name='EndAnnouncementEditInput' onclick='$(this).datebox( "open" );'
                                       required="required" data-role="datebox"
                                       data-options='{"mode":"calbox", "displayMode":"modal", "theme_cal_Selected":"important", "theme_cal_Today":"secondary", "useLang":"de", "overrideDateFormat": "%Y-%m-%d"}'
                                       style="background-color: #3b3b3b; color: #ffffff">
                            </div>
                            <input type="button" class="btn btn-warning btn-important ml-1 mt-1 mb-1" name="speichern"
                                   onclick="saveEditAnnouncement();"
                                   value="Speichern"
                                   style="float: right; position: relative;" id="EditAnnouncementSubmit">
                            <input type="button" class="btn btn-secondary m-1" name="speichern"
                                   onclick="editPreview();"
                                   value="Vorschau"
                                   style="float: right; position: relative;" id="AddAnnouncementSubmit">
                        </div>
                        <div id="previewEditTextDiv" hidden>
                            <hr>
                            <div class="text-light" id="previewEditText">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade col-6 offset-3" id="PreviewAnnouncementModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered" role="document">
        <!-- because normal overflow-y: auto is displaying scrollbar next to modal and not on right side of browser window-->
        <div class="modal-content">
            <div class="modal-header d-inline-flex align-items-baseline rounded-top-7">
                <h5 class="modal-title" style="color: white" id="PreviewAnnouncementModalMainTitle"></h5>
            </div>
            <div class="modal-body modal-body-unround" style="" id="CookieModalBody">
                <div class="container">
                    <div class="text-light" id="PreviewAnnouncementModalMainContent">

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-important"
                        onclick="$('#PreviewAnnouncementModal').modal('hide');">
                    Schließen
                </button>
            </div>
        </div>
    </div>
</div>
</body>
</html>