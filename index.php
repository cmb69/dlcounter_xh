<?php

/**
 * Front-end of Dlcounter_XH.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Dlcounter
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2016 Christoph M. Becker <http://3-magi.net>
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

/**
 * The plugin version.
 */
define('DLCOUNTER_VERSION', '@DLCOUNTER_VERSION@');

/**
 * The plugin object.
 *
 * @var Dlcounter
 */
$_Dlcounter = new Dlcounter\Controller(new Dlcounter\Domain());

/**
 * Returns the download form view.
 *
 * @param string $filename A filename.
 *
 * @return string (X)HTML.
 *
 * @global Dlcounter The plugin object.
 */
function dlcounter($filename)
{
    global $_Dlcounter;

    return $_Dlcounter->renderDownloadForm($filename);
}

/**
 * Includes the jquery4cmsimple include file.
 *
 * @return void
 *
 * @global array The paths of system files and folders.
 */
function Dlcounter_includeJQuery()
{
    global $pth;

    include_once $pth['folder']['plugins'] . 'jquery/jquery.inc.php';
}

if (!function_exists('XH_exit')) {
    /**
     * Exits the script.
     *
     * We can't call exit directly, because that breaks unit tests,
     * so we use this workaround.
     *
     * @return void
     */
    function XH_exit()
    {
        exit;
    }
}

/*
 * Handle the download request.
 */
if (isset($_POST['dlcounter']) && $_POST['dlcounter'] != '') {
    $_Dlcounter->download(stsl($_POST['dlcounter']));
}

?>
