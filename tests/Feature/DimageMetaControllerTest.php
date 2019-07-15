<?php

namespace Marcohern\Dimages\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Marcohern\Dimages\Lib\Dimages\DimageConstants;

class DimageMetaControllerTest extends TestCase
{
  protected $route;
  protected $disk;

  protected function setUp() : void {
    $this->route = DimageConstants::DIMROUTE;
    $this->disk = Storage::fake('dimages');
    parent::setUp();
  }

  protected function tearDown() : void {
    parent::tearDown();
    unset($this->route);
    unset($this->disk);
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
