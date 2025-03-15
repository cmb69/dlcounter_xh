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

namespace Dlcounter\Infra;

use Generator;

class TsvFile
{
    /** @return Generator<list<string>> */
    public function records(string $filename): Generator
    {
        if (is_readable($filename)) {
            $stream = fopen($filename, "r");
            if ($stream) {
                flock($stream, LOCK_SH);
                while (($line = fgets($stream))) {
                    yield explode("\t", rtrim($line, "\r\n"));
                }
                flock($stream, LOCK_UN);
                fclose($stream);
            }
        }
    }

    /** @param list<string> $record */
    public function append(string $filename, array $record): bool
    {
        if (is_dir(dirname($filename))) {
            $line = implode("\t", $record) . "\n";
            if ($stream = fopen($filename, "a")) {
                flock($stream, LOCK_EX);
                fwrite($stream, $line);
                flock($stream, LOCK_UN);
                fclose($stream);
                return true;
            }
        }
        return false;
    }
}
