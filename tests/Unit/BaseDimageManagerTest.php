<?php

namespace Marcohern\Dimages\Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
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

  protected function setUpFiles() {
    $disk = Storage::disk('dimages');
    $disk->put('img/games/death-stranding/000.txt','HELLO WORLD!');
    $disk->put('img/games/death-stranding/000.icon.ldpi.txt','HELLO WORLD!');
    $disk->put('img/games/death-stranding/000.icon.mdpi.txt','HELLO WORLD!');
    $disk->put('img/games/death-stranding/000.icon.hdpi.txt','HELLO WORLD!');
    $disk->put('img/games/death-stranding/000.cover.ldpi.txt','HELLO WORLD!');
    $disk->put('img/games/death-stranding/000.cover.mdpi.txt','HELLO WORLD!');
    $disk->put('img/games/death-stranding/000.cover.hdpi.txt','HELLO WORLD!');
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

    $dimages = new BaseDimageManager;
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

      'games/death-stranding/001.cover.hdpi.txt',
      'games/death-stranding/001.cover.ldpi.txt',
      'games/death-stranding/001.cover.mdpi.txt',
      'games/death-stranding/001.icon.hdpi.txt',
      'games/death-stranding/001.icon.ldpi.txt',
      'games/death-stranding/001.icon.mdpi.txt',
    ];
    $list = DimageName::fromFilePathArray($files);

    $dimages = new BaseDimageManager;
    $this->assertEquals( $dimages->derivatives('games','death-stranding'), $list );
  }

  public function test_source() {
    Storage::fake('dimages');
    $this->setUpFiles();
    
    $dimage0 = DimageName::fromFilePath('games/death-stranding/000.txt');
    $dimage1 = DimageName::fromFilePath('games/death-stranding/001.txt');
    
    $dimages = new BaseDimageManager;
    $this->assertEquals( $dimages->source('games','death-stranding'), $dimage0 );
    $this->assertEquals( $dimages->source('games','death-stranding', 0), $dimage0 );
    $this->assertEquals( $dimages->source('games','death-stranding', 1), $dimage1 );
  }

  public function test_derivative() {
    Storage::fake('dimages');
    $this->setUpFiles();

    $dimage0 = DimageName::fromFilePath('games/death-stranding/000.cover.mdpi.txt');
    $dimage1 = DimageName::fromFilePath('games/death-stranding/001.cover.mdpi.txt');

    $dimages = new BaseDimageManager;
    $this->assertEquals( $dimages->derivative('games','death-stranding','cover','mdpi'), $dimage0 );
    $this->assertEquals( $dimages->derivative('games','death-stranding','cover','mdpi',0), $dimage0 );
    $this->assertEquals( $dimages->derivative('games','death-stranding','cover','mdpi',1), $dimage1 );
  }
}