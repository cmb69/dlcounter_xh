# Dlcounter_XH

Dlcounter_XH is a simple download counter for CMSimple_XH. Instead of a link
to the downloadable file, it offers the download as HTML form, which should
be ignored by bots, so the download count is somewhat more accurate. Please
note that absolute accurate download counts cannot be archieved with
Dlcounter_XH (and probably neither with any other download counter) as e.g.
cancelled downloads or multiple downloads initiated by download managers are
not especially catered to.

Download statistics are available in the administration part of the plugin.

- [Requirements](#requirements)
- [Download](#download)
- [Installation](#installation)
- [Settings](#settings)
- [Usage](#usage)
- [Limitations](#limitations)
- [Troubleshooting](#troubleshooting)
- [License](#license)
- [Credits](#credits)

## Requirements

Dlcounter_XH is a plugin for [CMSimple_XH](https://www.cmsimple-xh.org/).
It requires CMSimple_XH ≥ 1.7.0 and PHP ≥ 7.1.0.
Dlcounter_XH also requires [Plib_XH](https://github.com/cmb69/plib_xh) ≥ 1.2;
if that is not already installed (see `Settings` → `Info`),
get the [lastest release](https://github.com/cmb69/plib_xh/releases/latest),
and install it.

## Download

The [lastest release](https://github.com/cmb69/dlcounter_xh/releases/latest)
is available for download on Github.

## Installation

The installation is done as with many other CMSimple_XH plugins.

1. Backup the data on your server.
1. Unzip the distribution on your computer.
1. Upload the whole directory `dlcounter/` to your server into
   the plugins directory of CMSimple_XH.
1. Set write permissions for the subdirectories `config/`,
   `css/` and `languages/`.
1. Navigate to `Plugins` → `Dlcounter` in the back-end to check
   if all requirements are fulfilled.

## Settings

The configuration of the plugin is done as with many other CMSimple_XH plugins
in the back-end of the Website. Go to `Plugins` → `Dlcounter`.

You can change the default settings of Dlcounter_XH under `Config`. Hints
for the options will be displayed when hovering over the help icons with
your mouse.

Localization is done under `Language`. You can translate the character
strings to your own language (if there is no appropriate language file
available), or customize them according to your needs.

The look of Dlcounter_XH can be customized under `Stylesheet`.

## Usage

All downloads that shall be counted have to placed directly in the
configured downloads folder (see `Config` → `Folder` → `Downloads`).
To display the download form for the file `download.pdf` on a page, enter:

    {{{dlcounter('download.pdf')}}}

You can safely check if the download works as expected, by triggering it
when you are logged in as administrator; these downloads will not be
counted.

To prohibit direct downloading of the files (somebody may be able to guess the
URL of a file), you have to protect the configured download folder by any means
your server provides (for Apache servers you can usually use a copy of
`cmsimple/.htaccess`).

To view the download statistics browse to `Plugins` → `Dlcounter` → `Statistics`.
You can sort the tables by clicking on the respective column heading.

## Limitations

If the Fileinfo PHP extension is not available, the downloads will be sent
with the generic MIME type `application/octet-stream`.
This *may* result in imperfect behavior of some browsers, but is usually
nothing you have to be concerned with.

## Troubleshooting

Report bugs and ask for support either on
[Github](https://github.com/cmb69/dlcounter_xh/issues)
or in the [CMSimple_XH Forum](https://cmsimpleforum.com/).

## License

Dlcounter_XH is free software: you can redistribute it and/or modify it
under the terms of the GNU General Public License as published
by the Free Software Foundation, either version 3 of the License,
or (at your option) any later version.

Dlcounter_XH is distributed in the hope that it will be useful,
but without any warranty; without even the implied warranty of merchantibility
or fitness for a particular purpose.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Dlcounter_XH. If not, see https://www.gnu.org/licenses/.

Copyright © Christoph M. Becker

Estonian translation © Alo Tanavots<br>
Russian translation © Lybomyr Kydray<br>
Slovak translation © Dr. Martin Sereday

## Credits

Dlcounter_XH uses the [jQuery Tablesorter plugin](https://github.com/christianbach/tablesorter).
Many thanks to Christian Bach for releasing it under GPL.

The plugin logo is designed by [YellowIcon](http://yellowicon.com/).
Many thanks for publishing this icon under GPL.

Many thanks to the community at the [CMSimple_XH forum](https://www.cmsimpleforum.com/)
for tips, suggestions and testing.
Special thanks to *frase* for offering an overhauled back-end stylesheet.

And last but not least many thanks to [Peter Harteg](https://www.harteg.dk/),
the “father” of CMSimple, and all developers of [CMSimple_XH](https://www.cmsimple-xh.org/)
without whom this amazing CMS would not exist.
