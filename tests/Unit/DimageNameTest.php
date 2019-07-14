<?php

namespace Marcohern\Dimages\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Marcohern\Dimages\Exceptions\ImageException;
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
    $this->expectException(ImageException::class);

    $dimage = DimageName::fromFilePath('path/to/invalid_file_name.jpeg');
  }

  public function test_source() {
    $dimage = DimageName::fromFilePath('music/sure-know-something-by-kiss/002.cover.mdpi.jpg');
    $source = $dimage->source();
    $this->assertEquals($source->toFileName(), 'music/sure-know-something-by-kiss/002.jpg');
  }

  public function test_fromUrl() {
    $dimage = DimageName::fromUrl('tecno/sucks-to-be-you-by-prozzak/cd-cover/hdpi/4', 'jpeg');
    $this->assertSame($dimage->entity, 'tecno');
    $this->assertSame($dimage->identity, 'sucks-to-be-you-by-prozzak');
    $this->assertSame($dimage->index, 4);
    $this->assertSame($dimage->profile, 'cd-cover');
    $this->assertSame($dimage->density, 'hdpi');
    $this->assertSame($dimage->ext, 'jpeg');
    $this->assertSame($dimage->toFileName(), 'tecno/sucks-to-be-you-by-prozzak/004.cd-cover.hdpi.jpeg');
  }
  
  public function test_toUrl_Full_Valid() {
    $dimage = new DimageName;
    $dimage->entity = 'movies';
    $dimage->identity = 'the-grand-budapest-hotel';
    $dimage->index = 5;
    $dimage->profile = 'toolbar';
    $dimage->density = 'hdpi';
    $dimage->ext = 'jpg';

    $this->assertEquals($dimage->toUrl(), 'movies/the-grand-budapest-hotel/toolbar/hdpi/5');
  }

  public function test_toUrl_FullNoIndex_Valid() {
    $dimage = new DimageName;
    $dimage->entity = 'movies';
    $dimage->identity = 'the-grand-budapest-hotel';
    $dimage->index = 0;
    $dimage->profile = 'toolbar';
    $dimage->density = 'hdpi';
    $dimage->ext = 'jpg';

    $this->assertEquals($dimage->toUrl(), 'movies/the-grand-budapest-hotel/toolbar/hdpi');
  }

  public function test_toUrl_NoProfileDensity_Valid() {
    $dimage = new DimageName;
    $dimage->entity = 'movies';
    $dimage->identity = 'the-grand-budapest-hotel';
    $dimage->index = 15;
    $dimage->ext = 'jpg';

    $this->assertEquals($dimage->toUrl(), 'movies/the-grand-budapest-hotel/15');
  }

  public function test_toUrl_NoProfileDensityIndex_Valid() {
    $dimage = new DimageName;
    $dimage->entity = 'movies';
    $dimage->identity = 'the-grand-budapest-hotel';
    $dimage->index = 0;
    $dimage->ext = 'jpg';

    $this->assertEquals($dimage->toUrl(), 'movies/the-grand-budapest-hotel');
  }

  public function test_toFileName() {
    $dimage = new DimageName;
    $dimage->entity = 'marvel';
    $dimage->identity = 'wolverine';
    $dimage->index = 0;
    $dimage->profile = 'main-cover';
    $dimage->density = 'xxhdpi';
    $dimage->ext = 'jpeg';

    $this->assertEquals($dimage->toFileName(), 'marvel/wolverine/000.main-cover.xxhdpi.jpeg');
  }

  public function test_toString() {
    $dimage = new DimageName;
    $dimage->entity = 'marvel';
    $dimage->identity = 'wolverine';
    $dimage->index = 3;
    $dimage->profile = 'main-cover';
    $dimage->density = 'xxhdpi';
    $dimage->ext = 'jpeg';

    $this->assertEquals($dimage, 'marvel/wolverine/main-cover/xxhdpi/3');
  }
  
  public function test_multitest() {
    $dimage1 = DimageName::fromFilePath('music/sure-know-something-by-kiss/002.cover.mdpi.jpg');
    $dimage2 = DimageName::fromFilePath('music/deuce-by-kiss/000.large-icon.ldpi.png');
    $dimage3 = DimageName::fromFilePath('music/god-gave-rock-and-roll-to-you-by-kiss/003.png');
    $dimage4 = DimageName::fromFilePath('music/detroit-rock-city-by-kiss/000.jpg');

    $this->assertEquals($dimage1->toUrl(), 'music/sure-know-something-by-kiss/cover/mdpi/2');
    $this->assertEquals($dimage2->toUrl(), 'music/deuce-by-kiss/large-icon/ldpi');
    $this->assertEquals($dimage3->toUrl(), 'music/god-gave-rock-and-roll-to-you-by-kiss/3');
    $this->assertEquals($dimage4->toUrl(), 'music/detroit-rock-city-by-kiss');

    $this->assertEquals($dimage1->toFileName(), 'music/sure-know-something-by-kiss/002.cover.mdpi.jpg');
    $this->assertEquals($dimage2->toFileName(), 'music/deuce-by-kiss/000.large-icon.ldpi.png');
    $this->assertEquals($dimage3->toFileName(), 'music/god-gave-rock-and-roll-to-you-by-kiss/003.png');
    $this->assertEquals($dimage4->toFileName(), 'music/detroit-rock-city-by-kiss/000.jpg');
  }
}
