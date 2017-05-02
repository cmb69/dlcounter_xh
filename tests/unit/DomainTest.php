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
                'plugins' => vfsStream::url('test/plugins/')
            )
        );
        $plugin_cf = array(
            'dlcounter' => array(
                'folder_data' => '',
                'folder_downloads' => 'downloads/'
            )
        );
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('test'));
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

    public function testLogoPath()
    {
        $this->assertEquals(
            vfsStream::url('test/plugins/dlcounter/dlcounter.png'),
            $this->subject->logoPath()
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
        $folder = vfsStream::url('test/plugins/dlcounter/data/');
        mkdir($folder, 0777, true);
        file_put_contents($folder . 'downloads.dat', $contents);
        $this->assertEquals($records, $this->subject->readDb());
    }

    public function testLog()
    {
        $folder = vfsStream::url('test/plugins/dlcounter/data/');
        mkdir($folder, 0777, true);
        file_put_contents($folder . 'downloads.dat', '');
        $timestamp = time();
        $this->subject->log($timestamp, 'foo');
        $this->assertEquals(
            array(array($timestamp, 'foo')),
            $this->subject->readDb()
        );
    }

    /**
     * @expectedException Dlcounter\WriteException
     */
    public function testCantLog()
    {
        $this->subject->log(time(), 'foo');
    }

    public function testLogInCustomDataFolder()
    {
        global $plugin_cf;

        $plugin_cf['dlcounter']['folder_data'] = 'userfiles';
        $folder = vfsStream::url('test/userfiles/');
        mkdir($folder, 0777, true);
        file_put_contents($folder . 'downloads.dat', '');
        $timestamp = time();
        $this->subject->log($timestamp, 'foo');
        $this->assertEquals(
            array(array($timestamp, 'foo')),
            $this->subject->readDb()
        );
    }

    public function testSystemChecks()
    {
        $this->assertCount(1, $this->subject->systemChecks());
    }
}
