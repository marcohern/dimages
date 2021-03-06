<?php

namespace Marcohern\Dimages\Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

use Marcohern\Dimages\Lib\DimageFile;
use Marcohern\Dimages\Lib\Managers\StorageManager;
use Marcohern\Dimages\Lib\Fs;
use Marcohern\Dimages\Lib\Factory;
class StorageManagerTest extends TestCase {

  protected $sm = null;
  protected $disk = null;
  protected $fs = null;
  protected $factory = null;

  protected function setUp():void {
    parent::setUp();
    Storage::fake('dimages');
    $this->disk = Storage::disk('dimages');
    $this->fs = new Fs;
    $this->factory = new Factory($this->fs);
    $this->sm = new StorageManager($this->factory, $this->fs);
  }

  protected function tearDown():void {
    unset($this->sm);
    unset($this->factory);
    unset($this->fs);
    unset($this->disk);
    parent::tearDown();
  }

  public function test_url() {
    $dimage = $this->factory->dimageFile('death-stranding','jpeg',123,'games','cover','hdpi');

    $this->assertEquals(
      '/storage/_anyone/games/death-stranding/123/cover/hdpi.jpeg',
      $this->sm->url($dimage)
    );
  }

  public function test_exists() {
    $this->disk->put('_anyone/games/death-stranding/004/boxart/hdpi.txt','HELLO');

    $dimage = $this->factory->dimageFile('death-stranding','txt',4,'games','boxart','hdpi');

    $this->assertTrue($this->sm->exists($dimage));
  }

  public function test_content() {
    $this->disk->put('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt','HELLO DIMAGE');

    $dimage = $this->factory->dimageFile('death-stranding','txt',4,'games','boxart','hdpi', 'marcohern@gmail.com');

    $this->assertEquals('HELLO DIMAGE',$this->sm->content($dimage));
  }

  public function test_destroy() {
    $this->disk->put('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt','HELLO DIMAGE');
    $dimage = $this->factory->dimageFile('death-stranding','txt',4,'games','boxart','hdpi', 'marcohern@gmail.com');
    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt');
    $this->sm->destroy($dimage);
    $this->disk->assertMissing('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt');
  }

  public function test_put() {
    $dimage = $this->factory->dimageFile('death-stranding','txt',4,'games','boxart','hdpi', 'marcohern@gmail.com');
    $content = 'HELLO CONTENT!';
    $this->sm->put($dimage,$content);
    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt');
    $this->assertSame($this->disk->get('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt'), 'HELLO CONTENT!');
  }

  public function test_deleteIndex() {
    $this->disk->put('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/004.txt','HELLO DIMAGE');
    
    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt');
    $this->sm->deleteIndex('marcohern@gmail.com','games','death-stranding',4);
    $this->disk->assertMissing('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt');
  }

  public function test_deleteMultiple() {
    $this->disk->put('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/000/cover/mdpi.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/002/icon/ldpi.txt','HELLO DIMAGE');

    $dimage1 = $this->factory->dimageFileFromPath('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt');
    $dimage2 = $this->factory->dimageFileFromPath('marcohern@gmail.com/games/death-stranding/000/cover/mdpi.txt');
    $dimage3 = $this->factory->dimageFileFromPath('marcohern@gmail.com/games/death-stranding/002/icon/ldpi.txt');

    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt');
    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/000/cover/mdpi.txt');
    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/002/icon/ldpi.txt');

    $this->sm->deleteMultiple([$dimage1, $dimage2, $dimage3]);

    $this->disk->assertMissing('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt');
    $this->disk->assertMissing('marcohern@gmail.com/games/death-stranding/000/cover/mdpi.txt');
    $this->disk->assertMissing('marcohern@gmail.com/games/death-stranding/002/icon/ldpi.txt');
  }

  public function test_move() {
    $this->disk->put('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt','HELLO DIMAGE');

    $source = $this->factory->dimageFileFromPath('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt');
    $target = $this->factory->dimageFileFromPath('giovanni.castellanos/games/death-stranding/000/cover/mdpi.txt');

    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt');
    $this->disk->assertMissing('giovanni.castellanos/games/death-stranding/000/cover/mdpi.txt');

    $this->sm->move($source, $target);

    $this->disk->assertMissing('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt');
    $this->disk->assertExists('giovanni.castellanos/games/death-stranding/000/cover/mdpi.txt');
  }

  public function test_attach() {
    $this->disk->put('marcohern@gmail.com/_staging/abcdefg/002.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/_staging/abcdefg/000.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/_staging/abcdefg/004.txt','HELLO DIMAGE');

    $this->disk->assertMissing('marcohern@gmail.com/games/death-stranding/002.txt');
    $this->disk->assertMissing('marcohern@gmail.com/games/death-stranding/000.txt');
    $this->disk->assertMissing('marcohern@gmail.com/games/death-stranding/004.txt');

    $this->sm->attach('marcohern@gmail.com','abcdefg','games','death-stranding');

    $this->disk->assertMissing('marcohern@gmail.com/_staging/abcdefg/002.txt');
    $this->disk->assertMissing('marcohern@gmail.com/_staging/abcdefg/000.txt');
    $this->disk->assertMissing('marcohern@gmail.com/_staging/abcdefg/004.txt');

    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/002.txt');
    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/000.txt');
    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/004.txt');
  }

  public function test_store() {
    $dimage = $this->factory->dimageFileFromPath('giovanni.castellanos/games/death-stranding/000.jpg');
    $upload = UploadedFile::fake()->image('test1.jpg');

    $this->sm->store($dimage, $upload);
    $this->disk->assertExists('giovanni.castellanos/games/death-stranding/000.jpg');
  }

  public function test_storeIdentity() {
    $upload1 = UploadedFile::fake()->image('test1.jpg');
    $upload2 = UploadedFile::fake()->image('test1.jpeg');
    $upload3 = UploadedFile::fake()->image('test1.png');
    $this->sm->storeIdentity('giovanni.castellanos','games','death-stranding', $upload1);
    $this->sm->storeIdentity('giovanni.castellanos','games','death-stranding', $upload2);
    $this->sm->storeIdentity('giovanni.castellanos','games','death-stranding', $upload3);

    $this->disk->assertExists('giovanni.castellanos/games/death-stranding/000.jpg');
    $this->disk->assertExists('giovanni.castellanos/games/death-stranding/001.jpeg');
    $this->disk->assertExists('giovanni.castellanos/games/death-stranding/002.png');
  }

  public function test_updateIdentity() {
    $upload1 = UploadedFile::fake()->image('test1.jpg');
    $upload2 = UploadedFile::fake()->image('test2.png');

    $this->disk->putFileAs('giovanni.castellanos/games/death-stranding',$upload1, '001.jpg');
    $this->sm->updateIdentity('giovanni.castellanos','games','death-stranding', 1, $upload2);

    $this->disk->assertMissing('giovanni.castellanos/games/death-stranding/001.jpg');
    $this->disk->assertExists ('giovanni.castellanos/games/death-stranding/001.png');
  }

  public function test_stageIdentity() {
    $upload1 = UploadedFile::fake()->image('test1.jpg');
    $upload2 = UploadedFile::fake()->image('test2.jpeg');
    $upload3 = UploadedFile::fake()->image('test3.png');

    $this->sm->stageIdentity('marco','abcdefg', $upload1);
    $this->sm->stageIdentity('marco','abcdefg', $upload2);
    $this->sm->stageIdentity('marco','abcdefg', $upload3);

    $this->disk->assertMissing('marco/_staging/abcdef/000.jpg');
    $this->disk->assertMissing('marco/_staging/abcdef/001.jpeg');
    $this->disk->assertMissing('marco/_staging/abcdef/002.png');
  }

  public function test_deleteStagingIndex() {
    $this->disk->put('marco/_staging/123456789/004/boxart/hdpi.txt','HELLO DIMAGE');
    $this->disk->put('marco/_staging/123456789/000/cover/mdpi.txt','HELLO DIMAGE');
    $this->disk->put('marco/_staging/123456789/002/icon/ldpi.txt','HELLO DIMAGE');

    $this->disk->assertExists('marco/_staging/123456789/004/boxart/hdpi.txt');
    $this->disk->assertExists('marco/_staging/123456789/000/cover/mdpi.txt');
    $this->disk->assertExists('marco/_staging/123456789/002/icon/ldpi.txt');

    $this->sm->deleteStagingIndex('marco','123456789', 4);

    $this->disk->assertMissing('marco/_staging/123456789/004/boxart/hdpi.txt');
    $this->disk->assertExists('marco/_staging/123456789/000/cover/mdpi.txt');
    $this->disk->assertExists('marco/_staging/123456789/002/icon/ldpi.txt');
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
    $this->disk->put('marcohern@gmail.com/_staging/abcdefg/004.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/_staging/abcdefg/000.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/_staging/abcdefg/002.txt','HELLO DIMAGE');

    $this->disk->assertExists('marcohern@gmail.com/_staging/abcdefg/004.txt');
    $this->disk->assertExists('marcohern@gmail.com/_staging/abcdefg/000.txt');
    $this->disk->assertExists('marcohern@gmail.com/_staging/abcdefg/002.txt');

    $this->sm->deleteStaging('marcohern@gmail.com','abcdefg');

    $this->disk->assertMissing('marcohern@gmail.com/_staging/abcdefg/004.txt');
    $this->disk->assertMissing('marcohern@gmail.com/_staging/abcdefg/000.txt');
    $this->disk->assertMissing('marcohern@gmail.com/_staging/abcdefg/002.txt');
  }

  public function test_deleteStagingForTenants() {
    $this->disk->put('marcohern@gmail.com/_staging/abcdefg/004.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/_staging/abcdefg/002.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/_staging/abcdefg/000.txt','HELLO DIMAGE');
    $this->disk->put('giovanni.castellanos/_staging/tuvwxyz/000.txt','HELLO DIMAGE');
    $this->disk->put('giovanni.castellanos/_staging/tuvwxyz/002.txt','HELLO DIMAGE');
    $this->disk->put('giovanni.castellanos/_staging/tuvwxyz/003.txt','HELLO DIMAGE');
    $this->disk->put('marco/_staging/hijklmn/000.txt','HELLO DIMAGE');
    $this->disk->put('marco/_staging/hijklmn/001.txt','HELLO DIMAGE');
    $this->disk->put('marco/_staging/hijklmn/002.txt','HELLO DIMAGE');

    $this->sm->deleteStagingForTenants(['marcohern@gmail.com','marco']);

    $this->disk->assertMissing('marcohern@gmail.com/_staging/abcdefg/004.txt','HELLO DIMAGE');
    $this->disk->assertMissing('marcohern@gmail.com/_staging/abcdefg/002.txt','HELLO DIMAGE');
    $this->disk->assertMissing('marcohern@gmail.com/_staging/abcdefg/000.txt','HELLO DIMAGE');
    $this->disk->assertExists('giovanni.castellanos/_staging/tuvwxyz/000.txt','HELLO DIMAGE');
    $this->disk->assertExists('giovanni.castellanos/_staging/tuvwxyz/002.txt','HELLO DIMAGE');
    $this->disk->assertExists('giovanni.castellanos/_staging/tuvwxyz/003.txt','HELLO DIMAGE');
    $this->disk->assertMissing('marco/_staging/hijklmn/000.txt','HELLO DIMAGE');
    $this->disk->assertMissing('marco/_staging/hijklmn/001.txt','HELLO DIMAGE');
    $this->disk->assertMissing('marco/_staging/hijklmn/002.txt','HELLO DIMAGE');
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

  public function test_indexes() {
    $this->disk->put('marcohern@gmail.com/games/death-stranding/004/boxart/hdpi.txt','HELLO DIMAGE');
    $this->disk->put('giovanni.castellanos/games/death-stranding/000/cover/mdpi.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/002/icon/ldpi.txt','HELLO DIMAGE');

    $this->assertSame(
      [
        'marcohern@gmail.com/games/death-stranding/002',
        'marcohern@gmail.com/games/death-stranding/004',
      ], $this->sm->indexes('marcohern@gmail.com','games','death-stranding')
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

  public function test_switchIndex() {
    $this->disk->put('marcohern@gmail.com/games/death-stranding/001.txt','HELLO ONE!');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/003.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/003/cover/hdpi.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/003/boxart/mdpi.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/003/icon/xxhdpi.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/005.txt','HELLO FIVE!');

    $this->sm->switchIndex('marcohern@gmail.com','games','death-stranding',3, 0);

    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/000.txt');
    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/000/cover/hdpi.txt');
    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/000/boxart/mdpi.txt');
    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/000/icon/xxhdpi.txt');

    $this->sm->switchIndex('marcohern@gmail.com','games','death-stranding',1, 5);
    $this->assertEquals('HELLO FIVE!',$this->disk->get('marcohern@gmail.com/games/death-stranding/001.txt'));
    $this->assertEquals('HELLO ONE!',$this->disk->get('marcohern@gmail.com/games/death-stranding/005.txt'));
  }

  public function test_normalize() {
    $this->disk->put('marcohern@gmail.com/games/death-stranding/001.txt','HELLO ONE!');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/003.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/003/cover/hdpi.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/003/boxart/mdpi.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/003/icon/xxhdpi.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/005.txt','HELLO FIVE!');

    $this->sm->normalize('marcohern@gmail.com','games','death-stranding');

    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/000.txt');
    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/001.txt');
    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/001/cover/hdpi.txt');
    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/001/boxart/mdpi.txt');
    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/001/icon/xxhdpi.txt');
    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/002.txt');
  }
}