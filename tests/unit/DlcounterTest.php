<?php

require_once 'vfsStream/vfsStream.php';
require_once './classes/Dlcounter.php';

const DLCOUNTER_VERSION = 'foobar';

function tag($string)
{
    return $string;
}

function include_jQuery() {}

function include_jQueryPlugin($name, $filename) {}

class DlcounterTest extends PHPUnit_Framework_TestCase
{
    private $_subject;

    public function setUp()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('test'));
        $this->setUpJqueryInc();
        $this->_subject = new Dlcounter();

    }

    protected function setUpJqueryInc()
    {
        mkdir(vfsStream::url('test/jquery/'));
        file_put_contents(vfsStream::url('test/jquery/jquery.inc.php'), '');
    }

    protected function setUpDataFile()
    {
        $filename = vfsStream::url('test/dlcounter/data/');
        mkdir($filename, 0777, true);
        $filename .= 'downloads.dat';
        file_put_contents($filename, "12345\tfoo\n");
    }

    public function testMain()
    {
        $matcher = array(
            'tag' => 'form'
        );
        $filename = vfsStream::url('test/') . '/test.txt';
        file_put_contents($filename, 'foobar');
        $this->assertTag($matcher, $this->_subject->main($filename));
    }

    public function testVersion()
    {
        $matcher = array(
            'tag' => 'h1',
            'content' => 'Dlcounter_XH'
        );
        $this->assertTag($matcher, $this->_subject->version());
    }

    public function testAdminMain()
    {
        global $pth;

        $matcher = array(
            'tag' => 'div',
            'id' => 'dlcounter_stats'
        );
        $this->setUpDataFile();
        $pth = array('folder' => array('plugins' => vfsStream::url('test/')));
        $this->assertTag($matcher, $this->_subject->adminMain());
    }
}

?>
