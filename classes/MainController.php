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

use Plib\Request;
use Plib\View;

class MainController
{
    /**
     * @var DbService
     */
    private $dbService;

    /** @var DownloadService */
    private $downloadService;

    /** @var View */
    private $view;

    /** @var array<string,string> */
    private $lang;

    public function __construct(DbService $dbService, DownloadService $downloadService, View $view)
    {
        global $plugin_tx;

        $this->dbService = $dbService;
        $this->downloadService = $downloadService;
        $this->view = $view;
        $this->lang = $plugin_tx['dlcounter'];
    }

    public function defaultAction(Request $request, string $basename): string
    {
        $filename = $this->downloadService->downloadFolder() . basename($basename);
        if (!$this->dbService->isReadable($filename)) {
            return XH_message('fail', sprintf($this->lang['message_cantread'], $filename));
        }

        return $this->view->render("download-form", [
            'actionUrl' => $request->url()->relative(),
            'basename' => $basename,
            'size' => $this->determineSize($filename),
            'times' => $this->dbService->getDownloadCountOf($basename)
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
        $log = (int) log((float) $filesize, 1024);
        return round($filesize / pow(1024, $log), 1) . ' ' . $units[$log];
    }

    public function downloadAction(Request $request, string $basename): string
    {
        $filename = $this->downloadService->downloadFolder() . basename($basename);
        if ($this->dbService->isReadable($filename)) {
            if (!$request->admin()) {
                if (!$this->dbService->log(time(), $filename)) {
                    return XH_message('fail', $this->lang['message_cantwrite'], $filename);
                }
            }
            $this->downloadService->deliverDownload($filename);
        } else {
            shead(404);
        }
        return "";
    }
}
