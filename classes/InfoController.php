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

use Pfw\SystemCheckService;
use Pfw\View\View;

class InfoController
{
    /**
     * @var string
     */
    private $pluginFolder;

    public function __construct()
    {
        global $pth;

        $this->pluginFolder = "{$pth['folder']['plugins']}dlcounter/";
    }

    public function defaultAction()
    {
        (new View('dlcounter'))
            ->template('info')
            ->data([
                'logo' => "{$this->pluginFolder}dlcounter.png",
                'version' => Plugin::VERSION,
                'checks' => (new SystemCheckService)
                    ->minPhpVersion('5.4.0')
                    ->extension('fileinfo')
                    ->minXhVersion('1.6.3')
                    ->plugin('jquery')
                    ->writable("{$this->pluginFolder}config/")
                    ->writable("{$this->pluginFolder}css/")
                    ->writable("{$this->pluginFolder}languages/")
                    ->getChecks()
            ])
            ->render();
    }
}
