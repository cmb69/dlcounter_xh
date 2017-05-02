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

class Domain
{
    /**
     * @return string
     */
    public function downloadFolder()
    {
        global $pth, $plugin_cf;

        return $pth['folder']['base']
            . $plugin_cf['dlcounter']['folder_downloads'];
    }

    /**
     * @return string
     */
    public function imageFolder()
    {
        global $pth;

        return $pth['folder']['plugins'] . 'dlcounter/images/';
    }

    /**
     * @return string
     */
    public function logoPath()
    {
        global $pth;

        return $pth['folder']['plugins'] . 'dlcounter/dlcounter.png';
    }

    /**
     * @return array
     */
    public function readDb()
    {
        $result = array();
        $filename = $this->dataFolder() . 'dlcounter.dat';
        if (is_readable($filename)) {
            $lines = file($filename);
        } else {
            $lines = false;
        }
        if ($lines !== false) {
            foreach ($lines as $line) {
                $result[] = explode("\t", rtrim($line));
            }
        }
        return $result;
    }

    /**
     * @param int $timestamp
     * @param string $basename
     * @return void
     * @throws WriteException
     */
    public function log($timestamp, $basename)
    {
        global $plugin_tx;

        $line = $timestamp . "\t" . basename($basename) . "\n";
        $filename = $this->dataFolder() . 'dlcounter.dat';
        if (!is_dir(dirname($filename))
            || file_put_contents($filename, $line, FILE_APPEND | LOCK_EX) === false
        ) {
            throw new WriteException(
                sprintf($plugin_tx['dlcounter']['message_cantwrite'], $filename)
            );
        }
    }

    /**
     * @return array
     */
    public function systemChecks()
    {
        global $pth, $tx, $plugin_tx;

        $ptx = $plugin_tx['dlcounter'];
        $phpVersion = '5.4.0';
        $result = array();
        $result[sprintf($ptx['syscheck_phpversion'], $phpVersion)]
            = version_compare(PHP_VERSION, $phpVersion) >= 0
                ? 'ok' : 'fail';
        foreach (array() as $ext) {
            $result[sprintf($ptx['syscheck_extension'], $ext)]
                = extension_loaded($ext) ? 'ok' : 'fail';
        }
        $result[$ptx['syscheck_encoding']]
            = strtoupper($tx['meta']['codepage']) == 'UTF-8' ? 'ok' : 'warn';
        $result[$ptx['syscheck_jquery']]
            = file_exists($pth['folder']['plugins'] . 'jquery/jquery.inc.php')
                ? 'ok' : 'fail';
        foreach (array('config/', 'css/', 'languages/') as $folder) {
            $folders[] = $pth['folder']['plugins'] . 'dlcounter/' . $folder;
        }
        foreach ($folders as $folder) {
            $result[sprintf($ptx['syscheck_writable'], $folder)]
                = is_writable($folder) ? 'ok' : 'warn';
        }
        return $result;
    }

    /**
     * @return string
     */
    private function dataFolder()
    {
        global $pth, $sl, $cf;

        if ($sl === $cf['language']['default']) {
            return $pth['folder']['content'];
        } else {
            return dirname($pth['folder']['content']) . "/";
        }
    }
}
