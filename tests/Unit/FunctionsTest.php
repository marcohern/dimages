<?php

namespace Marcohern\Dimages\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Marcohern\Dimages\Lib\Functions;
use Marcohern\Dimages\Lib\DimageName;
use Marcohern\Dimages\Lib\Dimage;

class FunctionsTest extends TestCase
{

  protected function setUp() : void {
    Dimage::boot();
    parent::setUp();
  }

  protected function tearDown() : void {
    parent::tearDown();
    Dimage::shutdown();
  }

  public function test_pad() {
    $this->assertSame(Functions::pad(1, 1), '1');
    $this->assertSame(Functions::pad(2, 2), '02');
    $this->assertSame(Functions::pad(3, 3), '003');
    $this->assertSame(Functions::pad(45, 4), '0045');
    $this->assertSame(Functions::pad(12345, 5), '12345');
    $this->assertSame(Functions::pad(12345, 3), '12345');
  }

  public function test_padIndex() {
    $this->assertSame(Functions::padIndex(1), '001');
    $this->assertSame(Functions::padIndex(12), '012');
    $this->assertSame(Functions::padIndex(123), '123');
    $this->assertSame(Functions::padIndex(1234), '1234');
  }

  public function test_findVariables() {
    $vars = Functions::findVariables('%var1/%var2/%var3');
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
    $exp = Functions::regex($source, $expressions);
    
    $this->assertEquals(
      $exp, "/(?<entity>[\w\-_\.@]+)\/(?<identity>[\w\-_\.@]+)\/(?<identity>\d+)(\/(?<profile>[\w\-_\.@]+)\/(?<density>[\w\-_\.@]+))?\.(?<ext>[\w\-_\.@]+)/"
    );
  }
}
