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
  protected $disk = null;

  protected function setUp():void {
    parent::setUp();
    Storage::fake('dimages');
    $this->disk = Storage::disk('dimages');
    $this->sm = new StorageManager;
  }

  protected function tearDown():void {
    unset($this->sm);
    unset($this->disk);
    parent::tearDown();
  }

  public function test_url() {
    $dimage = new DimageFile('games','death-stranding',123,'jpeg','cover','hdpi');

    $this->assertEquals(
      '/storage/_global/games/death-stranding/123/cover/hdpi.jpeg',
      $this->sm->url($dimage)
    );
  }

  public function test_exists() {
    $this->disk->put('_global/games/death-stranding/004/boxart/hdpi.txt','HELLO');

    $dimage = new DimageFile('games','death-stranding',4,'txt','boxart','hdpi');

    $this->assertTrue($this->sm->exists($dimage));
  }

  public function test_content() {
    $this->disk->put('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt','HELLO DIMAGE');

    $dimage = new DimageFile('games','death-stranding',4,'txt','boxart','hdpi', 'marcohern@gmail.com');

    $this->assertEquals('HELLO DIMAGE',$this->sm->content($dimage));
  }

  public function test_deleteSingle() {
    $this->disk->put('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt','HELLO DIMAGE');

    $dimage = new DimageFile('games','death-stranding',4,'txt','boxart','hdpi', 'marcohern@gmail.com');

    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt');

    $this->sm->deleteSingle($dimage);

    $this->disk->assertMissing('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt');
  }

  public function test_deleteMultiple() {
    $this->disk->put('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/000/cover/mdpi.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/002/icon/ldpi.txt','HELLO DIMAGE');

    $dimage1 = DimageFile::fromFilePath('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt');
    $dimage2 = DimageFile::fromFilePath('marcohern@gmail.com/games/death-stranding/000/cover/mdpi.txt');
    $dimage3 = DimageFile::fromFilePath('marcohern@gmail.com/games/death-stranding/002/icon/ldpi.txt');

    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt');
    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/000/cover/mdpi.txt');
    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/002/icon/ldpi.txt');

    $this->sm->deleteMultiple([$dimage1, $dimage2, $dimage3]);

    $this->disk->assertMissing('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt');
    $this->disk->assertMissing('marcohern@gmail.com/games/death-stranding/000/cover/mdpi.txt');
    $this->disk->assertMissing('marcohern@gmail.com/games/death-stranding/002/icon/ldpi.txt');
  }

  public function test_deleteIdentity() {
    $this->disk->put('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/000/cover/mdpi.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/002/icon/ldpi.txt','HELLO DIMAGE');

    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt');
    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/000/cover/mdpi.txt');
    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/002/icon/ldpi.txt');

    $this->sm->deleteIdentity('marcohern@gmail.com','games','death-stranding');

    $this->disk->assertMissing('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt');
    $this->disk->assertMissing('marcohern@gmail.com/games/death-stranding/000/cover/mdpi.txt');
    $this->disk->assertMissing('marcohern@gmail.com/games/death-stranding/002/icon/ldpi.txt');
  }

  public function test_deleteStaging() {
    $this->disk->put('marcohern@gmail.com/_tmp/abcdefg/004.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/_tmp/abcdefg/000.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/_tmp/abcdefg/002.txt','HELLO DIMAGE');

    $this->disk->assertExists('marcohern@gmail.com/_tmp/abcdefg/004.txt');
    $this->disk->assertExists('marcohern@gmail.com/_tmp/abcdefg/000.txt');
    $this->disk->assertExists('marcohern@gmail.com/_tmp/abcdefg/002.txt');

    $this->sm->deleteStaging('marcohern@gmail.com','abcdefg');

    $this->disk->assertMissing('marcohern@gmail.com/_tmp/abcdefg/004.txt');
    $this->disk->assertMissing('marcohern@gmail.com/_tmp/abcdefg/000.txt');
    $this->disk->assertMissing('marcohern@gmail.com/_tmp/abcdefg/002.txt');
  }

  public function test_deleteStagingForTenants() {
    $this->disk->put('marcohern@gmail.com/_tmp/abcdefg/004.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/_tmp/abcdefg/002.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/_tmp/abcdefg/000.txt','HELLO DIMAGE');
    $this->disk->put('giovanni.castellanos/_tmp/tuvwxyz/000.txt','HELLO DIMAGE');
    $this->disk->put('giovanni.castellanos/_tmp/tuvwxyz/002.txt','HELLO DIMAGE');
    $this->disk->put('giovanni.castellanos/_tmp/tuvwxyz/003.txt','HELLO DIMAGE');
    $this->disk->put('marco/_tmp/hijklmn/000.txt','HELLO DIMAGE');
    $this->disk->put('marco/_tmp/hijklmn/001.txt','HELLO DIMAGE');
    $this->disk->put('marco/_tmp/hijklmn/002.txt','HELLO DIMAGE');

    $this->sm->deleteStagingForTenants(['marcohern@gmail.com','marco']);

    $this->disk->assertMissing('marcohern@gmail.com/_tmp/abcdefg/004.txt','HELLO DIMAGE');
    $this->disk->assertMissing('marcohern@gmail.com/_tmp/abcdefg/002.txt','HELLO DIMAGE');
    $this->disk->assertMissing('marcohern@gmail.com/_tmp/abcdefg/000.txt','HELLO DIMAGE');
    $this->disk->assertExists('giovanni.castellanos/_tmp/tuvwxyz/000.txt','HELLO DIMAGE');
    $this->disk->assertExists('giovanni.castellanos/_tmp/tuvwxyz/002.txt','HELLO DIMAGE');
    $this->disk->assertExists('giovanni.castellanos/_tmp/tuvwxyz/003.txt','HELLO DIMAGE');
    $this->disk->assertMissing('marco/_tmp/hijklmn/000.txt','HELLO DIMAGE');
    $this->disk->assertMissing('marco/_tmp/hijklmn/001.txt','HELLO DIMAGE');
    $this->disk->assertMissing('marco/_tmp/hijklmn/002.txt','HELLO DIMAGE');
  }

  public function test_tenants() {
    $this->disk->put('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt','HELLO DIMAGE');
    $this->disk->put('giovanni.castellanos/games/death-stranding/000/cover/mdpi.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/002/icon/ldpi.txt','HELLO DIMAGE');

    $this->assertSame(['giovanni.castellanos','marcohern@gmail.com'], $this->sm->tenants());
  }

  public function test_entities() {
    $this->disk->put('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/video-games/death-stranding/000/cover/mdpi.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/002/icon/ldpi.txt','HELLO DIMAGE');

    $this->assertSame(['games','video-games'], $this->sm->entities('marcohern@gmail.com'));
  }

  public function test_identities() {
    $this->disk->put('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/games/darksouls-3/000/cover/mdpi.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/002/icon/ldpi.txt','HELLO DIMAGE');

    $this->assertSame(['darksouls-3','death-stranding'], $this->sm->identities('marcohern@gmail.com','games'));
  }

  public function test_sources() {
    $this->disk->put('marcohern@gmail.com/games/death-stranding/004.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/000.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/002.txt','HELLO DIMAGE');

    $this->assertSame(
      [
        'marcohern@gmail.com/games/death-stranding/000.txt',
        'marcohern@gmail.com/games/death-stranding/002.txt',
        'marcohern@gmail.com/games/death-stranding/004.txt',
      ], $this->sm->sources('marcohern@gmail.com','games','death-stranding')
    );
  }

  public function test_profiles() {
    $this->disk->put('marcohern@gmail.com/games/death-stranding/002/boxart/hdpi.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/002/cover/mdpi.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/002/icon/ldpi.txt','HELLO DIMAGE');

    $this->assertSame(
      ['boxart','cover','icon',],
      $this->sm->profiles('marcohern@gmail.com','games','death-stranding',2)
    );
  }

  public function test_derivatives() {
    $this->disk->put('marcohern@gmail.com/games/death-stranding/002/boxart/hdpi.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/002/boxart/mdpi.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/002/boxart/ldpi.txt','HELLO DIMAGE');

    $this->assertSame(
      [
        'marcohern@gmail.com/games/death-stranding/002/boxart/hdpi.txt',
        'marcohern@gmail.com/games/death-stranding/002/boxart/ldpi.txt',        
        'marcohern@gmail.com/games/death-stranding/002/boxart/mdpi.txt',
      ],
      $this->sm->derivatives('marcohern@gmail.com','games','death-stranding',2,'boxart')
    );
  }

  public function test_move() {
    $this->disk->put('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt','HELLO DIMAGE');

    $source = DimageFile::fromFilePath('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt');
    $target = DimageFile::fromFilePath('giovanni.castellanos/games/death-stranding/000/cover/mdpi.txt');

    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt');
    $this->disk->assertMissing('giovanni.castellanos/games/death-stranding/000/cover/mdpi.txt');

    $this->sm->move($source, $target);

    $this->disk->assertMissing('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt');
    $this->disk->assertExists('giovanni.castellanos/games/death-stranding/000/cover/mdpi.txt');
  }

  public function test_attach() {
    $this->disk->put('marcohern@gmail.com/_tmp/abcdefg/002.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/_tmp/abcdefg/000.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/_tmp/abcdefg/004.txt','HELLO DIMAGE');

    $this->disk->assertMissing('marcohern@gmail.com/games/death-stranding/002.txt');
    $this->disk->assertMissing('marcohern@gmail.com/games/death-stranding/000.txt');
    $this->disk->assertMissing('marcohern@gmail.com/games/death-stranding/004.txt');

    $this->sm->attach('marcohern@gmail.com','abcdefg','games','death-stranding');

    $this->disk->assertMissing('marcohern@gmail.com/_tmp/abcdefg/002.txt');
    $this->disk->assertMissing('marcohern@gmail.com/_tmp/abcdefg/000.txt');
    $this->disk->assertMissing('marcohern@gmail.com/_tmp/abcdefg/004.txt');

    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/002.txt');
    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/000.txt');
    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/004.txt');
  }
}