<?php

namespace Marcohern\Dimages\Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

use Marcohern\Dimages\Lib\Files\DimageFile;
use Marcohern\Dimages\Lib\Managers\StorageManager;

class DimageFileTest extends TestCase {

  protected function setUp():void {
    parent::setUp();
  }

  protected function tearDown():void {
    parent::tearDown();
  }

  public function test_construct() {
    $file = new DimageFile('games','death-stranding',5,'txt','cover','mdpi');
    $this->assertSame($file->entity, 'games');
    $this->assertSame($file->identity, 'death-stranding');
    $this->assertSame($file->index, 5);
    $this->assertSame($file->profile, 'cover');
    $this->assertSame($file->density, 'mdpi');
    $this->assertSame($file->ext, 'txt');
  }

  public function test_tenant() {
    $file1 = new DimageFile('games','death-stranding',5,'txt','cover','mdpi');
    $file2 = new DimageFile('games','death-stranding',5,'txt');
    $file1->tenant = $file2->tenant = 'marco';

    $this->assertSame($file1->toFilePath(), 'img/marco/games/death-stranding/005/cover/mdpi.txt');
    $this->assertSame($file2->toFilePath(), 'img/marco/games/death-stranding/005.txt');
  }

  public function test_isSource() {
    $file = new DimageFile('games','death-stranding',5,'txt');

    $this->assertTrue($file->isSource());
  }

  public function test_isDerived() {
    $file = new DimageFile('games','death-stranding',5,'txt','cover','mdpi');

    $this->assertTrue($file->isDerived());
  }

  public function test_toFilePath() {
    $file1 = new DimageFile('games','death-stranding',5,'txt','cover','mdpi');
    $file2 = new DimageFile('games','death-stranding',5,'txt');

    $this->assertSame($file1->toFilePath(), 'img/_global/games/death-stranding/005/cover/mdpi.txt');
    $this->assertSame($file2->toFilePath(), 'img/_global/games/death-stranding/005.txt');
  }

  public function test_toFolder() {
    $file1 = new DimageFile('games','death-stranding',5,'txt','cover','mdpi');
    $file2 = new DimageFile('games','death-stranding',5,'txt');

    $this->assertSame($file1->toFolder(), 'img/_global/games/death-stranding/005/cover');
    $this->assertSame($file2->toFolder(), 'img/_global/games/death-stranding');
  }

  public function test_toFileName() {
    $file1 = new DimageFile('games','death-stranding',5,'txt','cover','mdpi');
    $file2 = new DimageFile('games','death-stranding',5,'txt');

    $this->assertSame($file1->toFileName(), 'mdpi.txt');
    $this->assertSame($file2->toFileName(), '005.txt');
  }
}