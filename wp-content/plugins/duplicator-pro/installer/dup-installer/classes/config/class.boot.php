<?php
/**
 * Boot class
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package SC\DUPX\Constants
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

class DUPX_Boot
{

    /**
     * inizialize all
     */
    public static function init()
    {
        self::phpIni();
        self::includes();
    }

    /**
     * init ini_set and default constants
     *
     * @throws Exception
     */
    public static function phpIni()
    {
        if (!isset($GLOBALS['DUPX_INIT'])) {
            throw new Exception('GLOBALS DUPX_INIT not defined.');
        }

        /** Absolute path to the Installer directory. - necessary for php protection */
        if (!defined('DUPLICATOR_PRO_INSTALLER_KB_IN_BYTES')) {
            define('DUPLICATOR_PRO_INSTALLER_KB_IN_BYTES', 1024);
        }
        if (!defined('DUPLICATOR_PRO_INSTALLER_MB_IN_BYTES')) {
            define('DUPLICATOR_PRO_INSTALLER_MB_IN_BYTES', 1024 * DUPLICATOR_PRO_INSTALLER_KB_IN_BYTES);
        }
        if (!defined('DUPLICATOR_PRO_GB_IN_BYTES')) {
            define('DUPLICATOR_PRO_GB_IN_BYTES', 1024 * DUPLICATOR_PRO_INSTALLER_MB_IN_BYTES);
        }
        if (!defined('DUPLICATOR_PRO_PHP_MAX_MEMORY')) {
            define('DUPLICATOR_PRO_PHP_MAX_MEMORY', 4096 * DUPLICATOR_PRO_INSTALLER_MB_IN_BYTES);
        }

        date_default_timezone_set('UTC'); // Some machines don’t have this set so just do it here.
        @ignore_user_abort(true);

        require_once($GLOBALS['DUPX_INIT'].'/lib/snaplib/snaplib.all.php');

        @set_time_limit(3600);

        $defaultCharset = ini_get("default_charset");
        if (empty($defaultCharset) && DupProSnapLibUtil::wp_is_ini_value_changeable('default_charset')) {
            @ini_set("default_charset", 'utf-8');
        }
        if (DupProSnapLibUtil::wp_is_ini_value_changeable('memory_limit')) {
            @ini_set('memory_limit', DUPLICATOR_PRO_PHP_MAX_MEMORY);
        }
        if (DupProSnapLibUtil::wp_is_ini_value_changeable('max_input_time')) {
            @ini_set('max_input_time', '-1');
        }
        if (DupProSnapLibUtil::wp_is_ini_value_changeable('pcre.backtrack_limit')) {
            @ini_set('pcre.backtrack_limit', PHP_INT_MAX);
        }
    }

    /**
     * include default utils files and constants
     *
     * @throws Exception
     */
    public static function includes()
    {
        if (!isset($GLOBALS['DUPX_INIT'])) {
            throw new Exception('GLOBALS DUPX_INIT not defined.');
        }

        $GLOBALS['DUPX_ENFORCE_PHP_INI'] = false;
        $GLOBALS['DUPX_DEBUG']           = (isset($_GET['debug']) && $_GET['debug'] == 1) ? true : false;

        require_once($GLOBALS['DUPX_INIT'].'/classes/utilities/class.u.exceptions.php');
        require_once($GLOBALS['DUPX_INIT'].'/classes/utilities/class.u.php');
        require_once($GLOBALS['DUPX_INIT'].'/classes/utilities/class.u.notices.manager.php');
        require_once($GLOBALS['DUPX_INIT'].'/classes/utilities/class.u.html.php');
        require_once($GLOBALS['DUPX_INIT'].'/classes/config/class.constants.php');
        require_once($GLOBALS['DUPX_INIT'].'/ctrls/ctrl.base.php');
    }

    public static function initArchiveAndLog()
    {
        require_once($GLOBALS['DUPX_INIT'].'/classes/config/class.archive.config.php');
        $GLOBALS['DUPX_AC'] = DUPX_ArchiveConfig::getInstance();
        require_once($GLOBALS['DUPX_INIT'].'/classes/class.logging.php');
    }
}