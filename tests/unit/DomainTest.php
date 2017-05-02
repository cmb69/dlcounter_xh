<?php

/**
 * Testing the model.
 *
 * PHP version 5
 *
 * @category  Testing
 * @package   Dlcounter
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2017 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Dlcounter_XH
 */

use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStream;
use Dlcounter\Domain;

/**
 * Testing the model.
 *
 * @category Testing
 * @package  Dlcounter
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Dlcounter_XH
 */
class DomainTest extends PHPUnit_Framework_TestCase
{
    /**
     * The test subject.
     *
     * @var Domain
     */
    protected $subject;

    /**
     * Sets up the test fixture.
     *
     * @return void
     *
     * @global array The paths of system files and folders.
     * @global array The configuration of the plugins.
     */
    public function setUp()
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

    /**
     * Tests the download folder.
     *
     * @return void
     */
    public function testDownloadFolder()
    {
        $this->assertEquals(
            vfsStream::url('test/downloads/'),
            $this->subject->downloadFolder()
        );
    }

    /**
     * Tests the image folder.
     *
     * @return void
     */
    public function testImageFolder()
    {
        $this->assertEquals(
            vfsStream::url('test/plugins/dlcounter/images/'),
            $this->subject->imageFolder()
        );
    }

    /**
     * Tests the logo path.
     *
     * @return void
     */
    public function testLogoPath()
    {
        $this->assertEquals(
            vfsStream::url('test/plugins/dlcounter/dlcounter.png'),
            $this->subject->logoPath()
        );
    }

    /**
     * Tests reading an empty database.
     *
     * @return void
     */
    public function testReadEmptyDb()
    {
        $this->assertEmpty($this->subject->readDb());
    }

    /**
     * Tests reading the database.
     *
     * @return void
     */
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

    /**
     * Tests logging.
     *
     * @return void
     */
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
     * Tests that failed logging throws exception.
     *
     * @return void
     *
     * @expectedException Dlcounter\WriteException
     */
    public function testCantLog()
    {
        $this->subject->log(time(), 'foo');
    }

    /**
     * Tests logging in custom data folder.
     *
     * @return void
     *
     * @global array The configuration of the plugins.
     */
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

    /**
     * Tests the system checks.
     *
     * @return void
     */
    public function testSystemChecks()
    {
        $this->assertCount(1, $this->subject->systemChecks());
    }
}

?>
