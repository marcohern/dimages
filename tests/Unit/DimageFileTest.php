<?php

namespace Marcohern\Dimages\Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

use Marcohern\Dimages\Lib\Files\DimageFile;
use Marcohern\Dimages\Lib\Managers\StorageManager;
use Marcohern\Dimages\Lib\Factory;
use Marcohern\Dimages\Lib\Fs;

class DimageFileTest extends TestCase {
  protected $fs;

  protected function setUp():void {
    parent::setUp();
    $this->fs = new Fs();
  }

  protected function tearDown():void {
    unset($this->fs);
    parent::tearDown();
  }

  public function test_construct() {
    $file = new DimageFile($this->fs, 'death-stranding','txt', 5, 'games','cover','mdpi');
    $this->assertSame($file->entity, 'games');
    $this->assertSame($file->identity, 'death-stranding');
    $this->assertSame($file->index, 5);
    $this->assertSame($file->profile, 'cover');
    $this->assertSame($file->density, 'mdpi');
    $this->assertSame($file->ext, 'txt');
  }

  public function test_tenant() {
    $file1 = new DimageFile($this->fs, 'death-stranding','txt',5,'games','cover','mdpi');
    $file2 = new DimageFile($this->fs, 'death-stranding','txt',5 ,'games');
    $file1->tenant = $file2->tenant = 'marco';

    $this->assertSame($file1->toFilePath(), 'marco/games/death-stranding/005/cover/mdpi.txt');
    $this->assertSame($file2->toFilePath(), 'marco/games/death-stranding/005.txt');
  }

  public function test_isSource() {
    $file = new DimageFile($this->fs, 'death-stranding','txt' ,5 ,'games');

    $this->assertTrue($file->isSource());
  }

  public function test_isDerived() {
    $file = new DimageFile($this->fs, 'death-stranding','txt' ,5 ,'games','cover','mdpi');

    $this->assertTrue($file->isDerived());
  }

  public function test_toFilePath() {
    $file1 = new DimageFile($this->fs, 'death-stranding','txt' ,5 ,'games','cover','mdpi');
    $file2 = new DimageFile($this->fs, 'death-stranding','txt' ,5 ,'games');

    $this->assertSame($file1->toFilePath(), '_anyone/games/death-stranding/005/cover/mdpi.txt');
    $this->assertSame($file2->toFilePath(), '_anyone/games/death-stranding/005.txt');
  }

  public function test_toFolder() {
    $file1 = new DimageFile($this->fs, 'death-stranding','txt' ,5 ,'games','cover','mdpi');
    $file2 = new DimageFile($this->fs, 'death-stranding','txt' ,5 ,'games');

    $this->assertSame($file1->toFolder(), '_anyone/games/death-stranding/005/cover');
    $this->assertSame($file2->toFolder(), '_anyone/games/death-stranding');
  }

  public function test_toFileName() {
    $file1 = new DimageFile($this->fs, 'death-stranding','txt',5,'games','cover','mdpi');
    $file2 = new DimageFile($this->fs, 'death-stranding','txt',5,'games');

    $this->assertSame($file1->toFileName(), 'mdpi.txt');
    $this->assertSame($file2->toFileName(), '005.txt');
  }

  public function test_fromFilePath() {
    $file = DimageFile::fromFilePath($this->fs, 'marcohern@gmail.com/games/death-stranding/012.txt');
    $this->assertSame($file->tenant, 'marcohern@gmail.com');
    $this->assertSame($file->entity, 'games');
    $this->assertSame($file->identity, 'death-stranding');
    $this->assertSame($file->index, 12);
    $this->assertSame($file->profile, '');
    $this->assertSame($file->density, '');
    $this->assertSame($file->ext, 'txt');
  }
}