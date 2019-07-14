<?php

namespace Marcohern\Dimages\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Marcohern\Dimages\Lib\Dimages\Dimage;

class DimageTest extends TestCase
{
  protected function setUp():void {
    parent::setUp();
    Dimage::boot();
  }

  protected function tearDown():void {
    Dimage::shutdown();
    parent::tearDown();
  }

  public function testXFileName()
  {
    $this->assertNotEmpty(Dimage::xFileName());
  }
}
