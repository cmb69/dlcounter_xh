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
            new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["dlcounter"])
        );
        Approvals::verifyHtml($sut->defaultAction(new FakeRequest(), "test.txt"));
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
            new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["dlcounter"])
        );
        $sut->downloadAction(new FakeRequest(["admin" => true]), "test.txt");
    }

    public function testLogsDownload(): void
    {
        $dbService = $this->createMock(DbService::class);
        $dbService->method("isReadable")->willReturn(true);
        $dbService->method("fileSize")->willReturn(12345);
        $dbService->method("getDownloadCountOf")->willReturn(123);
        $dbService->expects($this->once())->method("log")->willReturn(true);
        $downloadService = $this->createMock(DownloadService::class);
        $sut = new MainController(
            $dbService,
            $downloadService,
            new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["dlcounter"])
        );
        $sut->downloadAction(new FakeRequest(["time" => 1234567]), "test.txt");
    }
}
