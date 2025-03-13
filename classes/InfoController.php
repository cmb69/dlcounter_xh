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
use Plib\SystemChecker;
use Plib\View;

class InfoController
{
    /** @var string */
    private $pluginFolder;

    /** @var SystemChecker */
    private $systemChecker;

    /** @var View */
    private $view;

    public function __construct(string $pluginFolder, SystemChecker $systemChecker, View $view)
    {
        $this->pluginFolder = $pluginFolder;
        $this->systemChecker = $systemChecker;
        $this->view = $view;
    }

    public function defaultAction(Request $request): string
    {
        return $this->view->render("info", [
            'logo' => "{$this->pluginFolder}dlcounter.png",
            'version' => DLCOUNTER_VERSION,
            'checks' => [
                $this->checkPhpVersion('7.1.0'),
                $this->checkExtension('fileinfo'),
                $this->checkXhVersion('1.7.0'),
                $this->checkJQuery(),
                $this->checkWritability("{$this->pluginFolder}config/"),
                $this->checkWritability("{$this->pluginFolder}css/"),
                $this->checkWritability("{$this->pluginFolder}languages/"),
            ],
        ]);
    }

    /** @return array{class:string,label:string,stateLabel:string} */
    private function checkPhpVersion(string $version): array
    {
        $state = $this->systemChecker->checkVersion(PHP_VERSION, $version) ? 'success' : 'fail';
        return [
            'class' => "xh_$state",
            'label' => $this->view->plain('syscheck_phpversion', $version),
            'stateLabel' => $this->view->plain("syscheck_$state"),
        ];
    }

    /** @return array{class:string,label:string,stateLabel:string} */
    private function checkExtension(string $name): array
    {
        $state = $this->systemChecker->checkExtension($name) ? 'success' : 'warning';
        return [
            'class' => "xh_$state",
            'label' => $this->view->plain('syscheck_extension', $name),
            'stateLabel' => $this->view->plain("syscheck_$state"),
        ];
    }

    /** @return array{class:string,label:string,stateLabel:string} */
    private function checkXhVersion(string $version): array
    {
        $state = $this->systemChecker->checkVersion(CMSIMPLE_XH_VERSION, "CMSimple_XH $version") ? 'success' : 'fail';
        return [
            'class' => "xh_$state",
            'label' => $this->view->plain('syscheck_xhversion', $version),
            'stateLabel' => $this->view->plain("syscheck_$state"),
        ];
    }

    /** @return array{class:string,label:string,stateLabel:string} */
    private function checkJQuery()
    {
        $state = $this->systemChecker->checkPlugin("jquery") ? 'success' : 'fail';
        return [
            'class' => "xh_$state",
            'label' => $this->view->plain('syscheck_plugin', 'jquery'),
            'stateLabel' => $this->view->plain("syscheck_$state"),
        ];
    }

    /** @return array{class:string,label:string,stateLabel:string} */
    private function checkWritability(string $folder): array
    {
        $state = $this->systemChecker->checkWritability($folder) ? 'success' : 'warning';
        return [
            'class' => "xh_$state",
            'label' => $this->view->plain('syscheck_writable', $folder),
            'stateLabel' => $this->view->plain("syscheck_$state"),
        ];
    }
}
