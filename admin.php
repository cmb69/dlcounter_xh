<?php

/**
 * Back-end of Dlcounter_XH.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Dlcounter
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2017 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Dlcounter_XH
 */

/*
 * Prevent direct access.
 */
if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

/*
 * Handle the plugin administration.
 */
if (XH_wantsPluginAdministration('dlcounter')) {
    $o .= print_plugin_admin('on');

    switch ($admin) {
    case '':
        $o .= $_Dlcounter->renderPluginInfo();
        break;
    case 'plugin_main':
        $o .= $_Dlcounter->renderStatistics();
        break;
    default:
        $o .= plugin_admin_common($action, $admin, $plugin);
    }
}

?>
