<?php

namespace Dlcounter;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\FakeRequest;
use Plib\FakeSystemChecker;
use Plib\View;

class InfoControllerTest extends TestCase
{
    public function testRendersInfo(): void
    {
        $sut = new InfoController(
            "./",
            new FakeSystemChecker(),
            new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["dlcounter"])
        );
        Approvals::verifyHtml($sut->defaultAction(new FakeRequest()));
    }
}
