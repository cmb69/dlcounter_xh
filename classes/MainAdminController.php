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

class MainAdminController
{
    /** @var string */
    private $pluginFolder;

    /** @var DbService */
    private $dbService;

    /** @var View */
    private $view;

    public function __construct(string $pluginFolder, DbService $dbService, View $view)
    {
        $this->pluginFolder = $pluginFolder;
        $this->dbService = $dbService;
        $this->view = $view;
    }

    /** @return void */
    public function defaultAction()
    {
        $this->emitScripts();
        $data = $this->dbService->readDb();
        $totals = array_count_values(
            array_map(function ($elt) {
                return $elt->name;
            }, $data)
        );
        echo $this->view->render("stats", [
            'totals' => $totals,
            'details' => $data
        ]);
    }

    /** @return void */
    private function emitScripts()
    {
        global $bjs;

        include_once "{$this->pluginFolder}jquery/jquery.inc.php";
        include_jQuery();
        include_jQueryPlugin(
            'tablesorter',
            "{$this->pluginFolder}dlcounter/lib/jquery.tablesorter.js"
        );
        $filename = "{$this->pluginFolder}dlcounter/admin.min.js";
        if (!file_exists($filename)) {
            $filename = "{$this->pluginFolder}dlcounter/admin.js";
        }
        $bjs .= "<script src=\"$filename\"></script>";
    }
}
