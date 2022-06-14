<?php
/**
 * Includes all required files for all database functions in correct order for top pages
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}
/**
 * includes all needed files for database access
 */
require_once 'bin/database/basic-db.php';
require_once 'bin/database/statistics-basic-dbfunctions.php';
require_once 'bin/database/user.php';
require_once 'bin/database/poi-db.php';
require_once 'bin/database/poi-val.php';
require_once 'bin/database/poi-comment.php';
require_once 'bin/database/validate-hist-adr.php';
require_once 'bin/database/validate-curr-adr.php';
require_once 'bin/database/validate-hist.php';
require_once 'bin/database/validate-type.php';
require_once 'bin/database/validate-name.php';
require_once 'bin/database/validate-operator.php';
require_once 'bin/database/validate-timespan.php';
require_once 'bin/database/validate-poi-story.php';
require_once 'bin/database/validate-seats.php';
require_once 'bin/database/validate-cinemas.php';
require_once 'bin/database/operators-db.php';
require_once 'bin/database/hist-adr-db.php';
require_once 'bin/database/names-db.php';
require_once 'bin/database/seats-db.php';
require_once 'bin/database/cinemas-db.php';
require_once 'bin/database/poi-story-db.php';
require_once 'bin/database/logging.php';
require_once 'bin/database/poi-picture-db.php';
require_once 'bin/database/validate-poi-picture.php';
require_once 'bin/database/cinema-type-db.php';
require_once 'bin/database/statistics-poi-db.php';
require_once 'bin/database/statistics-comments-db.php';
require_once 'bin/database/announcement-db.php';
require_once 'bin/database/source-type-db.php';
require_once 'bin/database/source-relation-db.php';
require_once 'bin/database/poi-source-db.php';
require_once 'bin/database/statistics_poi_validated.php';
require_once 'bin/database/session-db.php';