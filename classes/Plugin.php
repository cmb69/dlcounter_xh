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

class Plugin
{
    const VERSION = '1.0beta2';

    /** @return void */
    public function run()
    {
        if (XH_ADM) { // @phpstan-ignore-line
            XH_registerStandardPluginMenuItems(true);
            if (XH_wantsPluginAdministration('dlcounter')) {
                $this->handleAdministration();
            }
        }
    }

    /** @return void */
    private function handleAdministration()
    {
        global $o, $admin, $action;

        $o .= print_plugin_admin('on');
        switch ($admin) {
            case '':
                $o .= $this->renderPluginInfo();
                break;
            case 'plugin_main':
                $o .= $this->renderStatistics();
                break;
            default:
                $o .= plugin_admin_common();
        }
    }

    /**
     * @return string
     */
    private function renderPluginInfo()
    {
        ob_start();
        (new InfoController)->defaultAction();
        return (string) ob_get_clean();
    }

    /**
     * @return string
     */
    private function renderStatistics()
    {
        ob_start();
        (new MainAdminController)->defaultAction();
        return (string) ob_get_clean();
    }
}
