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
use Plib\Response;
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

    public function __construct(DbService $dbService, DownloadService $downloadService, View $view)
    {
        $this->dbService = $dbService;
        $this->downloadService = $downloadService;
        $this->view = $view;
    }

    public function defaultAction(Request $request, string $basename): Response
    {
        $filename = $this->downloadService->downloadFolder() . basename($basename);
        if (!$this->dbService->isReadable($filename)) {
            return Response::create($this->view->message("fail", "message_cantread", $filename));
        }
        return Response::create($this->view->render("download-form", [
            'actionUrl' => $request->url()->relative(),
            'basename' => $basename,
            'size' => $this->determineSize($filename),
            'times' => $this->dbService->getDownloadCountOf($basename)
        ]));
    }

    /**
     * @param string $filename
     * @return string
     */
    private function determineSize($filename)
    {
        $filesize = $this->dbService->fileSize($filename);
        $units = array('B', 'KB', 'MB', 'GB');
        $log = (int) log((float) $filesize, 1024);
        return round($filesize / pow(1024, $log), 1) . ' ' . $units[$log];
    }

    public function downloadAction(Request $request, string $basename): Response
    {
        $filename = $this->downloadService->downloadFolder() . basename($basename);
        if ($this->dbService->isReadable($filename)) {
            if (!$request->admin()) {
                if (!$this->dbService->log($request->time(), $filename)) {
                    return Response::create($this->view->message("fail", "message_cantwrite", $filename));
                }
            }
            $this->downloadService->deliverDownload($filename);
            return Response::create();
        } else {
            return Response::error(404);
        }
    }
}
