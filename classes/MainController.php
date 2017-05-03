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

class MainController
{
    /**
     * @var string
     */
    private $basename;

    /**
     * @var Domain
     */
    private $model;

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
        $this->model = new Domain;
        $this->lang = $plugin_tx['dlcounter'];
    }

    public function defaultAction()
    {
        global $sn, $su;

        $filename = $this->model->downloadFolder() . basename($this->basename);
        if (!is_readable($filename)) {
            echo XH_message('fail', sprintf($this->lang['message_cantread'], $filename));
            return;
        }

        $view = new View('download-form');
        $view->actionUrl = "$sn?$su";
        $view->basename = $this->basename;
        $view->downloadImage = $this->model->imageFolder() . 'download-button.png';
        $view->size = $this->determineSize($filename);
        $view->times = $this->model->getDownloadCountOf($this->basename);
        $view->render();
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
        $filename = $this->model->downloadFolder() . basename($this->basename);
        if (is_readable($filename)) {
            try {
                if (!XH_ADM) {
                    $this->model->log(time(), $filename);
                }
                $this->deliverDownload($filename);
            } catch (Exception $ex) {
                echo XH_message('fail', $ex->getMessage());
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
}
