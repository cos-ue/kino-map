<?php
/**
 * Page to Upload Pictures
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
checkPermission(config::$ROLE_UNAUTH_USER);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <?php
    $GetData = array();
    $poiid = -1;
    $map = 0;
    if (count($_GET) > 0) {
        $poiid = filter_input(INPUT_GET, "poi");
        $map = filter_input(INPUT_GET, "map");
    }
    if (count($_POST) > 0) {
        if (isset($_POST['title'], $_POST['description'], $_FILES['userfile']) == false) {
            permissionDenied("Wrong Keys in Request.");
        }
        $title = filter_input(INPUT_POST, 'title');
        $desc = filter_input(INPUT_POST, 'description');
        $rechteCheckBox = filter_input(INPUT_POST, "materialRightsCheckbox");
        $sourceType = filter_input(INPUT_POST, "sourcetype");
        $source = filter_input(INPUT_POST, "source");
        $rechteCheckBox == "on" ? $rechteCheckBox = true : $rechteCheckBox = false;
        $uploads_dir = 'images/uploadTmp';
        $newName = "";
        if ($_FILES["userfile"]["error"] == UPLOAD_ERR_OK && $title !== "" && $rechteCheckBox) { // error is 1 if file is too big
            $tmp_name = $_FILES["userfile"]["tmp_name"];
            if(function_exists('getimagesize')) {
                if(!@is_array(getimagesize($tmp_name))){
                    permissionDenied("file is no image or imagedata are corrupt.");
                }
            }
            $name = basename($_FILES["userfile"]["name"]);
            move_uploaded_file($tmp_name, "$uploads_dir/$name");
            $newName = hash_file('sha512', "$uploads_dir/$name");
            $path_parts = pathinfo($name);
            $newName = $uploads_dir . "/" . $newName . "." . $path_parts['extension'];
            rename("$uploads_dir/$name", $newName);
            $unlinkName = $newName;
            $newName = realpath($newName);
            $result = array("code" => -1);
            if ($source !== "" && $source !== null) {
                $result = UploadPicture($title, $desc, $newName, $_FILES["userfile"]["type"], $_SESSION["username"], $source, $sourceType);
            } else {
                $result = UploadPicture($title, $desc, $newName, $_FILES["userfile"]["type"], $_SESSION["username"]);
            }
            unlink($unlinkName);
            dump($result, 5);
            if ($result['code'] === 0) {
                if ($poiid > -1) {
                    updatePicForPoi($result['token'], $poiid);
                    deletevalidateByPOI($poiid);
                    updatePoiCreator($poiid);
                    Redirect("editPoi.php?" . http_build_query(array("poi" => $poiid, "map" => $map), '', '&'));
                } else {
                    Redirect("ListMaterial.php");
                }
            }
        }
    }
    ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Kino Karte - Material hinzufügen</title>
    <?php
    generateHeaderTags(
        array(
            array(
                "type" => "script",
                "typeval" => "text/javascript",
                "href" => "js/pictureUploadNew.js",
                "hrefmin" => "js/pictureUploadNew.min.js"
            )
        )
    );
    ?>
</head>

<body style="height: auto">
<?php
generateHeader(true, $lang);
?>
<div class="container text-light pt-3">
    <div class="offset-4" style="margin-top: 50px">
        <h1>Bilder hochladen</h1>
        <form enctype="multipart/form-data" method="post" name="pictureUpload"
            <?php
            if ($poiid > -1) {
                ?>
                action="MaterialUpload.php?<?php echo http_build_query(array("poi" => $poiid, "map" => $map), '', '&amp;'); ?>"
                <?php
            } else {
                ?>
                action="MaterialUpload.php"
                <?php
            }
            ?>
        >
            <label class="weiß2">Titel*</label>
            <input class="form-control col-6 textinput" id="title" type="text" name="title" required><br>
            <label class="weiß2">Beschreibung</label>
            <textarea class="form-control col-6 textinput" type="text" name="description" rows="5"></textarea><br>
            <label class="weiß2">Quellentyp</label>
            <select name="sourcetype" id="sourcetype"
                    class="form-control col-6 dropdown-list-dark">
                <?php
                $srcTypes = getAllSourceTypesCose();
                foreach ($srcTypes as $srcType) {
                    ?>
                    <option value="<?php echo $srcType['id'] ?>"><?php echo $srcType['name'] ?></option>
                    <?php
                }
                ?>
            </select><br>
            <label class="weiß2">Quelle</label>
            <input class="form-control col-6 textinput" id="source" type="text" name="source"><br>
            <div class="input-group mb-3">
                <input type="hidden" name="MAX_FILE_SIZE" value="209715200"/>
                <input type="file" id="imgInp" required accept="image/png, image/jpeg, image/gif" name="userfile">
                <button class="btn btn-sq btn-warning" type="button"
                        onclick="document.getElementById('imgInp').click();">
                    <img src="images/image-regular.svg" width="25px">
                </button>
                <input type="text" id="imageName" class="form-control col-6" value="Datei auswählen*"
                       style="background-color: transparent; border-color: transparent; color: white" readonly>
            </div>
            <div class="form-group" style="width: 50%;">
                <table>
                    <tr>
                        <td class="align-top pr-2">
                            <input type="checkbox" id="materialAddRightsCheckbox" name="materialRightsCheckbox"
                                   class="hidden"
                                   required>
                            <div class="d-inline-flex justify-content-center align-items-center checkbox-green"
                                 onclick="document.getElementById('materialAddRightsCheckbox').click();">
                                <img class="checkbox-check" src="images/check-solid.svg">
                            </div>
                        </td>
                        <td>
                            <label style="color: #d2d2d2" for="materialAddRightsCheckbox"><strong>Hiermit
                                    bestätige ich, dass ich die Rechte am Bild besitze und dieses Veröffentlichen
                                    möchte.*</strong></label>
                        </td>
                    </tr>
                </table>
            </div>
            <p style="color: #d2d2d2">
                Alle mit * markierten Felder sind Pflichtfelder.
            </p>
            <div>
                <input class="btn btn-success col-2 offset-4 btn-important" name="submitrequest" type="submit"
                       value="Hochladen">
            </div>
            <img class="mb-3" id='img-upload' style="margin-top: 1.5em"/>
        </form>
    </div>
</div>
</body>
</html>