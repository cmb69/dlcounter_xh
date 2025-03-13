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

class DbService
{
    /** @var string */
    private $dataFolder;

    public function __construct(string $dataFolder)
    {
        $this->dataFolder = $dataFolder;
    }

    /**
     * @return list<object{name:string,time:string}>
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
                list($time, $name) = explode("\t", rtrim($line), 2);
                $result[] = (object) compact('name', 'time');
            }
        }
        return $result;
    }

    /**
     * @param string $basename
     * @return int
     */
    public function getDownloadCountOf($basename)
    {
        $result = 0;
        $downloads = $this->readDb();
        foreach ($downloads as $download) {
            if ($download->name === $basename) {
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
        return $this->dataFolder;
    }

    public function isReadable(string $filename): bool
    {
        return is_readable($filename);
    }

    public function fileSize(string $filename): int
    {
        return (int) filesize($filename);
    }
}
