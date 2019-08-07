<?php

namespace Marcohern\Dimages\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Marcohern\Dimages\Lib\Fs;

class FsTest extends TestCase {
  protected $fs;

  protected function setUp():void {
    parent::setUp();
    $this->fs = new Fs;
  }

  protected function tearDown():void {
    unset($this->fs);
    parent::tearDown();
  }

  public function test_root() {
    $this->assertEquals('',$this->fs->root(''));
    $this->assertEquals('marco',$this->fs->root('marco'));

    $this->fs->setRoot('root');
    $this->assertEquals('root/marco',$this->fs->root('marco'));
  }

  public function test_rootFolder() {
    $this->assertEquals('',$this->fs->rootFolder());
  }

  public function test_tenantFolder() {
    $this->assertEquals('john-wick',$this->fs->tenantFolder('john-wick'));
  }

  public function test_entityFolder() {
    $this->assertEquals(
      'keanu-reeves/motorcicles',
      $this->fs->entityFolder('keanu-reeves','motorcicles')
    );
  }

  public function test_identityFolder() {
    $this->assertEquals(
      'keanu-reeves/motorcicles/mary-jane',
      $this->fs->identityFolder('keanu-reeves','motorcicles','mary-jane')
    );
  }

  public function test_indexFolder() {
    $this->assertEquals(
      'keanu-reeves/motorcicles/mary-jane/026',
      $this->fs->indexFolder('keanu-reeves','motorcicles','mary-jane',26)
    );
  }

  public function test_profileFolder() {
    $this->assertEquals(
      'keanu-reeves/motorcicles/mary-jane/026/cover-art',
      $this->fs->profileFolder('keanu-reeves','motorcicles','mary-jane',26,'cover-art')
    );
  }

  public function test_stagingFolder() {
    $this->assertEquals(
      'keanu-reeves/_staging',
      $this->fs->stagingFolder('keanu-reeves')
    );
  }

  public function test_stagingSessionFolder() {
    $this->assertEquals(
      'keanu-reeves/_staging/abcdefg',
      $this->fs->stagingSessionFolder('keanu-reeves','abcdefg')
    );
  }

  public function test_sourcePath() {
    $this->assertEquals(
      'keanu-reeves/motorcicles/mary-jane/026.jpeg',
      $this->fs->sourcePath('keanu-reeves','motorcicles','mary-jane',26,'jpeg')
    );
  }

  public function test_derivedPath() {
    $this->assertEquals(
      'keanu-reeves/motorcicles/mary-jane/026/cover-art/xhdpi.jpeg',
      $this->fs->derivedPath('keanu-reeves','motorcicles','mary-jane',26,'cover-art','xhdpi','jpeg')
    );
  }

  public function test_sourceFile() {
    $this->assertEquals(
      '026.jpeg',
      $this->fs->sourceFile(26,'jpeg')
    );
  }

  public function test_derivedFile() {
    $this->assertEquals(
      'xhdpi.jpeg',
      $this->fs->derivedFile('xhdpi','jpeg')
    );
  }

  public function test_sequencePath() {
    $this->assertEquals(
      'keanu-reeves/_seq/motorcicles.mary-jane.id',
      $this->fs->sequencePath('keanu-reeves','motorcicles','mary-jane')
    );
  }

  public function test_settingsPath() {
    $this->assertEquals(
      'keanu-reeves/settings.cfg',
      $this->fs->settingsPath('keanu-reeves')
    );
  }
}