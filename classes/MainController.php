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
 * Dlcounter_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Dlcounter_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Dlcounter;

use Plib\View;

class MainController
{
    /**
     * @var string
     */
    private $basename;

    /**
     * @var DbService
     */
    private $dbService;

    /**
     * @var array
     */
    private $lang;

    /**
     * @param string $basename
     */
    public function __construct($basename)
    {
        global $plugin_tx;

        $this->basename = $basename;
        $this->dbService = new DbService;
        $this->lang = $plugin_tx['dlcounter'];
    }

    public function defaultAction()
    {
        global $pth, $sn, $su;

        $filename = (new DownloadService)->downloadFolder() . basename($this->basename);
        if (!is_readable($filename)) {
            echo XH_message('fail', sprintf($this->lang['message_cantread'], $filename));
            return;
        }

        $view = new View("{$pth["folder"]["plugins"]}dlcounter/views/", $this->lang);
        echo $view->render("download-form", [
            'actionUrl' => "$sn?$su",
            'basename' => $this->basename,
            'size' => $this->determineSize($filename),
            'times' => $this->dbService->getDownloadCountOf($this->basename)
        ]);
    }

    /**
     * @param string $filename
     * @return string
     */
    private function determineSize($filename)
    {
        $filesize = filesize($filename);
        $units = array('B', 'KB', 'MB', 'GB');
        $log = (int) log($filesize, 1024);
        return round($filesize / pow(1024, $log), 1) . ' ' . $units[$log];
    }

    public function downloadAction()
    {
        $downloadService = new DownloadService;
        $filename = $downloadService->downloadFolder() . basename($this->basename);
        if (is_readable($filename)) {
            if (!XH_ADM) {
                if (!$this->dbService->log(time(), $filename)) {
                    echo XH_message('fail', $this->lang['message_cantwrite'], $filename);
                    return;
                }
            }
            $downloadService->deliverDownload($filename);
        } else {
            shead('404');
        }
    }
}
