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

class MainAdminController
{
    /**
     * @var DbService
     */
    private $dbService;

    public function __construct()
    {
        $this->dbService = new DbService;
    }

    public function defaultAction()
    {
        $this->emitScripts();
        $data = $this->dbService->readDb();
        $totals = array_count_values(
            array_map(function ($elt) {
                return $elt[1];
            }, $data)
        );
        $view = new View('stats');
        $view->totals = $totals;
        $view->details = $data;
        $view->render();
    }

    private function emitScripts()
    {
        global $pth, $bjs;

        $pluginFolder = $pth['folder']['plugins'];
        include_once "{$pluginFolder}jquery/jquery.inc.php";
        include_jQuery();
        include_jQueryPlugin(
            'tablesorter',
            "{$pluginFolder}dlcounter/lib/jquery.tablesorter.js"
        );
        $bjs .= "<script src=\"{$pluginFolder}dlcounter/admin.min.js\"></script>";
    }
}
