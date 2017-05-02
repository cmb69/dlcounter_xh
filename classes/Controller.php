<?php

/**
 * Copyright 2012-2017 Christoph M. Becker
 *
 * This file is part of Dlcounter_XH.
 *
 * Dlcounter_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Fa_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Dlcounter_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Dlcounter;

class Controller
{
    /**
     * @var Domain
     */
    private $domain;

    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
    }

    /**
     * @param string $basename
     * @return string
     */
    public function renderDownloadForm($basename)
    {
        global $sn, $plugin_tx;

        $ptx = $plugin_tx['dlcounter'];
        $filename = $this->domain->downloadFolder() . basename($basename);
        if (!is_readable($filename)) {
            return $this->renderMessage('fail', sprintf($ptx['message_cantread'], $filename));
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
     * @param int $filesize
     * @return string
     */
    private function renderSize($filesize)
    {
        $units = array('B', 'KB', 'MB', 'GB');
        $log = (int) log($filesize, 1024);
        return round($filesize / pow(1024, $log), 1) . ' ' . $units[$log];
    }

    /**
     * @param string $basename
     * @return void
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
            } catch (Exception $ex) {
                $o .= $this->renderMessage('fail', $ex->getMessage());
            }
        } else {
            shead('404');
        }
    }

    /**
     * @param string $filename
     */
    private function deliverDownload($filename)
    {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($filename));
        header('Content-Length: ' . filesize($filename));
        readfile($filename);
        XH_exit();
    }

    /**
     * @return string
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
     * @return string
     */
    private function renderSynopsis()
    {
        global $plugin_tx;

        $ptx = $plugin_tx['dlcounter'];
        return '<h4>' . $ptx['synopsis_title'] . '</h4>'
            . "<pre>{{{PLUGIN:dlcounter('$ptx[synopsis_filename]');}}}</pre>";
    }

    /**
     * @return string
     */
    private function renderSystemCheck()
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
     * @param string $check
     * @param string $state
     * @return string
     */
    private function renderSystemCheckItem($check, $state)
    {
        $src = $this->domain->imageFolder() . $state . '.png';
        return '<li>'
            . tag("img src=\"$src\" alt=\"$state\"") . " $check"
            . '</li>';
    }

    /**
     * @return string
     */
    private function renderVersion()
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
            . '<p>Copyright &copy; 2012-2017'
            . ' <a href="http://3-magi.net">Christoph M. Becker</a></p>'
            . $this->renderLicense();
    }

    /**
     * @return string
     */
    private function renderLicense()
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
     * @return string
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
     * @param array $totals
     * @return string
     */
    private function renderSummaryTable($totals)
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
     * @return string
     */
    private function renderSummaryTableHead()
    {
        global $plugin_tx;

        $ptx = $plugin_tx['dlcounter'];
        return '<thead>'
            . "<tr><th>$ptx[label_file]</th><th>$ptx[label_count]</th></tr>"
            . '</thead>';
    }

    /**
     * @param string $filename
     * @param int $count
     * @return string
     */
    private function renderSummaryTableRow($filename, $count)
    {
        return '<tr><td>' . $filename . '</td><td>' . $count . '</td></tr>';
    }

    /**
     * @param array $data
     * @return string
     */
    private function renderDetailsTable($data)
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
     * @return string
     */
    private function renderDetailsTableHead()
    {
        global $plugin_tx;

        $ptx = $plugin_tx['dlcounter'];
        return '<thead>'
            . "<tr><th>$ptx[label_date]</th><th>$ptx[label_file]</th></tr>"
            . '</thead>';
    }

    /**
     * @param int $timestamp
     * @param string $filename
     * @return string
     */
    private function renderDetailsTableRow($timestamp, $filename)
    {
        return '<tr><td>' . date('Y-m-d H:i:s', $timestamp) . '</td>'
            . '<td>' . $filename . '</td></tr>';
    }

    private function hjs()
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
     * @param string $type
     * @param string $message
     * @return string
     */
    private function renderMessage($type, $message)
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
