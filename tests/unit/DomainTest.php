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

use PHPUnit_Framework_TestCase;
use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStream;

class DomainTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Domain
     */
    private $subject;

    protected function setUp()
    {
        global $pth, $plugin_cf;

        $pth = array(
            'folder' => array(
                'base' => vfsStream::url('test/'),
                'content' => vfsStream::url('test/content/'),
                'plugins' => vfsStream::url('test/plugins/'),
                'userfiles' => vfsStream::url('test/')
            )
        );
        $plugin_cf = array(
            'dlcounter' => array(
                'folder_downloads' => 'downloads/'
            )
        );
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('test'));
        mkdir(vfsStream::url('test/content/'), 0777, true);
        touch(vfsStream::url('test/content/dlcounter.csv'));
        $this->subject = new Domain();
    }

    public function testDownloadFolder()
    {
        $this->assertEquals(
            vfsStream::url('test/downloads/'),
            $this->subject->downloadFolder()
        );
    }

    public function testImageFolder()
    {
        $this->assertEquals(
            vfsStream::url('test/plugins/dlcounter/images/'),
            $this->subject->imageFolder()
        );
    }

    public function testReadEmptyDb()
    {
        $this->assertEmpty($this->subject->readDb());
    }

    public function testReadDb()
    {
        $records = array(
            array(111, 'foo'),
            array(222, 'bar'),
            array(333, 'foo')
        );
        $contents = "111\tfoo\n222\tbar\n333\tfoo\n";
        $folder = vfsStream::url('test/content/');
        file_put_contents($folder . 'dlcounter.csv', $contents);
        $this->assertEquals($records, $this->subject->readDb());
    }

    public function testLog()
    {
        $folder = vfsStream::url('test/plugins/dlcounter/data/');
        mkdir($folder, 0777, true);
        file_put_contents($folder . 'dlcounter.csv', '');
        $timestamp = time();
        $this->subject->log($timestamp, 'foo');
        $this->assertEquals(
            array(array($timestamp, 'foo')),
            $this->subject->readDb()
        );
    }
}
