<?php
/**
 * Page to show what a user can do
 *
 * @package default
 */

/**
 * @const enables loading of other files without dying to improve security
 */
define('NICE_PROJECT', true);
require_once 'bin/inc.php';
if (isset($_SESSION["username"]) == false) {
    Redirect("index.php");
    permissionDenied();
}
$coseLink = substr(config::$CSAPI, 0, strrpos(config::$CSAPI, "/", -1));
$destination = array(
    array(
        "href" => "map.php",
        "title" => "Karte",
        "image" => "images/map.png",
        "needeRoleValue" => config::$ROLE_GUEST,
        "config" => true,
        "newpage" => false,
        "text" => "Auf der Karte werden Informationen zu ehemaligen Kino-Standorten gesammelt. Das Ziel ist, die Kinolandschaft zu rekonstruieren. Hierzu können Sie Spielstätten eintragen oder bearbeiten.",
    ),
    array(
        "href" => "ListMaterial.php",
        "title" => "Archiv",
        "image" => "images/pictures.png",
        "needeRoleValue" => config::$ROLE_GUEST,
        "config" => true,
        "newpage" => false,
        "text" => "Über das Archiv haben Sie die Möglichkeit, relevantes Bildmaterial hochzuladen und mit anderen Forschern zu teilen. Interessant sind zum Beispiel Foto-aufnahmen von Eintritts-karten, Programmheften oder Kinogebäuden.",
    ),
    array(
        "href" => "StoryUpload.php",
        "title" => "Erfahrungsberichte",
        "image" => "images/stories.png",
        "needeRoleValue" => config::$ROLE_GUEST,
        "config" => config::$ENABLE_STORIES,
        "newpage" => false,
        "text" => "In den Erfahrungs-berichten können Sie Ihre Erinnerungen an Kino-besuche, Kinos und Filme einstellen. Mittels der Erfahrungsberichte kann die Alltags-geschichte des Kinos erfahren werden.",
    ),
    array(
        "href" => "blog-link",
        "title" => "Blog",
        "image" => "images/blog.png",
        "needeRoleValue" => config::$ROLE_GUEST,
        "config" => true,
        "newpage" => true,
        "text" => "Auf unserem Blog können Sie mehr zum Hintergrund des Projektes erfahren und einige interessante Artikel zur Kinogeschichte lesen. Auch ist eine Vorstellung des Projektteams dort zu finden.",
    ),
    array(
        "href" => $coseLink,
        "title" => "COSP",
        "image" => "images/cose.png",
        "needeRoleValue" => config::$ROLE_AUTH_USER,
        "config" => true,
        "newpage" => false,
        "text" => "COSP ist das Akronym für Citizen Open Science Plattform. Dort erfolgt die zentrale Verwaltung der Nutzer und einiger Funktionen. Auf COSP können Sie Ihre persönlichen Daten einsehen.",
    ),
);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Hub</title>
    <?php
    generateHeaderTags(
        array(
            array(
                "type" => "script",
                "typeval" => "text/javascript",
                "href" => "js/hub.js",
                "hrefmin" => "js/hub.min.js"
            ),
        )
    );
    ?>
</head>
<body class="hub">
<?php
generateHeader(isset($_SESSION['username']), $lang, false);
?>
    <div class="flex-container hub-flex w-100">
        <div class="flex-container flex-item">
            <div class="flex-container ml-4 mr-4 mb-5 mt-5">
                <?php
                $count = count($destination);
                foreach ($destination as $dest) {
                    $count--;
                    if ($dest['needeRoleValue'] <= $_SESSION['role'] && $dest['config']) {
                        ?>
                        <div class="flex-item">
                            <a href="<?php echo $dest['href']; ?>" style="text-decoration: none" <?php echo $dest['newpage'] ? 'target="_blank"' : '' ?>>
                                <div class="card card-hub mr-4 ml-4">
                                    <img src="<?php echo $dest['image']; ?>" class="card-img-top-hub" style="width: 14rem; height: 14rem">
                                    <div class="card-body">
                                        <h5 class="card-title weiß2"><?php echo $dest['title']; ?></h5>
                                        <p class="card-text" style="color: white"><?php echo $dest['text']; ?></p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>