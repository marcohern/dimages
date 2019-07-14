<?php

namespace Marcohern\Dimages\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Marcohern\Dimages\Lib\Dimages\DimageFunctions;

class DimageFunctionsTest extends TestCase
{
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
  
}
