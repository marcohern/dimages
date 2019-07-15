<?php

namespace Marcohern\Dimages\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

use Marcohern\Dimages\Lib\DimageConstants;

class DimageMetaControllerTest extends TestCase
{
  protected function setUp() : void {
    parent::setUp();
  }

  protected function tearDown() : void {
    parent::tearDown();
    unset($this->route);
  }
    /**
     * Test dimage status
     *
     * @return void
     */
    public function test_status()
    {
      Storage::fake('dimages');
      $response = $this->get("mh/dim/api/_status");

      $response->assertStatus(200)->assertExactJson([
        'success' => true
      ]);
    }
}
