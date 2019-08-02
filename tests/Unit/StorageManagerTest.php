<?php

namespace Marcohern\Dimages\Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

use Marcohern\Dimages\Lib\DimageName;
use Marcohern\Dimages\Lib\Managers\StorageManager;

class StorageManagerTest extends TestCase {

  protected $sm = null;

  protected function setUp():void {
    $this->sm = new StorageManager;
    parent::setUp();
  }

  protected function tearDown():void {
    unset($this->sm);
    parent::tearDown();
  }

  public function test_url() {
    $dimage = new DimageName;
    $dimage->entity = 'games';
    $dimage->identity = 'death-stranding';
    $dimage->index = 123;
    $dimage->profile = 'cover';
    $dimage->density = 'hdpi';
    $dimage->ext = 'jpeg';

    $this->assertEquals(
      $this->sm->url($dimage),
      'http://localhost:8000/dimages/_global/games/death-stranding/123/cover/hdpi.jpeg'
    );
  }

  public function test_exists() {
    //Storage::fake('dimages');
    Storage::disk('dimages')->put('_global/games/death-stranding/004/boxart/hdpi.txt','HELLO');

    Storage::disk('dimages')->assertExists('_global/games/death-stranding/004/boxart/hdpi.txt');

    $dimage = new DimageName;
    $dimage->entity = 'games';
    $dimage->identity = 'death-stranding';
    $dimage->index = 4;
    $dimage->profile = 'boxart';
    $dimage->density = 'hdpi';
    $dimage->ext = 'txt';
  }
}