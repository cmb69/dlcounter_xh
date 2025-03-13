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

use Plib\Jquery;
use Plib\SystemChecker;
use Plib\View;

class Dic
{
    public static function mainController(): MainController
    {
        return new MainController(
            self::dbService(),
            self::downloadService(),
            self::view()
        );
    }

    public static function infoController(): InfoController
    {
        global $pth;

        return new InfoController(
            "{$pth['folder']['plugins']}dlcounter/",
            new SystemChecker(),
            self::view()
        );
    }

    public static function mainAdminController(): MainAdminController
    {
        global $pth;

        return new MainAdminController(
            $pth["folder"]["plugins"],
            self::dbService(),
            self::jquery(),
            self::view()
        );
    }

    private static function dbService(): DbService
    {
        global $pth, $sl, $cf;

        if ($sl === $cf["language"]["default"]) {
            $dataFolder = $pth["folder"]["content"];
        } else {
            $dataFolder = dirname($pth["folder"]["content"]) . "/";
        }
        return new DbService($dataFolder);
    }

    private static function downloadService(): DownloadService
    {
        global $pth, $plugin_cf;

        $folder = $pth["folder"]["userfiles"] . $plugin_cf["dlcounter"]["folder_downloads"];
        if ($folder[strlen($folder) - 1] !== "/") {
            $folder .= "/";
        }
        return new DownloadService($folder);
    }

    private static function jquery(): Jquery
    {
        global $pth;

        return new Jquery("{$pth["folder"]["plugins"]}jquery/");
    }

    private static function view(): View
    {
        global $pth, $plugin_tx;

        return new View("{$pth["folder"]["plugins"]}dlcounter/views/", $plugin_tx["dlcounter"]);
    }
}
