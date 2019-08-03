<?php

namespace Marcohern\Dimages\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Marcohern\Dimages\Lib\DimageFolders;

class DimageFoldersTest extends TestCase {
  public function test_entities() {
    $folder = DimageFolders::entities('the-user');
    $this->assertEquals('the-user',$folder);
  }

  public function test_identities() {
    $folder = DimageFolders::identities('the-user','the-entity');
    $this->assertEquals('the-user/the-entity', $folder);
  }

  public function test_sources() {
    $folder = DimageFolders::sources('the-user','the-entity','the-image');
    $this->assertEquals('the-user/the-entity/the-image', $folder);
  }

  public function test_source() {
    $folder = DimageFolders::source('the-user','the-entity','the-image',12);
    $this->assertEquals('the-user/the-entity/the-image/012', $folder);
  }

  public function test_profile() {
    $folder = DimageFolders::profile('the-user','the-entity','the-image',12,'cover');
    $this->assertEquals('the-user/the-entity/the-image/012/cover', $folder);
  }

  public function test_sequenceFile() {
    $folder = DimageFolders::sequenceFile('the-user','the-entity','the-image');
    $this->assertEquals('the-user/_seq/the-entity.the-image.id', $folder);
  }

  public function test_staging() {
    $folder = DimageFolders::staging('the-user','abcdefgh');
    $this->assertEquals('the-user/_tmp/abcdefgh', $folder);
  }

  public function test_derived() {
    $folder = DimageFolders::derived('the-user','the-entity','the-image',12,'cover','mdpi','jpeg');
    $this->assertEquals('the-user/the-entity/the-image/012/cover/mdpi.jpeg', $folder);
  }
}