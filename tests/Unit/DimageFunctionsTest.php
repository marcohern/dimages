<?php

namespace Marcohern\Dimages\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Marcohern\Dimages\Lib\DimageFunctions;
use Marcohern\Dimages\Lib\DimageName;
use Marcohern\Dimages\Lib\Dimage;

class DimageFunctionsTest extends TestCase
{

  protected function setUp() : void {
    Dimage::boot();
    parent::setUp();
  }

  protected function tearDown() : void {
    parent::tearDown();
    Dimage::shutdown();
  }
  public function test_fileNameRegex() {
    $regex = DimageFunctions::fileNameRegex();
    $this->assertRegExp($regex, 'tecno/sucks-to-be-you-by-prozzak/123.cover.mdpi.jpeg');
    $this->assertRegExp($regex, 'tecno/sucks-to-be-you-by-prozzak/123.jpeg');
  }
  
  public function test_urlRegex() {
    $regex = DimageFunctions::urlRegex();
    $this->assertRegExp($regex, 'tecno/sucks-to-be-you-by-prozzak/cover/mdpi/123');
    $this->assertRegExp($regex, 'tecno/sucks-to-be-you-by-prozzak/cover/mdpi/');
    $this->assertRegExp($regex, 'tecno/sucks-to-be-you-by-prozzak/123');
    $this->assertRegExp($regex, 'tecno/sucks-to-be-you-by-prozzak/');
    $this->assertRegExp($regex, 'tecno/sucks-to-be-you-by-prozzak');
  }

  public function test_pad() {
    $this->assertSame(DimageFunctions::pad(1, 1), '1');
    $this->assertSame(DimageFunctions::pad(2, 2), '02');
    $this->assertSame(DimageFunctions::pad(3, 3), '003');
    $this->assertSame(DimageFunctions::pad(45, 4), '0045');
    $this->assertSame(DimageFunctions::pad(12345, 5), '12345');
    $this->assertSame(DimageFunctions::pad(12345, 3), '12345');
  }

  public function test_padIndex() {
    $this->assertSame(DimageFunctions::padIndex(1), '001');
    $this->assertSame(DimageFunctions::padIndex(12), '012');
    $this->assertSame(DimageFunctions::padIndex(123), '123');
    $this->assertSame(DimageFunctions::padIndex(1234), '1234');
  }

  public function test_imageFomder() {
    $folder = DimageFunctions::rootFolder();
    $this->assertSame($folder, 'img');
  }

  public function test_entityFolder() {
    $folder = DimageFunctions::entityFolder('movies');
    
    $this->assertSame($folder, 'img/movies');
  }

  public function test_identityFolder() {
    $folder = DimageFunctions::identityFolder('movies','terminator');
    
    $this->assertSame($folder, 'img/movies/terminator');
  }

  public function test_findVariables() {
    $vars = DimageFunctions::findVariables('%var1/%var2/%var3');
    $this->assertSame($vars, ['var1','var2','var3']);
  }

  public function test_regex() {
    $source = '%entity\/%identity\/%index(\/%profile\/%density)?\.%ext';
    $expressions = [
      'entity'   => '(?<entity>%idf)',
      'identity' => '(?<identity>%idf)',
      'index'    => '(?<identity>%int)',
      'profile'  => '(?<profile>%idf)',
      'density'  => '(?<density>%idf)',
      'ext'      => '(?<ext>%idf)',
      'idf'      => '[\w\-_\.@]+',
      'int'      => '\d+',
    ];
    $exp = DimageFunctions::regex($source, $expressions);
    
    $this->assertEquals(
      $exp, "/(?<entity>[\w\-_\.@]+)\/(?<identity>[\w\-_\.@]+)\/(?<identity>\d+)(\/(?<profile>[\w\-_\.@]+)\/(?<density>[\w\-_\.@]+))?\.(?<ext>[\w\-_\.@]+)/"
    );
  }
}
