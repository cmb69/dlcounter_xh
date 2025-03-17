<?php

/**
 * Copyright (c) Christoph M. Becker
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

class DownloadService
{
    public function deliverDownload(string $filename, string $mimeType): void
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $basename = urlencode(basename($filename));
        header("Content-Type: {$mimeType}");
        header("Content-Disposition: attachment; filename=file.$extension; filename*=UTF-8''$basename");
        header('Content-Length: ' . filesize($filename));
        while (ob_get_level()) {
            ob_end_clean();
        }
        readfile($filename);
        XH_exit();
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
