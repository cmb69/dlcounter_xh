<?php

namespace Dlcounter;

use PHPUnit\Framework\TestCase;

class DicTest extends TestCase
{
    public function setUp(): void
    {
        global $pth, $plugin_tx;

        $pth = ["folder" => ["plugins" => ""]];
        $plugin_tx = ["dlcounter" => []];
    }

    public function testMakesMainController(): void
    {
        $this->assertInstanceOf(MainController::class, Dic::mainController());
    }

    public function testMakesInfoController(): void
    {
        $this->assertInstanceOf(InfoController::class, Dic::InfoController());
    }

    public function testMakesMainAdminController(): void
    {
        $this->assertInstanceOf(MainAdminController::class, Dic::mainAdminController());
    }
}
