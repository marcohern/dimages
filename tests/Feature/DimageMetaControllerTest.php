<?php

namespace Marcohern\Dimages\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

use Marcohern\Dimages\Lib\DimageConstants;

class DimageMetaControllerTest extends TestCase
{
  protected $route;

  protected function setUp() : void {
    $this->route = DimageConstants::DIMROUTE;
    //Storage::fake('dimages');
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
        $response = $this->get("{$this->route}/status");

        $response->assertStatus(200)->assertExactJson([
          'success' => true
        ]);
    }
}
