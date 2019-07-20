<?php

namespace Marcohern\Dimages\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Marcohern\Dimages\Lib\Dimage;
use Marcohern\Dimages\Lib\DimageName;
use Marcohern\Dimages\Lib\DimageManager;

class DimageManagerTest extends TestCase {
  protected $dimages;

  protected function setUp() : void {
    Dimage::boot();
    $this->dimages = new DimageManager;
    parent::setUp();
  }

  protected function tearDown() : void {
    parent::tearDown();
    unset($this->dimages);
    Dimage::shutdown();
  }

  public function test_url() {
    $dimage = DimageName::fromFilePath('music/sure-know-something-by-kiss/002.jpg');

    $this->assertSame($this->dimages->url($dimage), env('APP_URL').'/dimages/music/sure-know-something-by-kiss/002.jpg');
  }
}