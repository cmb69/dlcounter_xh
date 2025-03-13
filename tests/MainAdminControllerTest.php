<?php

namespace Dlcounter;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\FakeRequest;
use Plib\Jquery;
use Plib\View;

class MainAdminControllerTest extends TestCase
{
    public function testRendersStats(): void
    {
        $dbService = $this->createStub(DbService::class);
        $dbService->method("readDb")->willReturn($this->downloads());
        $sut = new MainAdminController(
            "../plugins/",
            $dbService,
            $this->createStub(Jquery::class),
            $this->view()
        );
        Approvals::verifyHtml($sut->defaultAction(new FakeRequest()));
    }

    private function downloads(): array
    {
        return [
            (object) ["name" => "foo.txt", "time" => 12345678],
            (object) ["name" => "bar.txt", "time" => 23456789],
        ];
    }

    private function view(): View
    {
        return new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["dlcounter"]);
    }
}
