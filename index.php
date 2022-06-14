<?php
/**
 * Page to start visit of webpage
 *
 * @package default
 */

/**
 * @const enables loading of other files without dying to improve security
 */
define('NICE_PROJECT', true);
require_once 'bin/inc.php';
$verhalten = 0;
if (config::$BETA) {
    $verhalten = 1;
}
if (isset($_GET['b'])) {
    $v = filter_input(INPUT_GET, 'b', FILTER_SANITIZE_NUMBER_INT);
    if ($v == 0 && config::$BETA) {
        $verhalten = 0;
    }
}
if (config::$MAINTENANCE) {
    $verhalten = 3;
}
if (isset($_GET['m'])) {
    $v = filter_input(INPUT_GET, 'm', FILTER_SANITIZE_NUMBER_INT);
    if ($v == 0 && config::$MAINTENANCE) {
        $verhalten = 0;
    }
}
if (isset($_GET['f'])) {
    $f = filter_input(INPUT_GET, 'f', FILTER_SANITIZE_NUMBER_INT);
    if ($f >= 0) {
        $verhalten = 6;
    }
}
$side = "";
$redirect = false;
if (isset($_GET['side'])) {
    $side = filter_input(INPUT_GET, "side");
}
if (isset($_GET['side'])) {
    $redirect = filter_input(INPUT_GET, "redirect") == 1;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_SESSION["username"])) {
        if ($redirect && $side != "") {
            switch ($side) {
                case 'map':
                    Redirect('map.php');
                    break;
                default:
                    Redirect('hub.php');
                    break;
            }
        } else {
            Redirect('hub.php');
        }
    } else {
        if (sizeof($_POST) > 0) {
            $user = filter_input(INPUT_POST, 'user',FILTER_SANITIZE_STRING);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
            $csrf = filter_input(INPUT_POST, "FormTokenScriptCSRF", FILTER_SANITIZE_STRING);
            if (!checkCSRFtoken($csrf)) {
                Redirect("index.php", false);
            }
            if (key_exists("registrieren", $_POST)) {
                $register = filter_input(INPUT_POST, 'registrieren', FILTER_SANITIZE_STRING);
                if ($register === "Registrieren") {
                    Redirect("registration.php");
                    $verhalten = 0;
                }
            } else if (isset($password) && isset($user) && $password !== "" && $user !== "") {
                if (getAuth($user, $password)) {
                    Redirect('hub.php');
                } else {
                    $verhalten = 2;
                }
            }
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_SESSION["username"])) {
        Redirect('hub.php');
    } else {
        if (sizeof($_GET) > 0) {
            $type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);
            $login = filter_input(INPUT_GET, 'login', FILTER_SANITIZE_STRING);
            if ($type == "guest" && $login) {
                getGuestAuth();
                Redirect('map.php');
            } else if ($type == "rup") {
                $username = filter_input(INPUT_GET, 'username', FILTER_SANITIZE_STRING);
                resetUserPassword($username);
                RedirectMainBetaIndex();
                Redirect('index.php');
            } else if ($redirect && $side != "") {
                getGuestAuth();
                dump('test', 3);
                switch ($side) {
                    case "map":
                        Redirect('map.php');
                        break;
                    default:
                        Redirect('hub.php');
                        break;
                }

            }
        }
    }
}
if (config::$DEBUG) {
    if (isset($_GET['v'])) {
        $v = filter_input(INPUT_GET, 'v', FILTER_SANITIZE_NUMBER_INT);
        $verhalten = $v;
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Anmelden</title>
    <?php
    generateHeaderTags(
        array(
            array(
                "type" => "script",
                "typeval" => "text/javascript",
                "href" => "js/loadCookie.js",
                "hrefmin" => "js/loadCookie.min.js"
            )
        )
    );
    ?>
</head>
<body>
<?php
generateHeader(false, $lang, false, true);
if ($verhalten == 0) {
    ?>
    <div class="container d-flex justify-content-center flex-column">
        <div class="d-flex align-items-center flex-column">
            <img class="mb-5" src="<?php echo config::$LOGO?>" style="height: 90px">
            <div class="card col-5 card-login">
                <div class="card-body">
                    <span class="mb-2" style="font-size: 2rem; color: white; float: left">Anmeldung</span>
                    <a href="index.php?<?php echo http_build_query(array('type' => "guest", "login" => true), '', '&amp;'); ?>"
                       class="btn login_btn col-5 float-right">Zur Karte</a>

                    <form method="post" action="index.php">
                        <input type="text" value="<?php echo createCSRFtokenClient()?>" id="FormTokenScriptCSRF" name="FormTokenScriptCSRF" hidden>
                        <div class="input-group form-group">
                            <div class="input-group-prepend">
                                        <span class="input-group-text" style="width: 45px">
                                            <img src="images/user.svg" height="25px">
                                        </span>
                            </div>
                            <input type="text" name="user" class="form-control textinput"
                                   placeholder="Nutzername">

                        </div>
                        <div class="input-group form-group">
                            <div class="input-group-prepend">
                                        <span class="input-group-text" style="width: 45px">
                                            <img src="images/key.svg" height="25px" style="margin-left: -2px">
                                        </span>
                            </div>
                            <input type="password" name="password" class="form-control textinput"
                                   placeholder="Kennwort">
                        </div>
                        <div class="form-group d-flex">
                            <a href="registration.php" class="btn login_btn flex-fill mr-2">Registrieren</a>
                            <input type="submit" name="Einloggen"
                                   class="btn login_btn btn-important flex-fill ml-2"
                                   value="Anmelden">
                        </div>
                        <div>
                            <a href="index.php?f=0" class="forgottenLink">Ich habe meine Anmeldedaten vergessen.</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
} else if ($verhalten == 1) {
    ?>
    <div class="container d-flex align-items-center">
        <div class="d-flex justify-content-center">
            <div class="card card-login col-5">
                <div class="card-body">
                    <h3 class="text-light mb-3" style="text-align: left" >Arbeit im Gange</h3>
                    <div class="d-flex justify-content-end text-light" style="text-align: left;">
                        Sehr geehrter Interessent, <br/>
                        Wir befinden uns momentan in einer geschlossen Testphase. Wir hoffen, Ihnen hier
                        demnächst eine fehlerfrei funktionierende Plattform bieten zu können.
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
} else if ($verhalten == 2) {
    ?>
    <div class="container d-flex justify-content-center align-items-center">
        <div class="card card-login col-5">
            <div class="card-body d-flex flex-column align-items-center">
                <h3 style="color: white; text-align: center">Anmeldung fehlgeschlagen</h3>
                <a class="btn btn-warning col-6 mt-2" href="index.php<?php if (config::$MAINTENANCE) {
                    echo "?m=0";
                } else if (config::$BETA) {
                    echo "?b=0";
                } ?>">Erneut versuchen</a>
                <?php
                if (in_array($user, getAllUsernames())) {
                    ?>
                    <a class="btn btn-warning btn-important col-6 mt-2"
                       href="index.php?<?php echo http_build_query(array('type' => "rup", "username" => $user, "login" => false), '', '&amp;'); ?>">Passwort
                        zurücksetzen</a>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
    <?php
} else if ($verhalten == 3) {
    ?>
    <div class="container d-flex align-items-center">
        <div class="d-flex justify-content-center">
            <div class="card card-login col-5">
                <div class="card-body">
                    <h3 class="text-light mb-3" style="text-align: left">Wartungsarbeiten</h3>
                    <div class="d-flex justify-content-start text-light" style="text-align: left;">
                        Sehr geehrter Nutzer, <br/>
                        Wir führen momentan Wartungsarbeiten durch, um die Plattform zu verbessern und sicherer
                        zu gestalten. Daher bitten wir Sie um etwas Geduld. Die Plattform wird in Kürze wieder
                        für Sie zur Verfügung stehen.
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
} else if ($verhalten == 6) {
    ?>
    <div class="container d-flex align-items-center justify-content-center">
        <div class="card card-login col-5">
            <div class="card-body d-flex align-items-center flex-column">
                <h3 style="color: white">Anmeldedaten vergessen</h3>
                <a href="forgottenLogin.php?n=1&e=0" class="btn btn-warning col mt-2">Ich kenne meinen Nutzernamen.</a>
                <a href="forgottenLogin.php?n=0&e=1" class="btn btn-warning col mt-2">Ich kenne meine E-Mailadresse.</a>
            </div>
        </div>
    </div>
    <?php
}
?>
</body>
</html>