<?php

namespace Dlcounter;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\FakeRequest;
use Plib\View;

class MainControllerTest extends TestCase
{
    public function testRendersDownloadForm(): void
    {
        $dbService = $this->createStub(DbService::class);
        $dbService->method("isReadable")->willReturn(true);
        $dbService->method("fileSize")->willReturn(12345);
        $dbService->method("getDownloadCountOf")->willReturn(123);
        $downloadService = $this->createStub(DownloadService::class);
        $sut = new MainController(
            $dbService,
            $downloadService,
            new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["dlcounter"]),
            XH_includeVar("./languages/en.php", "plugin_tx")["dlcounter"]
        );
        Approvals::verifyHtml($sut->defaultAction(new FakeRequest(), "test.txt"));
    }

    public function testReportsUnreadableDownload(): void
    {
        $dbService = $this->createStub(DbService::class);
        $dbService->method("isReadable")->willReturn(false);
        $dbService->method("fileSize")->willReturn(12345);
        $dbService->method("getDownloadCountOf")->willReturn(123);
        $downloadService = $this->createStub(DownloadService::class);
        $sut = new MainController(
            $dbService,
            $downloadService,
            new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["dlcounter"]),
            XH_includeVar("./languages/en.php", "plugin_tx")["dlcounter"]
        );
        $this->assertStringContainsString(
            "Can't read file &quot;test.txt&quot;!",
            $sut->defaultAction(new FakeRequest(), "test.txt")
        );
    }

    public function testMakesDownloadAvailableForAdmin(): void
    {
        $dbService = $this->createStub(DbService::class);
        $dbService->method("isReadable")->willReturn(true);
        $dbService->method("fileSize")->willReturn(12345);
        $dbService->method("getDownloadCountOf")->willReturn(123);
        $downloadService = $this->createMock(DownloadService::class);
        $downloadService->expects($this->once())->method("deliverDownload")->with("test.txt");
        $sut = new MainController(
            $dbService,
            $downloadService,
            new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["dlcounter"]),
            XH_includeVar("./languages/en.php", "plugin_tx")["dlcounter"]
        );
        $sut->downloadAction(new FakeRequest(["admin" => true]), "test.txt");
    }

    public function testLogsDownload(): void
    {
        $dbService = $this->createMock(DbService::class);
        $dbService->method("isReadable")->willReturn(true);
        $dbService->method("fileSize")->willReturn(12345);
        $dbService->method("getDownloadCountOf")->willReturn(123);
        $dbService->expects($this->once())->method("log")->with(1234567, "test.txt")->willReturn(true);
        $downloadService = $this->createMock(DownloadService::class);
        $sut = new MainController(
            $dbService,
            $downloadService,
            new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["dlcounter"]),
            XH_includeVar("./languages/en.php", "plugin_tx")["dlcounter"]
        );
        $sut->downloadAction(new FakeRequest(["time" => 1234567]), "test.txt");
    }

    public function testReportsFailureToLogDownload(): void
    {
        $dbService = $this->createMock(DbService::class);
        $dbService->method("isReadable")->willReturn(true);
        $dbService->method("fileSize")->willReturn(12345);
        $dbService->method("getDownloadCountOf")->willReturn(123);
        $dbService->expects($this->once())->method("log")->willReturn(false);
        $downloadService = $this->createMock(DownloadService::class);
        $sut = new MainController(
            $dbService,
            $downloadService,
            new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["dlcounter"]),
            XH_includeVar("./languages/en.php", "plugin_tx")["dlcounter"]
        );
        $this->assertStringContainsString(
            "Can't write to file &quot;test.txt&quot;!",
            $sut->downloadAction(new FakeRequest(["time" => 1234567]), "test.txt")
        );
    }
}
