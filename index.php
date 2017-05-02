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

/*
 * Prevent direct access.
 */
if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

define('DLCOUNTER_VERSION', '@DLCOUNTER_VERSION@');

/**
 * @var Dlcounter
 */
$_Dlcounter = new Dlcounter\Controller(new Dlcounter\Domain());

/**
 * @param string $filename
 * @return string
 */
function dlcounter($filename)
{
    global $_Dlcounter;

    return $_Dlcounter->renderDownloadForm($filename);
}

function Dlcounter_includeJQuery()
{
    global $pth;

    include_once $pth['folder']['plugins'] . 'jquery/jquery.inc.php';
}

if (!function_exists('XH_exit')) {
    /**
     * Exits the script.
     *
     * We can't call exit directly, because that breaks unit tests,
     * so we use this workaround.
     *
     * @return void
     */
    function XH_exit()
    {
        exit;
    }
}

/*
 * Handle the download request.
 */
if (isset($_POST['dlcounter']) && $_POST['dlcounter'] != '') {
    $_Dlcounter->download(stsl($_POST['dlcounter']));
}
