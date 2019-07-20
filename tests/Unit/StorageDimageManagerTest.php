<?php

namespace Marcohern\Dimages\Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

use Marcohern\Dimages\Lib\DimageManager;
use Marcohern\Dimages\Lib\DimageName;
use Marcohern\Dimages\Lib\Dimage;

class StorageDimageManagerTest extends TestCase {
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

    $dimages = new DimageManager;
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
    $dimages = new DimageManager;
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
    $dimages = new DimageManager;
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
    $dimages = new DimageManager;
    $dimages->deleteSingle($dimage);
    Storage::disk('dimages')->assertMissing('img/games/death-stranding/004.boxart.hdpi.txt');
  }

  public function test_deleteSingle_source_valid() {
    Storage::fake('dimages');
    Storage::disk('dimages')->put('img/games/death-stranding/004.txt','HELLO WORLD!');
    $dimage = new DimageName;
    $dimage->entity = 'games';
    $dimage->identity = 'death-stranding';
    $dimage->index = 4;
    $dimage->ext = 'txt';

    Storage::disk('dimages')->assertExists('img/games/death-stranding/004.txt');
    $dimages = new DimageManager;
    $dimages->deleteSingle($dimage);
    Storage::disk('dimages')->assertMissing('img/games/death-stranding/004.txt');
  }

  public function test_deleteMultiple() {
    Storage::fake('dimages');
    Storage::disk('dimages')->put('img/games/death-stranding/000.txt','HELLO WORLD!');
    Storage::disk('dimages')->put('img/games/death-stranding/000.cover.mdpi.txt','HELLO WORLD!');
    Storage::disk('dimages')->put('img/games/death-stranding/000.cover.ldpi.txt','HELLO WORLD!');
    Storage::disk('dimages')->put('img/games/death-stranding/000.cover.hdpi.txt','HELLO WORLD!');

    $dimage0 = DimageName::fromFilePath('games/death-stranding/000.txt');
    $dimage1 = DimageName::fromFilePath('games/death-stranding/000.cover.mdpi.txt');
    $dimage2 = DimageName::fromFilePath('games/death-stranding/000.cover.ldpi.txt');
    $dimage3 = DimageName::fromFilePath('games/death-stranding/000.cover.hdpi.txt');

    Storage::disk('dimages')->assertExists('img/games/death-stranding/000.txt');
    Storage::disk('dimages')->assertExists('img/games/death-stranding/000.cover.mdpi.txt');
    Storage::disk('dimages')->assertExists('img/games/death-stranding/000.cover.ldpi.txt');
    Storage::disk('dimages')->assertExists('img/games/death-stranding/000.cover.hdpi.txt');

    $dimages = new DimageManager;
    $dimages->deleteMultiple([ $dimage0, $dimage1, $dimage2, $dimage3 ]);

    Storage::disk('dimages')->assertMissing('img/games/death-stranding/000.txt');
    Storage::disk('dimages')->assertMissing('img/games/death-stranding/000.cover.mdpi.txt');
    Storage::disk('dimages')->assertMissing('img/games/death-stranding/000.cover.ldpi.txt');
    Storage::disk('dimages')->assertMissing('img/games/death-stranding/000.cover.hdpi.txt');
  }

  public function test_deleteIdentity() {
    Storage::fake('dimages');
    $disk = Storage::disk('dimages');
    $disk->put('img/text/file/000.txt','HELLO WORLD!');
    $disk->put('img/text/file/001.txt','HELLO WORLD!');
    $disk->put('img/text/file/002.txt','HELLO WORLD!');
    $disk->put('img/text/file/003.txt','HELLO WORLD!');

    $disk->assertExists('img/text/file');
    $dimages = new DimageManager;
    $dimages->deleteIdentity('text','file');
    $disk->assertMissing('img/text/file');
  }

  public function test_entities() {
    Storage::fake('dimages');
    $disk = Storage::disk('dimages');
    $disk->put('img/games/death-stranding/000.txt','HELLO WORLD!');
    $disk->put('img/games/dead-space/000.txt','HELLO WORLD!');
    $disk->put('img/pets/mr-snuggles/000.txt','HELLO WORLD!');
    $disk->put('img/pets/milo/000.txt','HELLO WORLD!');
    $disk->put('img/bars/bbc-andino/000.txt','HELLO WORLD!');
    $disk->put('img/bars/mdf-calle-10/000.txt','HELLO WORLD!');

    $dimages = new DimageManager;
    $this->assertEquals($dimages->entities(), [
      'img/bars', 'img/games', 'img/pets'
    ]);
  }

  public function test_identities() {
    Storage::fake('dimages');
    $disk = Storage::disk('dimages');
    $disk->put('img/games/death-stranding/000.txt','HELLO WORLD!');
    $disk->put('img/games/dead-space/000.txt','HELLO WORLD!');
    $disk->put('img/pets/mr-snuggles/000.txt','HELLO WORLD!');
    $disk->put('img/pets/milo/000.txt','HELLO WORLD!');
    $disk->put('img/bars/bbc-andino/000.txt','HELLO WORLD!');
    $disk->put('img/bars/mdf-calle-10/000.txt','HELLO WORLD!');

    $dimages = new DimageManager;
    $this->assertEquals($dimages->identities('pets'), [
      'img/pets/milo',
      'img/pets/mr-snuggles',
    ]);
  }

  public function test_files() {
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

    $dimages = new DimageManager;
    $this->assertEquals($dimages->files('games','death-stranding'), [
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

    $dimages = new DimageManager;
    $this->assertEquals($dimages->dimages('games','death-stranding'), [
      DimageName::fromFilePath('img/games/death-stranding/000.txt'),
      DimageName::fromFilePath('img/games/death-stranding/001.txt'),
      DimageName::fromFilePath('img/games/death-stranding/002.txt'),
    ]);
  }

  public function test_store() {
    Storage::fake('dimages');
    $disk = Storage::disk('dimages');

    $upload = UploadedFile::fake()->image('test3.png' , 1920, 1080);
    $dimage = DimageName::fromFilePath('games/darksouls-3/000.cover.mdpi.png');
    
    $disk->assertMissing('img/games/darksouls-3/000.cover.mdpi.png');
    $dimages = new DimageManager;
    $dimages->store($dimage, $upload);
    $disk->assertExists('img/games/darksouls-3/000.cover.mdpi.png');
  }

  public function test_move() {
    Storage::fake('dimages');
    $disk = Storage::disk('dimages');
    $disk->put('img/games/death-stranding/001.txt','THIS IS THE SOURCE!');

    $disk->assertExists('img/games/death-stranding/001.txt');
    $disk->assertMissing('img/games/death-stranding/004.txt');

    $dimages = new DimageManager;
    $dsource = DimageName::fromFilePath('img/games/death-stranding/001.txt');
    $dtarget = DimageName::fromFilePath('img/games/death-stranding/004.txt');
    $dimages->move($dsource, $dtarget);

    $disk->assertMissing('img/games/death-stranding/001.txt');
    $disk->assertExists('img/games/death-stranding/004.txt');
  }
}
