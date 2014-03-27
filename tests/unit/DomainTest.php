<?php

require_once 'vfsStream/vfsStream.php';
require_once './classes/Dlcounter.php';

class DomainTest extends PHPUnit_Framework_TestCase
{
    protected $subject;

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
        $this->subject = new Dlcounter_Domain();
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
        $this->subject->log('foo');
        $this->assertCount(1, $this->subject->readDb());
    }

    /**
     * @expectedException Dlcounter_WriteException
     */
    public function testCantLog()
    {
        $folder = vfsStream::url('test/plugins/dlcounter/data/');
        $this->subject->log('foo');
    }

    public function testLogInCustomDataFolder()
    {
        global $plugin_cf;

        $plugin_cf['dlcounter']['folder_data'] = 'userfiles';
        $folder = vfsStream::url('test/userfiles/');
        mkdir($folder, 0777, true);
        file_put_contents($folder . 'downloads.dat', '');
        $this->subject->log('foo');
        $this->assertCount(1, $this->subject->readDb());
    }

    public function testSystemChecks()
    {
        $this->assertCount(1, $this->subject->systemChecks());
    }
}

?>
