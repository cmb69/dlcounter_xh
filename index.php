<?php

/**
 * Front-end of Dlcounter_XH.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Dlcounter
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2014 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
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
 * The plugin classes.
 */
require_once $pth['folder']['plugin_classes'] . 'Dlcounter.php';

/**
 * The plugin version.
 */
define('DLCOUNTER_VERSION', '1alpha1');

/**
 * The plugin object.
 *
 * @var Dlcounter
 */
$_Dlcounter = new Dlcounter();

/**
 * Returns the download form view.
 *
 * @param string $fn A filename.
 *
 * @return string (X)HTML.
 */
function dlcounter($fn)
{
    global $_Dlcounter;

    return $_Dlcounter->main($fn);
}

/*
 * Handle the download request.
 */
if (isset($_GET['dlcounter']) && $_GET['dlcounter'] !== '') {
    $_Dlcounter->download(stsl($_GET['dlcounter']));
}

?>
