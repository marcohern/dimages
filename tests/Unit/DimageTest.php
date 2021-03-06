<?php

namespace Marcohern\Dimages\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Marcohern\Dimages\Lib\Dimage;

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

  public function testXFile()
  {
    $this->assertNotEmpty(Dimage::xFile());
  }
}
