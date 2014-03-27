<?php

/**
 * Dlcounter_XH classes.
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

/**
 * The general purpose class.
 *
 * @category CMSimple_XH
 * @package  Dlcounter
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Dlcounter_XH
 */
class Dlcounter
{
    /**
     * Returns the data folder's path.
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
        if (empty($pcf['folder_data'])) {
            $fn = $pth['folder']['plugins'] . 'dlcounter/data/';
        } else {
            $fn = $pth['folder']['base'] . $pcf['folder_data'];
            if ($fn{strlen($fn) - 1} != '/') {
                $fn .= '/';
            }
        }
        if (!file_exists($fn) && !mkdir($fn, 0777, true)) {
            e('cntsave', 'folder', $fn);
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
    protected function log($file)
    {
        global $adm;

        $rec = $adm ? '' : time() . "\t" . basename($file) . "\n";
        $fn = $this->dataFolder() . 'downloads.dat';
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
    public function download($fn)
    {
        global $pth, $plugin_cf;

        $pcf = $plugin_cf['dlcounter'];

        $fn = $pth['folder']['base'] . $pcf['folder_downloads'] . basename($fn);
        if (is_readable($fn)) {
            if ($this->log($fn)) {
                $this->deliverDownload($fn);
            }
        } else {
            shead('404');
        }
    }

    /**
     * Delivers a downloadable file.
     *
     * @param string $filename A filename.
     *
     * @return void
     */
    protected function deliverDownload($filename)
    {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($filename));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filename));
        readfile($filename);
        Dlcounter_exit();
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
    public function main($fn)
    {
        global $su, $pth, $plugin_cf, $plugin_tx;

        $pcf = $plugin_cf['dlcounter'];

        $ffn = $pth['folder']['base'] . $pcf['folder_downloads'] . $fn;
        if (!is_readable($ffn)) {
            e('notreadable', 'file', $ffn);
            return false;
        }
        $size = $this->renderSize(filesize($ffn));
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

    /**
     * Renders a filesize.
     *
     * @param int $filesize A file size.
     *
     * @return string
     */
    protected function renderSize($filesize)
    {
        $units = array('B', 'KB', 'MB', 'GB');
        $log = intval(log($filesize, 1024));
        return round($filesize / pow(1024, $log), 1) . ' ' . $units[$log];
    }

    /**
     * Returns the content of the downloads database.
     *
     * @return array
     *
     * @global array The paths of system files and folders.
     */
    protected function readDb()
    {
        global $pth;

        $data = array();
        $fn = $this->dataFolder() . 'downloads.dat';
        $lines = file($fn);
        if ($lines !== false) {
            foreach ($lines as $line) {
                $data[] = explode("\t", rtrim($line));
            }
        } else {
            e('cntopen', 'file', $fn);
        }
        return $data;
    }

    /**
     * Outputs the JS to initialize the tablesorter to <head>.
     *
     * @return void
     *
     * @global array  The paths of system files and folders.
     * @global string The (X)HTML to insert into the head element.
     */
    protected function hjs()
    {
        global $pth, $hjs;

        include_once $pth['folder']['plugins'] . 'jquery/jquery.inc.php';
        include_jQuery();
        include_jQueryPlugin(
            'tablesorter',
            $pth['folder']['plugins'] . 'dlcounter/lib/jquery.tablesorter.js'
        );
        $hjs .= <<<SCRIPT
<script type="text/javascript">
/* <![CDATA[ */
jQuery(function() {
    jQuery('table.tablesorter').tablesorter()
})
/* ]]> */
</script>

SCRIPT;
    }

    /**
     * Returns the plugin information view.
     *
     * @return string (X)HTML.
     *
     * @global array The paths of system files and folders.
     */
    public function version()
    {
        global $pth;

        return '<h1>Dlcounter_XH</h1>'
            . tag(
                'img class="dlcounter_plugin_icon" src="'
                . $pth['folder']['plugins'] . 'dlcounter/dlcounter.png" width="128"'
                . ' height="128" alt="Plugin Icon"'
            )
            . '<p>Version: ' . DLCOUNTER_VERSION . '</p>'
            . '<p>Copyright &copy; 2012-2014'
            . ' <a href="http://3-magi.net">Christoph M. Becker</a></p>'
            . $this->renderLicense();
    }

    /**
     * Renders the license text.
     *
     * @return string (X)HTML.
     */
    protected function renderLicense()
    {
        return <<<EOT
<p class="dlcounter_license">
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
</p>
<p class="dlcounter_license">
    This program is distributed in the hope that it will be useful,
    but <em>without any warranty</em>; without even the implied warranty of
    <em>merchantability</em> or <em>fitness for a particular purpose</em>.
    See the GNU General Public License for more details.
</p>
<p class="dlcounter_license">
    You should have received a copy of the GNU General Public License
    along with this program.  If not, see
    <a href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.
</p>
EOT;
    }

    /**
     * Renders the system check.
     *
     * @return string (X)HTML.
     *
     * @global array The localization of the plugins.
     */
    public function renderSystemCheck()
    {
        global $plugin_tx;

        $ptx = $plugin_tx['dlcounter'];
        $result = "<h4>$ptx[syscheck_title]</h4>"
            . '<ul class="pdeditor_system_check">';
        foreach ($this->systemChecks() as $check => $state) {
            $result .= $this->renderSystemCheckItem($check, $state);
        }
        $result .= '</ul>';
        return $result;
    }

    /**
     * Renders a single system check item.
     *
     * @param string $check A label.
     * @param string $state A state.
     *
     * @return string XHTML.
     *
     * @global array The paths of system files and folders.
     */
    protected function renderSystemCheckItem($check, $state)
    {
        global $pth;

        $imageFolder = $pth['folder']['plugins'] . 'dlcounter/images/';
        return '<li>'
            . tag("img src=\"$imageFolder$state.png\" alt=\"$state\"")
            . " $check"
            . '</li>';
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
    protected function systemChecks()
    {
        global $pth, $tx, $plugin_tx;

        $ptx = $plugin_tx['dlcounter'];
        $phpVersion = '4.2.0';
        $result = array();
        $result[sprintf($ptx['syscheck_phpversion'], $phpVersion)]
            = version_compare(PHP_VERSION, $phpVersion) >= 0
                ? 'ok' : 'fail';
        foreach (array('date') as $ext) {
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
     * Returns the statistics view.
     *
     * @return string (X)HTML.
     *
     * @global array The localization of the plugins.
     */
    public function adminMain()
    {
        global $plugin_tx;

        $ptx = $plugin_tx['dlcounter'];
        $this->hjs();
        $data = $this->readDb();
        $totals = array_count_values(
            array_map(create_function('$elt', 'return $elt[1];'), $data)
        );
        return '<div id="dlcounter_stats">'
            . '<div class="plugineditcaption">Dlcounter</div>'
            . $this->renderSummaryTable($totals)
            . $this->renderDetailsTable($data)
            . '</div>';
    }

    /**
     * Renders the summary table.
     *
     * @param array $totals A map from filenames to download counts.
     *
     * @return string (X)HTML.
     *
     * @global array The localiazation of the plugins.
     */
    protected function renderSummaryTable($totals)
    {
        global $plugin_tx;

        $ptx = $plugin_tx['dlcounter'];
        $o = '<h4 onclick="jQuery(this).next().toggle()">' . $ptx['label_totals']
            . '</h4>'
            . '<table id="dlcounter_summary_table" class="tablesorter">'
            . $this->renderSummaryTableHead()
            . '<tbody>';
        foreach ($totals as $file => $count) {
            $o .= $this->renderSummaryTableRow($file, $count);
        }
        $o .= '</tbody></table>';
        return $o;
    }

    /**
     * Renders the head of the summary table.
     *
     * @return string (X)HTML.
     *
     * @global array The localization of the plugins.
     */
    protected function renderSummaryTableHead()
    {
        global $plugin_tx;

        $ptx = $plugin_tx['dlcounter'];
        return '<thead>'
            . "<tr><th>$ptx[label_file]</th><th>$ptx[label_count]</th></tr>"
            . '</thead>';
    }

    /**
     * Renders a row of the summary table.
     *
     * @param string $filename A filename.
     * @param int    $count    A download count.
     *
     * @return string (X)HTML.
     */
    protected function renderSummaryTableRow($filename, $count)
    {
        return '<tr><td>' . $filename . '</td><td>' . $count . '</td></tr>';
    }

    /**
     * Renders the detail table.
     *
     * @param array $data A list of records (timestamp, filename).
     *
     * @return string (X)HTML.
     *
     * @global array The localization of the plugins.
     */
    protected function renderDetailsTable($data)
    {
        global $plugin_tx;

        $ptx = $plugin_tx['dlcounter'];
        $o = '<h4 onclick="jQuery(this).next().toggle()">'
            . $ptx['label_individual'] . '</h4>'
            . '<table id="dlcounter_details_table" class="tablesorter">'
            . $this->renderDetailsTableHead()
            . '<tbody>';
        foreach ($data as $rec) {
            $o .= $this->renderDetailsTableRow($rec[0], $rec[1]);
        }
        $o .= '</tbody></table>';
        return $o;
    }

    /**
     * Renders the head of the details table.
     *
     * @return string (X)HTML.
     *
     * @global array The localization of the plugins.
     */
    protected function renderDetailsTableHead()
    {
        global $plugin_tx;

        $ptx = $plugin_tx['dlcounter'];
        return '<thead>'
            . "<tr><th>$ptx[label_date]</th><th>$ptx[label_file]</th></tr>"
            . '</thead>';
    }

    /**
     * Renders a row of the details table.
     *
     * @param int    $timestamp A unix timestamp.
     * @param string $filename  A filename.
     *
     * @return string (X)HTML.
     */
    protected function renderDetailsTableRow($timestamp, $filename)
    {
        return '<tr><td>' . date('Y-m-d H:i:s', $timestamp) . '</td>'
            . '<td>' . $filename . '</td></tr>';
    }
}

?>
