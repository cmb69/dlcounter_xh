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

/**
 * @param string $filename
 * @return string
 */
function dlcounter($filename)
{
    $controller = new Dlcounter\MainController($filename);
    if (isset($_POST['dlcounter']) && $_POST['dlcounter'] === $filename) {
        $action = 'downloadAction';
    } else {
        $action = 'defaultAction';
    }
    ob_start();
    $controller->{$action}();
    return ob_get_clean();
}

(new Dlcounter\Plugin)->run();
