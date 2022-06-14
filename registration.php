<?php
/**
 * Page for self registration of users on COSP-Plattform
 *
 * @package default
 */

/**
 * @const enables loading of other files without dying to improve security
 */
define('NICE_PROJECT', true);
require_once "bin/inc.php";
$failurePWD = false;
$failureUN = false;
$failureEM = false;
$failureCP = false;
$success = false;
$failureCSRF = false;
if (isset($_SESSION["username"])) {
    Redirect("map.php");
}
if (config::$MAINTENANCE) {
    Redirect("index.php");
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <?php
    $GetData = array();
    if (count($_POST) > 0) {
        if (isset($_POST['password1'], $_POST['password2'], $_POST['username']) === false) {
            permissionDenied("Wrong Keys in Request.");
        }
        $password1 = filter_input(INPUT_POST, 'password1', FILTER_SANITIZE_STRING);
        $password2 = filter_input(INPUT_POST, 'password2', FILTER_SANITIZE_STRING);
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $firstname = "";
        $firstname = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_STRING);
        $lastname = "";
        $lastname = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
        $captcha = filter_input(INPUT_POST, 'captchaReturn', FILTER_SANITIZE_STRING);
        $csrf = filter_input(INPUT_POST, "FormTokenScriptCSRF", FILTER_SANITIZE_STRING);
        if (!checkCSRFtoken($csrf)) {
            $failureCSRF = true;
        }
        if ($_SESSION['captcha'] !== $captcha) {
            $failureCP = true;
        }
        if (inspectPassword($password1, $password2) == false) {
            $failurePWD = true;
        }
        $usernames = getAllUsernames();
        if (in_array($username, $usernames)) {
            $failureUN = true;
        }
        if (checkMailAddress($email) == false) {
            $failureEM = true;
        }
        if (($failureUN === false) && ($failurePWD === false) && ($failureEM == false || config::$DEBUG) && ($failureCP == false) && ($failureCSRF == false)) {
            $pw_hash = password_hash($password1, config::$PWD_ALGORITHM);
            $result = addRemoteUser($username, $email, $pw_hash, $firstname, $lastname);
            $success = $result['test'];
        }
    }
    ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Kino Karte - Registrieren</title>
    <?php
    generateHeaderTags(
        array(
            array(
                "type" => "script",
                "typeval" => "text/javascript",
                "href" => "tjs/registration.js",
                "hrefmin" => "tjs/registration.min.js"
            ),
            array(
                "type" => "script",
                "typeval" => "text/javascript",
                "href" => "js/loadCaptcha.js",
                "hrefmin" => "js/loadCaptcha.min.js"
            )
        )
    );
    ?>
</head>

<body>
<?php
generateHeader(false);
?>
<div class="container mt-4 mr-auto ml-auto text-light pt-5">
    <?php
    if ($success === false) {
        ?>
        <div class="offset-4">
            <h1>Registrieren</h1>
            <form action="registration.php" method="post" name="wishes" class="col-6">
                <input type="text" value="<?php echo createCSRFtokenClient()?>" id="FormTokenScriptCSRF" name="FormTokenScriptCSRF" hidden>
                <label class="weiß2">Nutzername*</label>
                <input class="form-control textinput <?php
                if ($failureUN) {
                    echo 'border-danger bg-danger" data-toggle="tooltip" title="Dieser Benutzername ist bereits vergeben."';
                }
                ?>" type="text" name="username" id="username_reg"<?php
                if ($failurePWD || $failureEM || $failureCP) {
                    echo 'value="' . $username . '" ';
                }
                ?> required><br>

                <label class="weiß2">Vorname</label>
                <input class="form-control textinput" type="text" name="firstname"
                    <?php
                    if ($failurePWD || $failureEM || $failureUN || $failureCP) {
                        echo 'value="' . $firstname . '" ';
                    }
                    ?>
                ><br>

                <label class="weiß2">Nachname</label>
                <input class="form-control textinput" type="text" name="lastname"
                    <?php
                    if ($failurePWD || $failureEM || $failureUN || $failureCP) {
                        echo 'value="' . $lastname . '" ';
                    }
                    ?>
                ><br>

                <label class="weiß2">E-Mail-Adresse*</label>
                <input class="form-control textinput <?php
                if ($failureEM) {
                    echo "border-danger bg-danger";
                }
                if ($failurePWD || $failureUN  || $failureCP) {
                    echo 'border-danger bg-danger" value="' . $email . '" ' . 'data-toggle="tooltip" title="Bitte eine gültige E-Mail-Adresse verwenden.';
                }
                ?>" type="text" name="email" required id="emailAddUser"><br>

                <label class="weiß2">Passwort* (mindestends <?php echo config::$PWD_LENGTH ?> Zeichen)</label>
                <input class="form-control textinput <?php
                if ($failurePWD) {
                    echo "border-danger bg-danger" . '" ' . 'data-toggle="tooltip" title="Bitte ein Passwort mit mindestens ' . config::$PWD_LENGTH . " Zeichen verwenden und in beide Felder identische Passwörter eingeben.";
                }
                ?>" type="password" name="password1" required><br>

                <label class="weiß2">Passwort wiederholen*</label>
                <input class="form-control textinput <?php
                if ($failurePWD) {
                    echo "border-danger bg-danger" . '" ' . 'data-toggle="tooltip" title="Bitte ein Passwort mit mindestens ' . config::$PWD_LENGTH . " Zeichen verwenden und in beide Felder identische Passwörter eingeben.";
                }
                ?>" type="password" name="password2" required><br>
                <div id="captchaBox">
                    <label class="captchaReturn">Bitte Captcha eingeben</label>
                    <div class="container pl-0">
                        <div class="row">
                            <div class="col pr-0">
                                <img class="captcha captcha2" id="Captcha"/>
                            </div>
                            <div class="col pl-0">
                                <a class="btn btn-success btn-block text-dark ml-2"
                                        onclick="loadCaptchaContact()">Neues Captcha
                                </a>
                            </div>
                        </div>
                    </div>
                    <br/>
                    <input type="text" name="captchaReturn" id="captchaReturn"
                           class="form-control textinput <?php
                           if ($failureCP) {
                               echo "border-danger bg-danger" . '" ';
                           } ?> ">
                </div>
                <br/>
                <div>
                    <a class="btn btn-success btn-block text-dark" onclick="checkMailAdress();">Registrieren</a>
                    <button class="btn btn-success btn-block" name="submitrequest" type="submit"
                            value="Registrieren" hidden id="submitRegistration"></button>
                </div>
            </form>
        </div>
        <?php
    } else {
        ?>
        <div class="justify-content-center mx-auto">
            <h1>Registrieren</h1>
            <div>
                Die Registrierung war erfolgreich. Bitte bestätigen sie Ihre Mailadresse, indem sie den Link öffnen. Die
                entsprechende E-Mail ist unter Umständen im Spamordner gelandet. <br/>
                Desweiteren müssen Sie noch durch einen Mitarbeiter des Projektes für die Plattform freigeschaltet
                werden.
            </div>
        </div>
        <?php
    }
    ?>
</div>
</body>
</html>
