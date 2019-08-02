<?php

namespace Marcohern\Dimages\Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

use Marcohern\Dimages\Lib\Files\DimageFile;
use Marcohern\Dimages\Lib\Managers\StorageManager;

class StorageManagerTest extends TestCase {

  protected $sm = null;

  protected function setUp():void {
    parent::setUp();
    $this->sm = new StorageManager;
  }

  protected function tearDown():void {
    unset($this->sm);
    parent::tearDown();
  }

  public function test_url() {
    $dimage = new DimageFile('games','death-stranding',123,'jpeg','cover','hdpi');

    $this->assertEquals(
      $this->sm->url($dimage),
      'http://localhost:8000/dimages/_global/games/death-stranding/123/cover/hdpi.jpeg'
    );
  }

  public function test_exists() {
    Storage::fake('dimages');
    Storage::disk('dimages')->put('_global/games/death-stranding/004/boxart/hdpi.txt','HELLO');

    $dimage = new DimageFile('games','death-stranding',4,'txt','boxart','hdpi');

    $this->assertTrue($this->sm->exists($dimage));
  }
}