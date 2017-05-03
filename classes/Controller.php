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
        global $sn, $su, $plugin_tx;

        $filename = $this->domain->downloadFolder() . basename($basename);
        if (!is_readable($filename)) {
            return XH_message('fail', sprintf($plugin_tx['dlcounter']['message_cantread'], $filename));
        }

        $view = new View('download-form');
        $view->actionUrl = "$sn?$su";
        $view->basename = $basename;
        $view->downloadImage = $this->domain->imageFolder() . 'download-button.png';
        $view->size = $this->renderSize(filesize($filename));
        return (string) $view;
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
                $o .= XH_message('fail', $ex->getMessage());
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
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $basename = urlencode(basename($filename));
        $mimeType = mime_content_type($filename);
        header("Content-Type: $mimeType");
        header("Content-Disposition: attachment; filename=file.$extension; filename*=UTF-8''$basename");
        header('Content-Length: ' . filesize($filename));
        while (ob_get_level()) {
            ob_end_clean();
        }
        readfile($filename);
        XH_exit();
    }

    /**
     * @return string
     */
    public function renderPluginInfo()
    {
        $view = new View('info');
        $view->logo = $this->domain->logoPath();
        $view->version = DLCOUNTER_VERSION;
        $view->checks = (new SystemCheckService)->getChecks();
        return (string) $view;
    }

    /**
     * @return string
     */
    public function renderStatistics()
    {
        $this->hjs();
        $data = $this->domain->readDb();
        $totals = array_count_values(
            array_map(function ($elt) {
                return $elt[1];
            }, $data)
        );
        $view = new View('stats');
        $view->totals = $totals;
        $view->details = $data;
        return (string) $view;
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
}
