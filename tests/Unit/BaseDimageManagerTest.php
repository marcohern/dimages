<?php

namespace Marcohern\Dimages\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

use Marcohern\Dimages\Lib\Managers\BaseDimageManager;
use Marcohern\Dimages\Lib\DimageName;
use Marcohern\Dimages\Lib\Dimage;

class BaseDimageManagerTest extends TestCase {
  protected $dimages;

  protected function setUp() : void {
    Dimage::boot();
    parent::setUp();
  }

  protected function tearDown() : void {
    parent::tearDown();
    Dimage::shutdown();
  }

  public function test_url() {
    Storage::fake('dimages');
    $dimage = new DimageName;
    $dimage->entity = 'games';
    $dimage->identity = 'death-stranding';
    $dimage->index = 3;
    $dimage->profile = 'boxart';
    $dimage->density = 'hdpi';
    $dimage->ext = 'jpeg';

    $dimages = new BaseDimageManager;
    $this->assertEquals($dimages->url($dimage),'/storage/games/death-stranding/003.boxart.hdpi.jpeg');
  }

  public function test_exists() {
    Storage::fake('dimages')->put('HELLO','img/games/death-stranding/003.boxart.hdpi.jpeg');
    $dimage = new DimageName;
    $dimage->entity = 'games';
    $dimage->identity = 'death-stranding';
    $dimage->index = 3;
    $dimage->profile = 'boxart';
    $dimage->density = 'hdpi';
    $dimage->ext = 'jpeg';

    Storage::disk('dimages')->assertExists('img/games/death-stranding/003.boxart.hdpi.jpeg');
  }
}
