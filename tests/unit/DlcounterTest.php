<?php

/**
 * Testing the controller.
 *
 * PHP version 5
 *
 * @category  Testing
 * @package   Dlcounter
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2016 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Dlcounter_XH
 */

require_once '../../cmsimple/functions.php';
require_once './classes/Dlcounter.php';

/**
 * Test dummy.
 *
 * @return void
 */
function Dlcounter_includeJQuery()
{
    // pass
}

/**
 * Test dummy.
 *
 * @return void
 */
function Include_jQuery()
{
    // pass
}

/**
 * Test dummy.
 *
 * @param string $name     A name.
 * @param string $filename A filename.
 *
 * @return void
 */
function Include_jQueryPlugin($name, $filename)
{
    // pass
}

/**
 * Testing the controller.
 *
 * @category Testing
 * @package  Dlcounter
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Dlcounter_XH
 */
class DlcounterTest extends PHPUnit_Framework_TestCase
{
    /**
     * The test subject.
     *
     * @var Dlcounter
     */
    protected $subject;

    /**
     * The model.
     *
     * @var Dlcounter_Domain
     */
    protected $model;

    /**
     * ???
     *
     * @var array
     */
    protected $records;

    /**
     * The header() mock.
     *
     * @var PHPUnit_Extensions_MockFunction
     */
    protected $headerMock;

    /**
     * The shead() mock.
     *
     * @var PHPUnit_Extensions_MockFunction
     */
    protected $sHeadMock;

    /**
     * The exit() mock.
     *
     * @var PHPUnit_Extensions_MockFunction
     */
    protected $exitMock;

    /**
     * Sets up the test fixture.
     *
     * @return void
     */
    public function setUp()
    {
        $this->defineConstant('XH_ADM', false);
        $this->defineConstant('DLCOUNTER_VERSION', 'foobar');
        $this->model = $this->getMockBuilder('Dlcounter_Domain')->getMock();
        $this->subject = new Dlcounter($this->model);
        $this->records = array(
            array(111, 'foo'),
            array(222, 'bar'),
            array(333, 'foo')
        );
        $this->headerMock = new PHPUnit_Extensions_MockFunction(
            'header', $this->subject
        );
        $this->sHeadMock = new PHPUnit_Extensions_MockFunction(
            'shead', $this->subject
        );
        $this->exitMock = new PHPUnit_Extensions_MockFunction(
            'XH_exit', $this->subject
        );
    }

    /**
     * Tests that the download form is rendered.
     *
     * @return void
     */
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
        @$this->assertTag(
            $matcher,
            $this->subject->renderDownloadForm('version.nfo')
        );
    }

    /**
     * Tests the the download form without a downloadable file is rendered.
     *
     * @return void
     */
    public function testRenderDownloadFormWithoutDownloadFile()
    {
        $matcher = array(
            'tag' => 'p',
            'attributes' => array(
                'class' => 'xh_fail'
            )
        );
        @$this->assertTag(
            $matcher,
            $this->subject->renderDownloadForm('foo')
        );
    }

    /**
     * Tests that the download form with a downloadable file in a subfolder is
     * rendered.
     *
     * @return void
     */
    public function testRenderDownloadFormWithDownloadFileInSubfolder()
    {
        $this->model->expects($this->once())
            ->method('downloadFolder')
            ->will($this->returnValue('./'));
        $matcher = array(
            'tag' => 'p',
            'attributes' => array(
                'class' => 'xh_fail'
            )
        );
        @$this->assertTag(
            $matcher,
            $this->subject->renderDownloadForm('languages/en.php')
        );
    }

    /**
     * Tests the download.
     *
     * @return void
     */
    public function testDownload()
    {
        $this->model->expects($this->once())
            ->method('downloadFolder')
            ->will($this->returnValue('./'));
        $this->exitMock->expects($this->once());
        $this->expectOutputString(
            'Dlcounter_XH,@DLCOUNTER_VERSION@,@DLCOUNTER_VERSION@,,'
            . ',http://3-magi.net/?CMSimple_XH/Dlcounter_XH'
            . ',http://3-magi.net/downloads/versioninfo/dlcounter1.nfo'
        );
        $this->subject->download('version.nfo');
    }

    /**
     * Tests that the download can't log.
     *
     * @return void
     *
     * @global string $o The output.
     */
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
            'attributes' => array('class' => 'xh_fail')
        );
        @$this->assertTag($matcher, $o);
    }

    /**
     * Tests that the download is not found.
     *
     * @return void
     */
    public function testDownloadNotFound()
    {
        $this->model->expects($this->once())
            ->method('downloadFolder')
            ->will($this->returnValue('./'));
        $this->sHeadMock->expects($this->once())->with('404');
        $this->subject->download('foo.bar');
    }

    /**
     * Tests that the plugin info renders the system check.
     *
     * @return void
     */
    public function testRenderPluginInfoHasSystemCheck()
    {
        $this->model->expects($this->once())
            ->method('systemChecks')
            ->will($this->returnValue(array('foo' => 'bar')));
        $actual = $this->subject->renderPluginInfo();
        $matcher = array(
            'tag' => 'ul',
            'attributes' => array('class' => 'dlcounter_system_check'),
            'children' => array(
                'only' => array('tag' => 'li'),
                'count' => 1
            )
        );
        @$this->assertTag($matcher, $actual);
    }

    /**
     * Tests that the plugin info renders the version.
     *
     * @return void
     */
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
        @$this->assertTag($matcher, $actual);
    }

    /**
     * Tests the rendering of the statistics.
     *
     * @return void
     */
    public function testRenderStatistics()
    {
        $this->model->expects($this->once())
            ->method('readDb')
            ->will($this->returnValue($this->records));
        $matcher = array(
            'tag' => 'div',
            'id' => 'dlcounter_stats'
        );
        @$this->assertTag($matcher, $this->subject->renderStatistics());
    }

    /**
     * Tests the rendering of the statistics where the data file can't be read.
     *
     * @return void
     */
    public function testRenderStatisticsWhereDataFileCantBeRead()
    {
        $this->model->expects($this->once())
            ->method('readDb')
            ->will($this->returnValue($this->records));
        $matcher = array(
            'tag' => 'div',
            'id' => 'dlcounter_stats'
        );
        @$this->assertTag($matcher, $this->subject->renderStatistics());
    }

    /**
     * Tests that the statistics renders the summary table.
     *
     * @return void
     */
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
        @$this->assertTag($matcher, $this->subject->renderStatistics());
    }

    /**
     * Tests that the statistics renders the details table.
     *
     * @return void
     */
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
        @$this->assertTag($matcher, $this->subject->renderStatistics());
    }

    /**
     * (Re)defines a constant.
     *
     * @param string $name  A name.
     * @param string $value A value.
     *
     * @return void
     */
    protected function defineConstant($name, $value)
    {
        if (!defined($name)) {
            define($name, $value);
        } else {
            runkit_constant_redefine($name, $value);
        }
    }
}

?>
