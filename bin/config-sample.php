<?php
/**
 * sample of configuration file. Not used in productive environment.
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * Class sample how config.php should look like
 */
class configsample
{
    /**
     * @var string sets sql server
     */
    public static $SQL_SERVER = "localhost";

    /**
     * @var string sets sql user
     */
    public static $SQL_USER = "root";

    /**
     * @var string sets password for sql user
     */
    public static $SQL_PASSWORD = "";

    /**
     * @var string sets schema which should be used
     */
    public static $SQL_SCHEMA = "SchemaNameHere";

    /**
     * @var string sets prefix of tables which will be used
     */
    public static $SQL_PREFIX = "YourPrefix";

    /**
     * currently only pdo is available
     * @var string sets type of connector
     */
    public static $SQL_Connector = "pdo";

    /**
     * @var string sets path for temporary folder
     */
    public static $PICTURE_PATH = "Formular/Bilder/";

    /**
     * @var bool indicates if system is in debug mode
     */
    public static $DEBUG = false;

    /**
     * @var int sets level of debug; if above null there could be impacts on apis
     */
    public static $DEBUG_LEVEL = 0;

    /**
     * @var int sets minimal password length
     */
    public static $PWD_LENGTH = 4;

    /**
     * @var string selects used password algorithm
     */
    public static $PWD_ALGORITHM = PASSWORD_ARGON2ID;

    /**
     * @var int set length of random string for multiple functions
     */
    public static $RANDOM_STRING_LENGTH = 170;

    /**
     * @var string sets path to cosp api
     */
    public static $CSAPI = "https://test.test.de/api.php";

    /**
     * @var string sets auth-token for cosp
     */
    public static $CSTOKEN = "insertYourTokenHere";

    /**
     * @var string sets path to user's api from cosp for modules
     */
    public static $USAPI = "https://test.test.de/uapi.php";

    /**
     * @var bool defines if stories are enabled
     */
    public static $ENABLE_STORIES = false;

    /**
     * @var bool sets beta mode for instance
     */
    public static $BETA = false;

    /**
     * @var bool sets maintenance mode
     */
    public static $MAINTENANCE = false;

    /**
     * @var int defines value of role which a guest must have
     */
    public static $ROLE_GUEST = 0;

    /**
     * @var int defines value of role which a unauth user must have
     */
    public static $ROLE_UNAUTH_USER = 1;

    /**
     * @var int defines value of role which a auth user must have
     */
    public static $ROLE_AUTH_USER = 2;

    /**
     * @var int defines value of role which a employee must have
     */
    public static $ROLE_EMPLOYEE = 10;

    /**
     * @var int defines value of role which a admin must have
     */
    public static $ROLE_ADMIN = 20;

    /**
     * @var bool enables some special chats in captcha codes
     */
    public static $SPECIAL_CHARS_CAPTCHA = true;

    /**
     * @var bool enables Contact formular for all visitors
     */
    public static $PUBLIC_CONTACT = TRUE;

    /**
     * @var string admins mail address
     */
    public static $ZENTRAL_MAIL = "admin@test.de";

    /**
     * @var string name of content responsible for Impressum
     */
    public static $IMPRESSUM_NAME = "Max Muster";

    /**
     * @var string street and Housenumber of content responsible for Impressum
     */
    public static $IMPRESSUM_STREET = "Musterstraße 21";

    /**
     * @var string City and Postalcode of content responsible for Impressum
     */
    public static $IMPRESSUM_CITY = "00000 Musterstadt";

    /**
     * @var string Name of your Company
     */
    public static $PRIVACY_COMPANY_NAME = "Musterfirma/Musterunternehmer";

    /**
     * @var string Street and Housenumber of the company address
     */
    public static $PRIVACY_COMPANY_STREET = "Musterstraße 1";

    /**
     * @var string postalcode and city of the company adress
     */
    public static $PRIVACY_COMPANY_CITY = "12345 Musterstadt";

    /**
     * @var string phone contact of company
     */
    public static $PRIVACY_COMPANY_FON = "Telefonnummer";

    /**
     * @var string fax contact of company
     */
    public static $PRIVACY_COMPANY_FAX = "Faxnummer";

    /**
     * @var string contactmailadress of company
     */
    public static $PRIVACY_COMPANY_MAIL = "muster@mustermail.xy";

    /**
     * @var string name of privacy representative
     */
    public static $PRIVACY_REP_NAME = "Maxie Musterfrau";

    /**
     * @var string position of privacy representative
     */
    public static $PRIVACY_REP_POS = "Datenschutzbeauftragte";

    /**
     * @var string street and housenumber of privacy representative address
     */
    public static $PRIVACY_REP_STREET = "Musterstraße 1";

    /**
     * @var string postalcode and city of privacy representative address
     */
    public static $PRIVACY_REP_CITY = "12345 Musterstadt";

    /**
     * @var string phone contacr of privacy representative
     */
    public static $PRIVACY_REP_FON = "Telefonnummer";

    /**
     * @var string fax contacr of privacy representative
     */
    public static $PRIVACY_REP_FAX = "Faxnummer";

    /**
     * @var string mail contacr of privacy representative
     */
    public static $PRIVACY_REP_MAIL = "datenschutz@mustermail.xy";

    /**
     * @var bool determines if data is directly deleted (true) or only marked as deleted (false)
     */
    public static $DIRECT_DELETE = false;

    /**
     * @var int sets a multiplier for needed values for a certain rank, must at least be greater than 0
     */
    public static $RANK_MULTIPLIER = 10;

    /**
     * @var string defines where logo can be found relational to root directory
     */
    public static $LOGO = "images/logo_alt.svg";

    /**
     * @var string defines an hmac key
     */
    public static $HMAC_SECRET = "abcdefghijklmnopqrstuvwxyz";
}
