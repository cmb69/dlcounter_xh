<?php

/**
 * The domain model.
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

namespace Dlcounter;

/**
 * The domain model.
 *
 * @category CMSimple_XH
 * @package  Dlcounter
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Dlcounter_XH
 */
class Domain
{
    /**
     * Returns the path of the download folder.
     *
     * @return string
     *
     * @global array The paths of system files and folders.
     * @global array The configuration of the plugins.
     */
    public function downloadFolder()
    {
        global $pth, $plugin_cf;

        return $pth['folder']['base']
            . $plugin_cf['dlcounter']['folder_downloads'];
    }

    /**
     * Returns the path of the image folder.
     *
     * @return string
     *
     * @global array The paths of system files and folders.
     */
    public function imageFolder()
    {
        global $pth;

        return $pth['folder']['plugins'] . 'dlcounter/images/';
    }

    /**
     * Returns the path of the plugin logo.
     *
     * @return string
     *
     * @global array The paths of system files and folders.
     */
    public function logoPath()
    {
        global $pth;

        return $pth['folder']['plugins'] . 'dlcounter/dlcounter.png';
    }

    /**
     * Returns the content of the downloads database.
     *
     * @return array An array of records.
     *
     * @global array The paths of system files and folders.
     */
    public function readDb()
    {
        global $pth;

        $result = array();
        $filename = $this->dataFolder() . 'downloads.dat';
        if (is_readable($filename)) {
            $lines = file($filename);
        } else {
            $lines = false;
        }
        if ($lines !== false) {
            foreach ($lines as $line) {
                $result[] = explode("\t", rtrim($line));
            }
        }
        return $result;
    }

    /**
     * Appends a log entry for the download.
     *
     * @param int    $timestamp A timestamp.
     * @param string $basename  A basename.
     *
     * @return void
     *
     * @throws WriteException
     *
     * @global array The localization of the plugins.
     */
    public function log($timestamp, $basename)
    {
        global $plugin_tx;

        $line = $timestamp . "\t" . basename($basename) . "\n";
        $filename = $this->dataFolder() . 'downloads.dat';
        if (!is_dir(dirname($filename))
            || file_put_contents($filename, $line, FILE_APPEND | LOCK_EX) === false
        ) {
            throw new WriteException(
                sprintf($plugin_tx['dlcounter']['message_cantwrite'], $filename)
            );
        }
    }

    /**
     * Returns the system checks as map<string, status>.
     *
     * @return array
     *
     * @global array The paths of system files and folders.
     * @global array The localization of the core.
     * @global array The localization of the plugins.
     */
    public function systemChecks()
    {
        global $pth, $tx, $plugin_tx;

        $ptx = $plugin_tx['dlcounter'];
        $phpVersion = '5.1.0';
        $result = array();
        $result[sprintf($ptx['syscheck_phpversion'], $phpVersion)]
            = version_compare(PHP_VERSION, $phpVersion) >= 0
                ? 'ok' : 'fail';
        foreach (array() as $ext) {
            $result[sprintf($ptx['syscheck_extension'], $ext)]
                = extension_loaded($ext) ? 'ok' : 'fail';
        }
        $result[$ptx['syscheck_encoding']]
            = strtoupper($tx['meta']['codepage']) == 'UTF-8' ? 'ok' : 'warn';
        $result[$ptx['syscheck_magic_quotes']]
            = !get_magic_quotes_runtime() ? 'ok' : 'warn';
        $result[$ptx['syscheck_jquery']]
            = file_exists($pth['folder']['plugins'] . 'jquery/jquery.inc.php')
                ? 'ok' : 'fail';
        foreach (array('config/', 'css/', 'languages/') as $folder) {
            $folders[] = $pth['folder']['plugins'] . 'dlcounter/' . $folder;
        }
        $folders[] = $this->dataFolder();
        foreach ($folders as $folder) {
            $result[sprintf($ptx['syscheck_writable'], $folder)]
                = is_writable($folder) ? 'ok' : 'warn';
        }
        return $result;
    }

    /**
     * Returns the path of the data folder.
     *
     * @return string
     *
     * @global array The path of system files and folders.
     * @global array The configuration of the plugins.
     */
    protected function dataFolder()
    {
        global $pth, $plugin_cf;

        $pcf = $plugin_cf['dlcounter'];
        if (trim($pcf['folder_data']) == '') {
            $result = $pth['folder']['plugins'] . 'dlcounter/data/';
        } else {
            $result = $pth['folder']['base'] . $pcf['folder_data'];
            if ($result[strlen($result) - 1] != '/') {
                $result .= '/';
            }
        }
        return $result;
    }
}

?>
