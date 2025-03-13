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

class DownloadService
{
    /**
     * @param string $filename
     * @return void
     */
    public function deliverDownload($filename)
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $basename = urlencode(basename($filename));
        $mimeType = $this->mimeType($filename);
        header("Content-Type: {$mimeType}");
        header("Content-Disposition: attachment; filename=file.$extension; filename*=UTF-8''$basename");
        header('Content-Length: ' . filesize($filename));
        while (ob_get_level()) {
            ob_end_clean();
        }
        readfile($filename);
        XH_exit();
    }

    private function mimeType(string $filename): string
    {
        if (function_exists("mime_content_type")) {
            $mimeType = mime_content_type($filename);
        }
        return isset($mimeType) && is_string($mimeType) ? $mimeType : "application/octet-stream";
    }
}
