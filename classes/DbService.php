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

use Dlcounter\Infra\TsvFile;

class DbService
{
    /** @var string */
    private $dataFolder;

    /** @var TsvFile */
    private $tsvFile;

    public function __construct(string $dataFolder, TsvFile $tsvFile)
    {
        $this->dataFolder = $dataFolder;
        $this->tsvFile = $tsvFile;
    }

    /**
     * @return list<object{name:string,time:string}>
     */
    public function readDb()
    {
        $result = [];
        foreach ($this->tsvFile->records($this->dataFolder() . 'dlcounter.csv') as $record) {
            if (count($record) === 2) {
                $result[] = (object) [
                    "time" => $record[0],
                    "name" => $record[1],
                ];
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
        foreach ($this->tsvFile->records($this->dataFolder() . 'dlcounter.csv') as $record) {
            if (count($record) === 2 && $record[1] === $basename) {
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
        return $this->tsvFile->append($this->dataFolder() . 'dlcounter.csv', [
            (string) $timestamp,
            basename($basename)
        ]);
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
