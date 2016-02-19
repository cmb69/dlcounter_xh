<?php

/**
 * The controllers.
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

/**
 * The controllers.
 *
 * @category CMSimple_XH
 * @package  Dlcounter
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Dlcounter_XH
 */
class Dlcounter_Controller
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
     * @global string The script name.
     * @global array  The localization of the plugins.
     */
    public function renderDownloadForm($basename)
    {
        global $sn, $plugin_tx;

        $ptx = $plugin_tx['dlcounter'];
        $filename = $this->domain->downloadFolder() . basename($basename);
        if (!is_readable($filename)) {
            return $this->renderMessage(
                'fail', sprintf($ptx['message_cantread'], $filename)
            );
        }
        $size = $this->renderSize(filesize($filename));
        return '<form class="dlcounter" action="' . $sn . '" method="post">'
            . tag('input type="hidden" name="dlcounter" value="' . $basename . '"')
            . '<button>'
            . tag(
                'img src="' . $this->domain->imageFolder() . 'download-button.png"'
                . ' alt="' . $ptx['label_download'] . '"'
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
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($filename));
        header('Content-Length: ' . filesize($filename));
        readfile($filename);
        XH_exit();
    }

    /**
     * Renders the plugin info.
     *
     * @return string (X)HTML.
     *
     * @global array The localization of the plugins.
     */
    public function renderPluginInfo()
    {
        global $plugin_tx;

        $ptx = $plugin_tx['dlcounter'];
        return '<h1>Dlcounter &ndash; ' . $ptx['info_title'] . '</h1>'
            . $this->renderSynopsis()
            . $this->renderSystemCheck()
            . $this->renderVersion();
    }

    /**
     * Renders the plugin synopsis.
     *
     * @return string (X)HTML.
     *
     * @global array The localization of the plugins.
     */
    protected function renderSynopsis()
    {
        global $plugin_tx;

        $ptx = $plugin_tx['dlcounter'];
        return '<h4>' . $ptx['synopsis_title'] . '</h4>'
            . "<pre>{{{PLUGIN:dlcounter('$ptx[synopsis_filename]');}}}</pre>";
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
            . '<ul class="dlcounter_system_check">';
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
     * Renders the plugin version.
     *
     * @return string (X)HTML.
     *
     * @global array The localization of the plugins.
     */
    protected function renderVersion()
    {
        global $plugin_tx;

        $ptx = $plugin_tx['dlcounter'];
        return '<h4>' . $ptx['info_about'] . '</h4>'
            . tag(
                'img class="dlcounter_plugin_icon" src="'
                . $this->domain->logoPath() . '" width="128"'
                . ' height="128" alt="Plugin Icon"'
            )
            . '<p>Version: ' . DLCOUNTER_VERSION . '</p>'
            . '<p>Copyright &copy; 2012-2016'
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
        return '<h1>Dlcounter &ndash; ' . $ptx['menu_main'] . '</h1>'
            . '<div id="dlcounter_stats">'
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

?>
