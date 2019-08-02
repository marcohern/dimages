<?php

namespace Marcohern\Dimages\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Marcohern\Dimages\Lib\DimageFolders;

class DimageFoldersTest extends TestCase {
  public function test_entities() {
    $folder = DimageFolders::entities('the-user');
    $this->assertEquals($folder, 'img/the-user');
  }

  public function test_identities() {
    $folder = DimageFolders::identities('the-user','the-entity');
    $this->assertEquals($folder, 'img/the-user/the-entity');
  }

  public function test_sources() {
    $folder = DimageFolders::sources('the-user','the-entity','the-image');
    $this->assertEquals($folder, 'img/the-user/the-entity/the-image');
  }

  public function test_source() {
    $folder = DimageFolders::source('the-user','the-entity','the-image',12);
    $this->assertEquals($folder, 'img/the-user/the-entity/the-image/012');
  }

  public function test_profile() {
    $folder = DimageFolders::profile('the-user','the-entity','the-image',12,'cover');
    $this->assertEquals($folder, 'img/the-user/the-entity/the-image/012/cover');
  }

  public function test_sequenceFile() {
    $folder = DimageFolders::sequenceFile('the-user','the-entity','the-image');
    $this->assertEquals($folder, 'img/the-user/_seq/the-entity.the-image.id');
  }

  public function test_staging() {
    $folder = DimageFolders::staging('the-user','abcdefgh');
    $this->assertEquals($folder, 'img/the-user/_tmp/abcdefgh');
  }
}