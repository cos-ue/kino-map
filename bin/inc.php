<?php
/**
 * Include all needed files to display all pages.
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * includes all needed files; used in files above this directory level
 */

header('Content-Type: text/html; charset=utf-8');
require_once 'bin/config.php';
require_once 'bin/settings.php';
require_once 'bin/database/inc-db.php';
require_once 'bin/deletions.php';
require_once 'bin/functionLib.php';
require_once 'bin/authSystem.php';
require_once 'bin/session.php';
require_once 'bin/api-functions.php';
require_once 'bin/rapi-functions.php';
require_once 'bin/statistic-calc.php';
require_once 'bin/browser-recognition.php';
require_once 'bin/csrf.php';

/**
 * sets parameters to debug if debug mode is enabled
 */
if (config::$DEBUG === true) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
}

/**
 * defines language file static and loads it, no multi-language needed yet
 */
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = "de";
}
require_once "bin/" . $_SESSION['lang'] . ".php";