<?php

require_once 'vfsStream/vfsStream.php';
require_once './classes/Dlcounter.php';

const DLCOUNTER_VERSION = 'foobar';

class ExitException extends Exception {}

class NotFoundException extends Exception {}

function shead($string)
{
    throw new NotFoundException();
}

function tag($string)
{
    return $string;
}

function include_jQuery() {}

function include_jQueryPlugin($name, $filename) {}

function Dlcounter_exit()
{
    throw new ExitException();
}

runkit_function_redefine('header', '$string', '');

class DlcounterTest extends PHPUnit_Framework_TestCase
{
    private $_subject;

    private $_downloadFile;

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
        $contents = "111\tfoo\n222\tbar\n333\tfoo";
        file_put_contents($filename, $contents);
    }

    protected function setUpGlobals()
    {
        global $pth, $plugin_cf;

        $plugin_cf = array(
            'dlcounter' => array(
                'folder_data' => 'dlcounter/data',
                'folder_downloads' => ''
            )
        );
        $pth = array(
            'folder' => array(
                'base' => vfsStream::url('test/'),
                'plugins' => vfsStream::url('test/')
            )
        );
    }

    protected function setUpDownloadFile()
    {
        $this->_downloadFile = vfsStream::url('test/test.txt');
        file_put_contents($this->_downloadFile, 'foobar');

    }

    public function testMain()
    {
        $this->setUpDownloadFile();
        $matcher = array(
            'tag' => 'form',
            'attributes' => array(
                'class' => 'dlcounter',
                'method' => 'GET'
            )
        );
        $this->assertTag($matcher, $this->_subject->main($this->_downloadFile));
    }

    public function testMainWithoutDownloadFile()
    {
        $matcher = array(
            'tag' => 'p',
            'attributes' => array(
                'class' => 'cmsimplecore_warning'
            )
        );
        $this->assertTag($matcher, $this->_subject->main($this->_downloadFile));
    }

    public function testVersion()
    {
        $matcher = array(
            'tag' => 'h1',
            'content' => 'Dlcounter_XH'
        );
        $this->assertTag($matcher, $this->_subject->version());
        $matcher = array(
            'tag' => 'p',
            'content' => DLCOUNTER_VERSION
        );
        $this->assertTag($matcher, $this->_subject->version());
    }

    public function testSystemCheck()
    {
        $this->setUpGlobals();
        $this->setUpDataFile();
        $matcher = array(
            'tag' => 'h4'
        );
        $this->assertTag($matcher, $this->_subject->renderSystemCheck());
    }

    public function testAdminMain()
    {
        $this->setUpGlobals();
        $this->setUpDataFile();
        $matcher = array(
            'tag' => 'div',
            'id' => 'dlcounter_stats'
        );
        $this->assertTag($matcher, $this->_subject->adminMain());
    }

    public function testAdminMainWhereDataFileCantBeRead()
    {
        $this->setUpGlobals();
        $matcher = array(
            'tag' => 'div',
            'id' => 'dlcounter_stats'
        );
        $this->assertTag($matcher, $this->_subject->adminMain());
    }

    public function testAdminMainHasSummaryTable()
    {
        $this->setUpGlobals();
        $this->setUpDataFile();
        $matcher = array(
            'tag' => 'tbody',
            'ancestor' => array(
                'tag' => 'table',
                'id' => 'dlcounter_summary_table'
            ),
            'children' => array(
                'count' => 2,
                'only' => array('tag' => 'tr')
            )
        );
        $this->assertTag($matcher, $this->_subject->adminMain());
    }

    public function testAdminMainHasDetailsTable()
    {
        $this->setUpGlobals();
        $this->setUpDataFile();
        $matcher = array(
            'tag' => 'tbody',
            'ancestor' => array(
                'tag' => 'table',
                'id' => 'dlcounter_details_table'
            ),
            'children' => array(
                'count' => 3,
                'only' => array('tag' => 'tr')
            )
        );
        $this->assertTag($matcher, $this->_subject->adminMain());
    }

    /**
     * @expectedException ExitException
     */
    public function testDownload()
    {
        $this->setUpGlobals();
        $this->setUpDataFile();
        $this->setUpDownloadFile();
        $this->_subject->download($this->_downloadFile);
        $this->expectOutputString('foobar');
    }

    /**
     * @expectedException ExitException
     */
    public function testDownloadWithDefaultDataFolder()
    {
        global $plugin_cf;

        $this->setUpGlobals();
        $this->setUpDataFile();
        $this->setUpDownloadFile();
        $plugin_cf['dlcounter']['folder_data'] = '';
        $this->_subject->download($this->_downloadFile);
        $this->expectOutputString('foobar');
    }

    public function testDownloadWhereDataFileCantBeWritten()
    {
        global $o;

        $o = '';
        $this->setUpGlobals();
        $this->setUpDownloadFile();
        mkdir(vfsStream::url('test/dlcounter/data/downloads.dat'), 0777, true);
        $this->_subject->download($this->_downloadFile);
        $matcher = array(
            'tag' => 'p',
            'attributes' => array('class' => 'cmsimplecore_warning')
        );
        $this->assertTag($matcher, $o);
    }

    /**
     * @expectedException NotFoundException
     */
    public function testDownloadNotFound()
    {
        $this->setUpGlobals();
        $this->setUpDataFile();
        $filename = vfsStream::url('foo');
        $this->_subject->download($filename);
    }
}

?>
