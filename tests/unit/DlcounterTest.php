<?php

require_once './classes/Dlcounter.php';

const XH_ADM = false;

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
    protected $subject;

    protected $model;

    protected $records;

    public function setUp()
    {
        $this->model = $this->getMockBuilder('Dlcounter_Domain')->getMock();
        $this->subject = new Dlcounter($this->model);
        $this->records = array(
            array(111, 'foo'),
            array(222, 'bar'),
            array(333, 'foo')
        );
    }

    public function testRenderDownloadForm()
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
                'method' => 'post'
            ),
            'child' => array(
                'tag' => 'button'
            )
        );
        $this->assertTag(
            $matcher,
            $this->subject->renderDownloadForm('version.nfo')
        );
    }

    public function testRenderDownloadFormWithoutDownloadFile()
    {
        $matcher = array(
            'tag' => 'p',
            'attributes' => array(
                'class' => 'cmsimplecore_warning'
            )
        );
        $this->assertTag(
            $matcher,
            $this->subject->renderDownloadForm('foo')
        );
    }

    public function testRenderDownloadFormWithDownloadFileInSubfolder()
    {
        $this->model->expects($this->once())
            ->method('downloadFolder')
            ->will($this->returnValue('./'));
        $matcher = array(
            'tag' => 'p',
            'attributes' => array(
                'class' => 'cmsimplecore_warning'
            )
        );
        $this->assertTag(
            $matcher,
            $this->subject->renderDownloadForm('languages/en.php')
        );
    }

    /**
     * @expectedException ExitException
     */
    public function testDownload()
    {
        $this->model->expects($this->once())
            ->method('downloadFolder')
            ->will($this->returnValue('./'));
        $this->expectOutputString(
            'Dlcounter_XH,@DLCOUNTER_VERSION@,@DLCOUNTER_VERSION@,,'
            . ',http://3-magi.net/?CMSimple_XH/Dlcounter_XH'
            . ',http://3-magi.net/downloads/versioninfo/dlcounter1.nfo'
        );
        $this->subject->download('version.nfo');
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
        $this->subject->download('version.nfo');
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
        $this->subject->download('foo.bar');
    }

    public function testRenderPluginInfoHasSystemCheck()
    {
        global $plugin_tx;

        $plugin_tx['dlcounter']['syscheck_title'] = 'System check';
        $this->model->expects($this->once())
            ->method('systemChecks')
            ->will($this->returnValue(array('foo' => 'bar')));
        $actual = $this->subject->renderPluginInfo();
        $matcher = array(
            'tag' => 'h4',
            'content' => $plugin_tx['dlcounter']['syscheck_title']
        );
        $this->assertTag($matcher, $actual);
        $matcher = array(
            'tag' => 'ul',
            'attributes' => array('class' => 'pdeditor_system_check'),
            'children' => array(
                'only' => array('tag' => 'li'),
                'count' => 1
            )
        );
        $this->assertTag($matcher, $actual);
    }

    public function testRenderPluginInfoHasVersion()
    {
        $this->model->expects($this->once())
            ->method('systemChecks')
            ->will($this->returnValue(array()));
        $actual = $this->subject->renderPluginInfo();
        $matcher = array(
            'tag' => 'p',
            'content' => DLCOUNTER_VERSION
        );
        $this->assertTag($matcher, $actual);
    }

    public function testRenderStatistics()
    {
        $this->model->expects($this->once())
            ->method('readDb')
            ->will($this->returnValue($this->records));
        $matcher = array(
            'tag' => 'div',
            'id' => 'dlcounter_stats'
        );
        $this->assertTag($matcher, $this->subject->renderStatistics());
    }

    public function testRenderStatisticsWhereDataFileCantBeRead()
    {
        $this->model->expects($this->once())
            ->method('readDb')
            ->will($this->returnValue($this->records));
        $matcher = array(
            'tag' => 'div',
            'id' => 'dlcounter_stats'
        );
        $this->assertTag($matcher, $this->subject->renderStatistics());
    }

    public function testRenderStatisticsHasSummaryTable()
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
        $this->assertTag($matcher, $this->subject->renderStatistics());
    }

    public function testRenderStatisticsHasDetailsTable()
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
        $this->assertTag($matcher, $this->subject->renderStatistics());
    }
}

?>
