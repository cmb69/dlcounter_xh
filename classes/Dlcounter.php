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
     * The domain object.
     *
     * @var Dlcounter_Domain
     */
    protected $domain;

    /**
     * Initializes a new instance.
     *
     * @param Dlcounter_Domain $domain A domain model.
     */
    public function __construct(Dlcounter_Domain $domain)
    {
        $this->domain = $domain;
    }

    /**
     * Renders the download form.
     *
     * @param string $basename A filename.
     *
     * @return string (X)HTML.
     *
     * @global string The URL of the current page.
     * @global array  The localization of the plugins.
     */
    public function renderDownloadForm($basename)
    {
        global $su, $plugin_tx;

        $filename = $this->domain->downloadFolder() . $basename;
        if (!is_readable($filename)) {
            return $this->renderMessage(
                'fail', sprintf('Can\'t read file "%s"!', $filename)
            );
        }
        $size = $this->renderSize(filesize($filename));
        return '<form class="dlcounter" action="?' . $su . '" method="post">'
            . tag('input type="hidden" name="dlcounter" value="' . $basename . '"')
            . '<button>'
            . tag(
                'img src="' . $this->domain->imageFolder() . 'download-button.png"'
                . ' alt="' . $plugin_tx['dlcounter']['label_download'] . '"'
                . ' title="' . $basename . ' &ndash; ' . $size . '"'
            )
            . '</button>'
            . '</form>';
    }

    /**
     * Renders a filesize.
     *
     * @param int $filesize A filesize.
     *
     * @return string
     */
    protected function renderSize($filesize)
    {
        $units = array('B', 'KB', 'MB', 'GB');
        $log = (int) log($filesize, 1024);
        return round($filesize / pow(1024, $log), 1) . ' ' . $units[$log];
    }

    /**
     * Delivers the download.
     *
     * @param string $basename A basename.
     *
     * @return void
     *
     * @global string The (X)HTML for the contents area.
     */
    public function download($basename)
    {
        global $o;

        $filename = $this->domain->downloadFolder() . basename($basename);
        if (is_readable($filename)) {
            try {
                if (!XH_ADM) {
                    $this->domain->log(time(), $filename);
                }
                $this->deliverDownload($filename);
            } catch (Dlcounter_Exception $ex) {
                $o .= $this->renderMessage('fail', $ex->getMessage());
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
     * Renders the plugin info.
     *
     * @return string (X)HTML.
     */
    public function renderPluginInfo()
    {
        return $this->renderVersion() . tag('hr') . $this->renderSystemCheck();
    }

    /**
     * Renders the plugin version.
     *
     * @return string (X)HTML.
     */
    protected function renderVersion()
    {
        return '<h1>Dlcounter_XH</h1>'
            . tag(
                'img class="dlcounter_plugin_icon" src="'
                . $this->domain->logoPath() . '" width="128"'
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
    protected function renderSystemCheck()
    {
        global $plugin_tx;

        $ptx = $plugin_tx['dlcounter'];
        $result = "<h4>$ptx[syscheck_title]</h4>"
            . '<ul class="pdeditor_system_check">';
        foreach ($this->domain->systemChecks() as $check => $state) {
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
     */
    protected function renderSystemCheckItem($check, $state)
    {
        $src = $this->domain->imageFolder() . $state . '.png';
        return '<li>'
            . tag("img src=\"$src\" alt=\"$state\"") . " $check"
            . '</li>';
    }

    /**
     * Renders the statistics.
     *
     * @return string (X)HTML.
     *
     * @global array The localization of the plugins.
     */
    public function renderStatistics()
    {
        global $plugin_tx;

        $ptx = $plugin_tx['dlcounter'];
        $this->hjs();
        $data = $this->domain->readDb();
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

        Dlcounter_includeJQuery();
        include_jQuery();
        include_jQueryPlugin(
            'tablesorter',
            $pth['folder']['plugins'] . 'dlcounter/lib/jquery.tablesorter.js'
        );
        $hjs .= <<<SCRIPT
<script type="text/javascript">
/* <![CDATA[ */
jQuery(function() {
    jQuery('table.tablesorter').tablesorter();
})
/* ]]> */
</script>

SCRIPT;
    }

    /**
     * Renders a message.
     *
     * @param string $type    A type ('success', 'info', 'warning' or 'fail').
     * @param string $message A message.
     *
     * @return string (X)HTML.
     */
    protected function renderMessage($type, $message)
    {
        if (function_exists('XH_message')) {
            return XH_message($type, $message);
        } else {
            $class = in_array($type, array('warning', 'fail'))
                ? 'cmsimplecore_warning'
                : '';
            return '<p class="' . $class . '">' . $message . '</p>';
        }
    }
}

/**
 * The domain model.
 *
 * @category CMSimple_XH
 * @package  Dlcounter
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Dlcounter_XH
 */
class Dlcounter_Domain
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
     * @throws Dlcounter_WriteException
     */
    public function log($timestamp, $basename)
    {
        $line = $timestamp . "\t" . basename($basename) . "\n";
        $filename = $this->dataFolder() . 'downloads.dat';
        if (!is_dir(dirname($filename))
            || file_put_contents($filename, $line, FILE_APPEND) === false
        ) {
            throw new Dlcounter_WriteException(
                sprintf('Can\'t write to "%s"', $filename)
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

/**
 * The base class of all plugin exceptions.
 *
 * @category CMSimple_XH
 * @package  Dlcounter
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Dlcounter_XH
 */
class Dlcounter_Exception extends Exception
{
    // pass
}

/**
 * Exceptions where data sources can't be written.
 *
 * @category CMSimple_XH
 * @package  Dlcounter
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Dlcounter_XH
 */
class Dlcounter_WriteException extends Dlcounter_Exception
{
    // pass
}

?>
