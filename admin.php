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

/*
 * Prevent direct access.
 */
if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

XH_registerStandardPluginMenuItems(true);

/*
 * Handle the plugin administration.
 */
if (XH_wantsPluginAdministration('dlcounter')) {
    $o .= print_plugin_admin('on');

    switch ($admin) {
        case '':
            $o .= $_Dlcounter->renderPluginInfo();
            break;
        case 'plugin_main':
            $o .= $_Dlcounter->renderStatistics();
            break;
        default:
            $o .= plugin_admin_common($action, $admin, $plugin);
    }
}
