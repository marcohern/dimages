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
    Storage::fake('dimages');
    Storage::disk('dimages')->put('img/games/death-stranding/004.boxart.hdpi.txt','HELLO');
    $dimage = new DimageName;
    $dimage->entity = 'games';
    $dimage->identity = 'death-stranding';
    $dimage->index = 4;
    $dimage->profile = 'boxart';
    $dimage->density = 'hdpi';
    $dimage->ext = 'txt';

    Storage::disk('dimages')->assertExists('img/games/death-stranding/004.boxart.hdpi.txt');
    $dimages = new BaseDimageManager;
    $this->assertTrue($dimages->exists($dimage));
  }

  public function test_content() {
    Storage::fake('dimages');
    Storage::disk('dimages')->put('img/games/death-stranding/004.boxart.hdpi.txt','HELLO WORLD!');
    $dimage = new DimageName;
    $dimage->entity = 'games';
    $dimage->identity = 'death-stranding';
    $dimage->index = 4;
    $dimage->profile = 'boxart';
    $dimage->density = 'hdpi';
    $dimage->ext = 'txt';

    Storage::disk('dimages')->assertExists('img/games/death-stranding/004.boxart.hdpi.txt');
    $dimages = new BaseDimageManager;
    $this->assertEquals($dimages->content($dimage), 'HELLO WORLD!');
  }

  public function test_deleteSingle() {
    Storage::fake('dimages');
    Storage::disk('dimages')->put('img/games/death-stranding/004.boxart.hdpi.txt','HELLO WORLD!');
    $dimage = new DimageName;
    $dimage->entity = 'games';
    $dimage->identity = 'death-stranding';
    $dimage->index = 4;
    $dimage->profile = 'boxart';
    $dimage->density = 'hdpi';
    $dimage->ext = 'txt';

    Storage::disk('dimages')->assertExists('img/games/death-stranding/004.boxart.hdpi.txt');
    $dimages = new BaseDimageManager;
    $dimages->deleteSingle($dimage);
    Storage::disk('dimages')->assertMissing('img/games/death-stranding/004.boxart.hdpi.txt');
  }

  public function test_deleteIdentity() {
    Storage::fake('dimages');
    $disk = Storage::disk('dimages');
    $disk->put('img/text/file/000.txt','HELLO WORLD!');
    $disk->put('img/text/file/001.txt','HELLO WORLD!');
    $disk->put('img/text/file/002.txt','HELLO WORLD!');
    $disk->put('img/text/file/003.txt','HELLO WORLD!');

    $disk->assertExists('img/text/file');
    $dimages = new BaseDimageManager;
    $dimages->deleteIdentity('text','file');
    $disk->assertMissing('img/text/file');
  }

  public function test_entityFolders() {
    Storage::fake('dimages');
    $disk = Storage::disk('dimages');
    $disk->put('img/games/death-stranding/000.txt','HELLO WORLD!');
    $disk->put('img/games/dead-space/000.txt','HELLO WORLD!');
    $disk->put('img/pets/mr-snuggles/000.txt','HELLO WORLD!');
    $disk->put('img/pets/milo/000.txt','HELLO WORLD!');
    $disk->put('img/bars/bbc-andino/000.txt','HELLO WORLD!');
    $disk->put('img/bars/mdf-calle-10/000.txt','HELLO WORLD!');

    $dimages = new BaseDimageManager;
    $this->assertEquals($dimages->entityFolders(), [
      'img/bars', 'img/games', 'img/pets'
    ]);
  }

  public function test_identityFolders() {
    Storage::fake('dimages');
    $disk = Storage::disk('dimages');
    $disk->put('img/games/death-stranding/000.txt','HELLO WORLD!');
    $disk->put('img/games/dead-space/000.txt','HELLO WORLD!');
    $disk->put('img/pets/mr-snuggles/000.txt','HELLO WORLD!');
    $disk->put('img/pets/milo/000.txt','HELLO WORLD!');
    $disk->put('img/bars/bbc-andino/000.txt','HELLO WORLD!');
    $disk->put('img/bars/mdf-calle-10/000.txt','HELLO WORLD!');

    $dimages = new BaseDimageManager;
    $this->assertEquals($dimages->identityFolders('pets'), [
      'img/pets/milo',
      'img/pets/mr-snuggles',
    ]);
  }

  public function test_imageFiles() {
    Storage::fake('dimages');
    $disk = Storage::disk('dimages');
    $disk->put('img/games/death-stranding/000.txt','HELLO WORLD!');
    $disk->put('img/games/death-stranding/001.txt','HELLO WORLD!');
    $disk->put('img/games/death-stranding/002.txt','HELLO WORLD!');
    $disk->put('img/games/dead-space/000.txt','HELLO WORLD!');
    $disk->put('img/pets/mr-snuggles/000.txt','HELLO WORLD!');
    $disk->put('img/pets/milo/000.txt','HELLO WORLD!');
    $disk->put('img/bars/bbc-andino/000.txt','HELLO WORLD!');
    $disk->put('img/bars/mdf-calle-10/000.txt','HELLO WORLD!');

    $dimages = new BaseDimageManager;
    $this->assertEquals($dimages->imageFiles('games','death-stranding'), [
      'img/games/death-stranding/000.txt',
      'img/games/death-stranding/001.txt',
      'img/games/death-stranding/002.txt'
    ]);
  }
  
  public function test_dimages() {
    Storage::fake('dimages');
    $disk = Storage::disk('dimages');
    $disk->put('img/games/death-stranding/000.txt','HELLO WORLD!');
    $disk->put('img/games/death-stranding/001.txt','HELLO WORLD!');
    $disk->put('img/games/death-stranding/002.txt','HELLO WORLD!');
    $disk->put('img/games/dead-space/000.txt','HELLO WORLD!');
    $disk->put('img/pets/mr-snuggles/000.txt','HELLO WORLD!');
    $disk->put('img/pets/milo/000.txt','HELLO WORLD!');
    $disk->put('img/bars/bbc-andino/000.txt','HELLO WORLD!');
    $disk->put('img/bars/mdf-calle-10/000.txt','HELLO WORLD!');

    $dimages = new BaseDimageManager;
    $this->assertEquals($dimages->dimages('games','death-stranding'), [
      DimageName::fromFilePath('img/games/death-stranding/000.txt'),
      DimageName::fromFilePath('img/games/death-stranding/001.txt'),
      DimageName::fromFilePath('img/games/death-stranding/002.txt'),
    ]);
  }
}
