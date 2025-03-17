<?php

namespace Dlcounter;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\FakeRequest;
use Plib\View;

class MainControllerTest extends TestCase
{
    public function testDoesNothingWhenSearching(): void
    {
        $sut = $this->sut($this->dbService(), $this->downloadService());
        $request = new FakeRequest(["url" => "http://example.com/?&function=search"]);
        $this->assertSame("", $sut($request, "test.txt")->output());
    }

    public function testRendersDownloadForm(): void
    {
        $sut = $this->sut($this->dbService(), $this->downloadService());
        Approvals::verifyHtml($sut(new FakeRequest(), "test.txt")->output());
    }

    public function testReportsUnreadableDownload(): void
    {
        $sut = $this->sut($this->dbService(), $this->downloadService(false));
        $this->assertStringContainsString(
            "Can't read file &quot;test.txt&quot;!",
            $sut(new FakeRequest(), "test.txt")->output()
        );
    }

    public function testMakesDownloadAvailableForAdmin(): void
    {
        $downloadService = $this->downloadService();
        $downloadService->expects($this->once())->method("deliverDownload")->with("test.txt");
        $sut = $this->sut($this->dbService(), $downloadService);
        $request = new FakeRequest(["post" => ["dlcounter" => "test.txt"], "admin" => true]);
        $sut($request, "test.txt");
    }

    public function testLogsDownload(): void
    {
        $dbService = $this->dbService();
        $dbService->expects($this->once())->method("log")->with(1234567, "test.txt")->willReturn(true);
        $sut = $this->sut($dbService, $this->downloadService());
        $request = new FakeRequest(["post" => ["dlcounter" => "test.txt"], "time" => 1234567]);
        $sut($request, "test.txt");
    }

    public function testReportsFailureToLogDownload(): void
    {
        $dbService = $this->dbService();
        $dbService->expects($this->once())->method("log")->willReturn(false);
        $sut = $this->sut($dbService, $this->downloadService());
        $request = new FakeRequest(["post" => ["dlcounter" => "test.txt"], "time" => 1234567]);
        $this->assertStringContainsString(
            "Can't write to file &quot;test.txt&quot;!",
            $sut($request, "test.txt")->output()
        );
    }

    public function testRespondsWith404ForUnreadableDownload(): void
    {
        $sut = $this->sut($this->dbService(), $this->downloadService(false));
        $request = new FakeRequest(["post" => ["dlcounter" => "test.txt"]]);
        $this->assertSame(404, $sut($request, "test.txt")->status());
    }

    /** @return MainController&MockObject */
    private function sut($dbService, $downloadService)
    {
        return $this->getMockBuilder(MainController::class)
            ->setConstructorArgs(["", $dbService, $downloadService, $this->view()])
            ->onlyMethods(["mimeType"])
            ->getMock();
    }

    /** @return DbService&MockObject */
    private function dbService()
    {
        $dbService = $this->createMock(DbService::class);
        $dbService->method("getDownloadCountOf")->willReturn(123);
        return $dbService;
    }

    /** @return DownloadService&MockObject */
    private function downloadService(bool $readable = true)
    {
        $downloadService = $this->createMock(DownloadService::class);
        $downloadService->method("isReadable")->willReturn($readable);
        $downloadService->method("fileSize")->willReturn(12345);
        return $downloadService;
    }

    private function view(): View
    {
        return new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["dlcounter"]);
    }
}
