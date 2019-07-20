<?php

namespace Marcohern\Dimages\Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

use Marcohern\Dimages\Exceptions\DimageNotFoundException;
use Marcohern\Dimages\Lib\DimageManager;
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

  protected function setUpFiles() {
    $disk = Storage::disk('dimages');
    $disk->put('img/games/death-stranding/000.txt','HELLO WORLD!');
    $disk->put('img/games/death-stranding/000.icon.ldpi.txt','HELLO WORLD!');
    $disk->put('img/games/death-stranding/000.icon.mdpi.txt','HELLO WORLD!');
    $disk->put('img/games/death-stranding/000.icon.hdpi.txt','HELLO WORLD!');
    $disk->put('img/games/death-stranding/000.cover.ldpi.txt','HELLO WORLD!');
    $disk->put('img/games/death-stranding/000.cover.mdpi.txt','HELLO WORLD!');
    $disk->put('img/games/death-stranding/000.cover.hdpi.txt','HELLO WORLD!');
    $disk->put('img/games/death-stranding/000.topbar.ldpi.txt','HELLO WORLD!');
    $disk->put('img/games/death-stranding/001.txt','HELLO WORLD!');
    $disk->put('img/games/death-stranding/001.icon.ldpi.txt','HELLO WORLD!');
    $disk->put('img/games/death-stranding/001.icon.mdpi.txt','HELLO WORLD!');
    $disk->put('img/games/death-stranding/001.icon.hdpi.txt','HELLO WORLD!');
    $disk->put('img/games/death-stranding/001.cover.ldpi.txt','HELLO WORLD!');
    $disk->put('img/games/death-stranding/001.cover.mdpi.txt','HELLO WORLD!');
    $disk->put('img/games/death-stranding/001.cover.hdpi.txt','HELLO WORLD!');
  }

  public function test_sources() {
    Storage::fake('dimages');
    $this->setUpFiles();

    $dimage0 = DimageName::fromFilePath('games/death-stranding/000.txt');
    $dimage1 = DimageName::fromFilePath('games/death-stranding/001.txt');

    $dimages = new DimageManager;
    $this->assertEquals(
      $dimages->sources('games','death-stranding'),
      [ $dimage0, $dimage1 ]
    );
  }

  public function test_derivatives() {
    Storage::fake('dimages');
    $this->setUpFiles();

    $files = [
      'games/death-stranding/000.cover.hdpi.txt',
      'games/death-stranding/000.cover.ldpi.txt',
      'games/death-stranding/000.cover.mdpi.txt',
      'games/death-stranding/000.icon.hdpi.txt',
      'games/death-stranding/000.icon.ldpi.txt',
      'games/death-stranding/000.icon.mdpi.txt',
      'games/death-stranding/000.topbar.ldpi.txt',

      'games/death-stranding/001.cover.hdpi.txt',
      'games/death-stranding/001.cover.ldpi.txt',
      'games/death-stranding/001.cover.mdpi.txt',
      'games/death-stranding/001.icon.hdpi.txt',
      'games/death-stranding/001.icon.ldpi.txt',
      'games/death-stranding/001.icon.mdpi.txt',
    ];
    $list = DimageName::fromFilePathArray($files);

    $dimages = new DimageManager;
    $this->assertEquals( $dimages->derivatives('games','death-stranding'), $list );
  }

  public function test_source() {
    Storage::fake('dimages');
    $this->setUpFiles();
    
    $dimage0 = DimageName::fromFilePath('games/death-stranding/000.txt');
    $dimage1 = DimageName::fromFilePath('games/death-stranding/001.txt');
    
    $dimages = new DimageManager;
    $this->assertEquals( $dimages->source('games','death-stranding'), $dimage0 );
    $this->assertEquals( $dimages->source('games','death-stranding', 0), $dimage0 );
    $this->assertEquals( $dimages->source('games','death-stranding', 1), $dimage1 );
  }

  public function test_source_NoSource_Exception() {
    Storage::fake('dimages');
    $this->expectException(DimageNotFoundException::class);
    $this->expectExceptionMessage('Image not found:games/death-stranding/0');
    $dimages = new DimageManager;
    $dimages->source('games','death-stranding');
  }

  public function test_source_NonExistent_Exception() {
    Storage::fake('dimages');
    $this->expectException(DimageNotFoundException::class);
    $this->expectExceptionMessage('Image not found:games/death-stranding/5');
    $dimages = new DimageManager;
    $dimages->source('games','death-stranding', 5);
  }

  public function test_derivative() {
    Storage::fake('dimages');
    $this->setUpFiles();

    $dimage0 = DimageName::fromFilePath('games/death-stranding/000.cover.mdpi.txt');
    $dimage1 = DimageName::fromFilePath('games/death-stranding/001.cover.mdpi.txt');

    $dimages = new DimageManager;
    $this->assertEquals( $dimages->derivative('games','death-stranding','cover','mdpi'), $dimage0 );
    $this->assertEquals( $dimages->derivative('games','death-stranding','cover','mdpi',0), $dimage0 );
    $this->assertEquals( $dimages->derivative('games','death-stranding','cover','mdpi',1), $dimage1 );
  }

  public function test_derivative_NoSource_Exception() {
    Storage::fake('dimages');
    $this->expectException(DimageNotFoundException::class);
    $this->expectExceptionMessage('Image not found:games/death-stranding/cover/mdpi/0');
    $dimages = new DimageManager;
    $dimages->derivative('games','death-stranding','cover','mdpi');
  }

  public function test_derivative_NonExistent_Exception() {
    Storage::fake('dimages');
    $this->expectException(DimageNotFoundException::class);
    $this->expectExceptionMessage('Image not found:games/death-stranding/cover/mdpi/1');
    $dimages = new DimageManager;
    $dimages->derivative('games','death-stranding','cover','mdpi',1);
  }

  public function test_derivativeOrSource() {
    Storage::fake('dimages');
    $this->setUpFiles();

    $dimages = new DimageManager;
    $dimage = $dimages->derivativeOrSource('games','death-stranding','icon','ldpi',1);

    $this->assertTrue($dimage->isDerived());
    
    $dimage = $dimages->derivativeOrSource('games','death-stranding','icon','xxxhdpi',1);

    $this->assertTrue($dimage->isSource());
  }

  public function test_get() {
    Storage::fake('dimages');
    $disk = Storage::disk('dimages');
    $upload = UploadedFile::fake()->image('image.jpg',1920, 1080);
    $disk->putFileAs('img/games/death-stranding', $upload, '001.jpg');

    $dimages = new DimageManager;
    $dimage = $dimages->get('games','death-stranding','launcher-icons','mdpi',1);

    $disk->assertExists('img/games/death-stranding/001.launcher-icons.mdpi.jpg');
  }

  public function test_storeIdentity() {
    Storage::fake('dimages');
    $disk = Storage::disk('dimages');
    $upload = UploadedFile::fake()->image('image.png',1920, 1080);

    $dimages = new DimageManager;
    $dimages->storeIdentity('racing-games', 'need-for-speed-xviii', $upload);

    $disk->assertExists('img/racing-games/need-for-speed-xviii/000.png');
  }

  public function test_rename() {
    Storage::fake('dimages');
    $disk = Storage::disk('dimages');
    $this->setUpFiles();

    $dsource = DimageName::fromFilePath('games/death-stranding/000.cover.hdpi.txt');
    $dtarget = DimageName::fromFilePath('games/death-stranding/005.cover.hdpi.txt');

    $disk->assertExists ($dsource->toFullPathFileName());
    $disk->assertMissing($dtarget->toFullPathFileName());

    $dimages = new DimageManager;
    $dimages->rename($dsource, $dtarget);

    $disk->assertMissing($dsource->toFullPathFileName());
    $disk->assertExists ($dtarget->toFullPathFileName());
  }

  public function test_switchIndex() {
    Storage::fake('dimages');
    $disk = Storage::disk('dimages');
    $this->setUpFiles();

    $disk->assertExists ('img/games/death-stranding/000.topbar.ldpi.txt');

    $dimages = new DimageManager;
    $dimages->switchIndex('games','death-stranding',0,1);
    
    $disk->assertMissing('img/games/death-stranding/000.topbar.ldpi.txt');
    $disk->assertExists ('img/games/death-stranding/001.topbar.ldpi.txt');
  }

  public function test_deleteDerivatives() {
    Storage::fake('dimages');
    $disk = Storage::disk('dimages');
    $this->setUpFiles();

    $disk->assertExists('img/games/death-stranding/000.txt');
    $disk->assertExists('img/games/death-stranding/000.icon.ldpi.txt');
    $disk->assertExists('img/games/death-stranding/000.icon.mdpi.txt');
    $disk->assertExists('img/games/death-stranding/000.icon.hdpi.txt');
    $disk->assertExists('img/games/death-stranding/000.cover.ldpi.txt');
    $disk->assertExists('img/games/death-stranding/000.cover.mdpi.txt');
    $disk->assertExists('img/games/death-stranding/000.cover.hdpi.txt');
    $disk->assertExists('img/games/death-stranding/000.topbar.ldpi.txt');
    $disk->assertExists('img/games/death-stranding/001.txt');
    $disk->assertExists('img/games/death-stranding/001.icon.ldpi.txt');
    $disk->assertExists('img/games/death-stranding/001.icon.mdpi.txt');
    $disk->assertExists('img/games/death-stranding/001.icon.hdpi.txt');
    $disk->assertExists('img/games/death-stranding/001.cover.ldpi.txt');
    $disk->assertExists('img/games/death-stranding/001.cover.mdpi.txt');
    $disk->assertExists('img/games/death-stranding/001.cover.hdpi.txt');

    $dimages = new DimageManager;
    $dimages->deleteDerivatives('games','death-stranding');

    $disk->assertExists ('img/games/death-stranding/000.txt');
    $disk->assertMissing('img/games/death-stranding/000.icon.ldpi.txt');
    $disk->assertMissing('img/games/death-stranding/000.icon.mdpi.txt');
    $disk->assertMissing('img/games/death-stranding/000.icon.hdpi.txt');
    $disk->assertMissing('img/games/death-stranding/000.cover.ldpi.txt');
    $disk->assertMissing('img/games/death-stranding/000.cover.mdpi.txt');
    $disk->assertMissing('img/games/death-stranding/000.cover.hdpi.txt');
    $disk->assertMissing('img/games/death-stranding/000.topbar.ldpi.txt');
    $disk->assertExists ('img/games/death-stranding/001.txt');
    $disk->assertMissing('img/games/death-stranding/001.icon.ldpi.txt');
    $disk->assertMissing('img/games/death-stranding/001.icon.mdpi.txt');
    $disk->assertMissing('img/games/death-stranding/001.icon.hdpi.txt');
    $disk->assertMissing('img/games/death-stranding/001.cover.ldpi.txt');
    $disk->assertMissing('img/games/death-stranding/001.cover.mdpi.txt');
    $disk->assertMissing('img/games/death-stranding/001.cover.hdpi.txt');
  }

  public function test_normalize() {
    Storage::fake('dimages');
    $disk = Storage::disk('dimages');
    
    $disk->put('img/games/death-stranding/001.txt','HELLO WORLD!');
    $disk->put('img/games/death-stranding/001.cover.hdpi.txt','HELLO WORLD!');
    $disk->put('img/games/death-stranding/003.txt','HELLO WORLD!');
    $disk->put('img/games/death-stranding/003.cover.mdpi.txt','HELLO WORLD!');
    $disk->put('img/games/death-stranding/005.txt','HELLO WORLD!');
    $disk->put('img/games/death-stranding/005.boxart.mdpi.txt','HELLO WORLD!');

    $dimages = new DimageManager;
    $dimages->normalize('games','death-stranding');

    $disk->assertExists ('img/games/death-stranding/000.txt');
    $disk->assertExists ('img/games/death-stranding/000.cover.hdpi.txt');
    $disk->assertExists ('img/games/death-stranding/001.txt');
    $disk->assertExists ('img/games/death-stranding/001.cover.mdpi.txt');
    $disk->assertExists ('img/games/death-stranding/002.txt');
    $disk->assertExists ('img/games/death-stranding/002.boxart.mdpi.txt');
  }
}