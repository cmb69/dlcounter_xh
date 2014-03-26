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
 * The plugin version.
 */
define('DLCOUNTER_VERSION', '1alpha1');

/**
 * Returns the data folder's path.
 *
 * @return string
 *
 * @global array The path of system files and folders.
 * @global array The configuration of the plugins.
 */
function Dlcounter_dataFolder()
{
    global $pth, $plugin_cf;

    $pcf = $plugin_cf['dlcounter'];
    if (empty($pcf['folder_data'])) {
        $fn = $pth['folder']['plugins'] . 'dlcounter/data/';
    } else {
        $fn = $pth['folder']['base'] . $pcf['folder_data'];
        if ($fn{strlen($fn) - 1} != '/') {
            $fn .= '/';
        }
    }
    if (file_exists($fn)) {
        if (!is_dir($fn)) {
            e('cntopen', 'folder', $fn);
        }
    } else {
        if (!mkdir($fn, 0777)) {
            e('cntsave', 'folder', $fn);
        }
    }
    return $fn;
}

/**
 * Returns whether a log entry for the download was appended.
 *
 * @param string $file A filename.
 *
 * @return bool
 *
 * @global bool Whether we're in admin mode.
 */
function Dlcounter_log($file)
{
    global $adm;

    $rec = $adm ? '' : time() . "\t" . basename($file) . "\n";
    $fn = Dlcounter_dataFolder() . 'downloads.dat';
    if (($fh = fopen($fn, 'a')) !== false && fwrite($fh, $rec) !== false) {
        $ok = true;
    } else {
        $ok = false;
        e('cntwriteto', 'file', $fn);
    }
    if ($fh !== false) {
        fclose($fh);
    }
    return $ok;
}

/**
 * Delivers the download.
 *
 * @param string $fn A filename.
 *
 * @return void
 *
 * @global array The paths of system files and folders.
 * @global array The configuration of the plugins.
 */
function Dlcounter_download($fn)
{
    global $pth, $plugin_cf;

    $pcf = $plugin_cf['dlcounter'];

    $fn = $pth['folder']['base'] . $pcf['folder_downloads'] . basename($fn);
    if (is_readable($fn)) {
        if (Dlcounter_log($fn)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($fn));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($fn));
            if (ob_get_level()) {
                ob_end_clean();
            }
            readfile($fn);
            exit;
        }
    } else {
        shead('404');
    }
}

/**
 * Returns the download form view.
 *
 * @param string $fn A filename.
 *
 * @return string (X)HTML.
 *
 * @global string The URL of the current page.
 * @global array  The paths of system files and folders.
 * @global array  The configuration of the plugins.
 * @global array  The localization of the plugins.
 */
function dlcounter($fn)
{
    global $su, $pth, $plugin_cf, $plugin_tx;

    $units = array('B', 'KB', 'MB', 'GB');
    $pcf = $plugin_cf['dlcounter'];

    $ffn = $pth['folder']['base'] . $pcf['folder_downloads'] . $fn;
    if (!is_readable($ffn)) {
        e('notreadable', 'file', $ffn);
        return false;
    }
    $size = filesize($ffn);
    $log = intval(log($size, 1024));
    $size = round($size / pow(1024, $log), 1) . ' ' . $units[$log];
    return '<form class="dlcounter" action="?' . $su . '" method="GET">'
        . tag('input type="hidden" name="dlcounter" value="' . $fn . '"')
        . tag(
            'input type="image" src="' . $pth['folder']['plugins']
            . 'dlcounter/images/download-button.png"'
            . ' alt="' . $plugin_tx['dlcounter']['label_download'] . '"'
            . ' title="' . $fn . ' &ndash; ' . $size . '"'
        )
        . '</form>';
}

/*
 * Handle the download request.
 */
if (isset($_GET['dlcounter']) && $_GET['dlcounter'] !== '') {
    Dlcounter_download(stsl($_GET['dlcounter']));
}

?>
