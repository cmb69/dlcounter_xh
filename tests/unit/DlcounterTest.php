<?php

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

function Dlcounter_includeJQuery() {}

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

    protected $model;

    protected $records;

    private $_downloadFile;

    public function setUp()
    {
        $this->model = $this->getMockBuilder('Dlcounter_Domain')->getMock();
        $this->_subject = $this->getMockBuilder('DlCounter')
            ->setConstructorArgs(array($this->model))
            ->setMethods(array('includeJQuery'))
            ->getMock();
        $this->records = array(
            array(111, 'foo'),
            array(222, 'bar'),
            array(333, 'foo')
        );
    }

    public function testMain()
    {
        $this->model->expects($this->once())
            ->method('downloadFolder')
            ->will($this->returnValue('./'));
        $this->model->expects($this->once())
            ->method('imageFolder')
            ->will($this->returnValue(''));
        $matcher = array(
            'tag' => 'form',
            'attributes' => array(
                'class' => 'dlcounter',
                'method' => 'GET'
            )
        );
        $this->assertTag($matcher, $this->_subject->main('version.nfo'));
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
        $this->model->expects($this->once())
            ->method('systemChecks')
            ->will($this->returnValue(array('foo' => 'bar')));
        $matcher = array(
            'tag' => 'ul',
            'children' => array(
                'only' => array('tag' => 'li'),
                'count' => 1
            )
        );
        $this->assertTag($matcher, $this->_subject->renderSystemCheck());
    }

    public function testAdminMain()
    {
        $this->model->expects($this->once())
            ->method('readDb')
            ->will($this->returnValue($this->records));
        $matcher = array(
            'tag' => 'div',
            'id' => 'dlcounter_stats'
        );
        $this->assertTag($matcher, $this->_subject->adminMain());
    }

    public function testAdminMainWhereDataFileCantBeRead()
    {
        $this->model->expects($this->once())
            ->method('readDb')
            ->will($this->returnValue($this->records));
        $matcher = array(
            'tag' => 'div',
            'id' => 'dlcounter_stats'
        );
        $this->assertTag($matcher, $this->_subject->adminMain());
    }

    public function testAdminMainHasSummaryTable()
    {
        $this->model->expects($this->once())
            ->method('readDb')
            ->will($this->returnValue($this->records));
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
        $this->model->expects($this->once())
            ->method('readDb')
            ->will($this->returnValue($this->records));
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
        $this->model->expects($this->once())
            ->method('downloadFolder')
            ->will($this->returnValue('./'));
        $this->_subject->download('version.nfo');
        $this->expectOutputString('foobar');
    }

    public function testDownloadCantLog()
    {
        global $o;

        $o = '';
        $this->model->expects($this->once())
            ->method('downloadFolder')
            ->will($this->returnValue('./'));
        $this->model->expects($this->once())
            ->method('log')
            ->will($this->throwException(new Dlcounter_WriteException()));
        $this->_subject->download('version.nfo');
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
        $this->model->expects($this->once())
            ->method('downloadFolder')
            ->will($this->returnValue('./'));
        $this->_subject->download('foo.bar');
    }
}

?>
