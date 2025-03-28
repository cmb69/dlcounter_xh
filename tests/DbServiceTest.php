<?php

namespace Dlcounter;

use Dlcounter\Infra\TsvFile;
use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStream;

class DbServiceTest extends TestCase
{
    /**
     * @var DbService
     */
    private $subject;

    protected function setUp(): void
    {
        global $pth, $cf, $plugin_cf;

        $pth = array(
            'folder' => array(
                'base' => vfsStream::url('test/'),
                'plugins' => vfsStream::url('test/plugins/'),
                'userfiles' => vfsStream::url('test/')
            )
        );
        $cf = ['language' => ['default' => "en"]];
        $plugin_cf = array(
            'dlcounter' => array(
                'folder_downloads' => 'downloads/'
            )
        );
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('test'));
        mkdir(vfsStream::url('test/content/'), 0777, true);
        touch(vfsStream::url('test/content/dlcounter.csv'));
        $this->subject = new DbService(vfsStream::url('test/content/'), new TsvFile());
    }

    public function testReadEmptyDb()
    {
        $this->assertEmpty($this->subject->readDb());
    }

    public function testReadDb()
    {
        $records = array(
            (object) ['time' => 111, 'name' => 'foo'],
            (object) ['time' => 222, 'name' => 'bar'],
            (object) ['time' => 333, 'name' => 'foo']
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
            array((object) ['time' => $timestamp, 'name' => 'foo']),
            $this->subject->readDb()
        );
    }
}
