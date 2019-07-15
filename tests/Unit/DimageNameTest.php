<?php

namespace Marcohern\Dimages\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Marcohern\Dimages\Exceptions\DimagesException;
use Marcohern\Dimages\Lib\Dimages\Dimage;
use Marcohern\Dimages\Lib\Dimages\DimageName;

class DimageNameTest extends TestCase
{
  protected function setUp() : void {
    Dimage::boot();
    parent::setUp();
  }

  protected function tearDown() : void {
    parent::tearDown();
    Dimage::shutdown();
  }

  public function test_fromFilePath() {
    $dimage = DimageName::fromFilePath('music/sure-know-something-by-kiss/002.cover.mdpi.jpg');

    $this->assertEquals($dimage->entity, 'music');
    $this->assertEquals($dimage->identity, 'sure-know-something-by-kiss');
    $this->assertEquals($dimage->index, 2);
    $this->assertEquals($dimage->profile, 'cover');
    $this->assertEquals($dimage->density, 'mdpi');
    $this->assertEquals($dimage->ext, 'jpg');
  }

  public function test_fromFilePath_NoProfileDensity_Valid() {
    $dimage = DimageName::fromFilePath('music/sure-know-something-by-kiss/002.jpg');

    $this->assertSame($dimage->entity, 'music');
    $this->assertSame($dimage->identity, 'sure-know-something-by-kiss');
    $this->assertSame($dimage->index, 2);
    $this->assertSame($dimage->profile,'');
    $this->assertSame($dimage->density,'');
    $this->assertSame($dimage->ext, 'jpg');
  }

  public function test_fromFilePath_NoProfileDensityIndexZero_Valid() {
    $dimage = DimageName::fromFilePath('music/sure-know-something-by-kiss/000.jpg');

    $this->assertEquals($dimage->entity, 'music');
    $this->assertEquals($dimage->identity, 'sure-know-something-by-kiss');
    $this->assertSame($dimage->index, 0);
    $this->assertEmpty($dimage->profile);
    $this->assertEmpty($dimage->density);
    $this->assertEquals($dimage->ext, 'jpg');
  }

  public function test_fromFilePath_InvalidFileName_Exception() {
    $this->expectException(DimagesException::class);

    $dimage = DimageName::fromFilePath('path/to/invalid_file_name.jpeg');
  }

  public function test_source() {
    $dimage = DimageName::fromFilePath('music/sure-know-something-by-kiss/002.cover.mdpi.jpg');
    $source = $dimage->source();
    $this->assertEquals($source->toIdentityPathFileName(), 'music/sure-know-something-by-kiss/002.jpg');
  }

  public function test_fromUrl() {
    $dimage = DimageName::fromUrl('tecno/sucks-to-be-you-by-prozzak/cd-cover/hdpi/4', 'jpeg');
    $this->assertSame($dimage->entity, 'tecno');
    $this->assertSame($dimage->identity, 'sucks-to-be-you-by-prozzak');
    $this->assertSame($dimage->index, 4);
    $this->assertSame($dimage->profile, 'cd-cover');
    $this->assertSame($dimage->density, 'hdpi');
    $this->assertSame($dimage->ext, 'jpeg');
    $this->assertSame($dimage->toIdentityPathFileName(), 'tecno/sucks-to-be-you-by-prozzak/004.cd-cover.hdpi.jpeg');
  }

  public function test_toMethods() {
    $dimage = new DimageName;
    $dimage->entity = 'movies';
    $dimage->identity = 'terminator';
    $dimage->index = 5;
    $dimage->profile = 'toolbar';
    $dimage->density = 'hdpi';
    $dimage->ext = 'jpg';

    //URL
    $this->assertEquals($dimage,        'movies/terminator/toolbar/hdpi/5');//toString
    $this->assertSame($dimage->toUrl(), 'movies/terminator/toolbar/hdpi/5');

    //Path
    $this->assertSame($dimage->toFullPath(), 'img/movies/terminator');
    $this->assertSame($dimage->toIdentityPath(), 'movies/terminator');

    //File
    $this->assertSame($dimage->toFullPathFileName(), 'img/movies/terminator/005.toolbar.hdpi.jpg');
    $this->assertSame($dimage->toIdentityPathFileName(), 'movies/terminator/005.toolbar.hdpi.jpg');
    $this->assertSame($dimage->toFileName(),                               '005.toolbar.hdpi.jpg');
  }
}
