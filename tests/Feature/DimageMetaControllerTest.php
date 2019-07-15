<?php

namespace Marcohern\Dimages\Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Storage;

use Marcohern\Dimages\Lib\DimageConstants;

class DimageMetaControllerTest extends TestCase
{
  protected function setUp() : void {
    parent::setUp();
  }

  protected function tearDown() : void {
    parent::tearDown();
  }

  public function testExample() {
    $this->assertTrue(true);
  }
}
