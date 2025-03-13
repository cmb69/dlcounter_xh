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
        $sut = new MainController(
            $this->dbService(),
            $this->downloadService(),
            $this->view()
        );
        Approvals::verifyHtml($sut->defaultAction(new FakeRequest(), "test.txt")->output());
    }

    public function testReportsUnreadableDownload(): void
    {
        $sut = new MainController(
            $this->dbService(false),
            $this->downloadService(),
            $this->view()
        );
        $this->assertStringContainsString(
            "Can't read file &quot;test.txt&quot;!",
            $sut->defaultAction(new FakeRequest(), "test.txt")->output()
        );
    }

    public function testMakesDownloadAvailableForAdmin(): void
    {
        $downloadService = $this->downloadService();
        $downloadService->expects($this->once())->method("deliverDownload")->with("test.txt");
        $sut = new MainController(
            $this->dbService(),
            $downloadService,
            $this->view()
        );
        $sut->downloadAction(new FakeRequest(["admin" => true]), "test.txt");
    }

    public function testLogsDownload(): void
    {
        $dbService = $this->dbService();
        $dbService->expects($this->once())->method("log")->with(1234567, "test.txt")->willReturn(true);
        $sut = new MainController(
            $dbService,
            $this->downloadService(),
            $this->view()
        );
        $sut->downloadAction(new FakeRequest(["time" => 1234567]), "test.txt");
    }

    public function testReportsFailureToLogDownload(): void
    {
        $dbService = $this->dbService();
        $dbService->expects($this->once())->method("log")->willReturn(false);
        $sut = new MainController(
            $dbService,
            $this->downloadService(),
            $this->view()
        );
        $this->assertStringContainsString(
            "Can't write to file &quot;test.txt&quot;!",
            $sut->downloadAction(new FakeRequest(["time" => 1234567]), "test.txt")->output()
        );
    }

    /** @return DbService&MockObject */
    private function dbService(bool $readable = true)
    {
        $dbService = $this->createMock(DbService::class);
        $dbService->method("isReadable")->willReturn($readable);
        $dbService->method("fileSize")->willReturn(12345);
        $dbService->method("getDownloadCountOf")->willReturn(123);
        return $dbService;
    }

    /** @return DownloadService&MockObject */
    private function downloadService()
    {
        return $this->createMock(DownloadService::class);
    }

    private function view(): View
    {
        return new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["dlcounter"]);
    }
}
