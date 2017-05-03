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

        $folder = $pth['folder']['userfiles'] . $plugin_cf['dlcounter']['folder_downloads'];
        if ($folder[strlen($folder) - 1] !== '/') {
            $folder .= '/';
        }
        return $folder;
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
     * @return array
     */
    public function readDb()
    {
        $result = array();
        $filename = $this->dataFolder() . 'dlcounter.csv';
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

    public function getDownloadCountOf($basename)
    {
        $result = 0;
        $downloads = $this->readDb();
        foreach ($downloads as $download) {
            if ($download[1] === $basename) {
                $result++;
            }
        }
        return $result;
    }

    /**
     * @param int $timestamp
     * @param string $basename
     * @return bool
     */
    public function log($timestamp, $basename)
    {
        $line = $timestamp . "\t" . basename($basename) . "\n";
        $filename = $this->dataFolder() . 'dlcounter.csv';
        return is_dir(dirname($filename))
            && file_put_contents($filename, $line, FILE_APPEND | LOCK_EX) !== false;
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
