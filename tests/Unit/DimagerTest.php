<?php

namespace Marcohern\Dimages\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Marcohern\Dimages\Lib\Dimager;
use Marcohern\Dimages\Lib\Dimage;
use Marcohern\Dimages\Exceptions\DimageNotFoundException;
use stdClass;

class DimagerTest extends TestCase
{
    public function test_getById() {
        $dir = dirname(dirname(__FILE__))."/images";
        $dimager = new Dimager($dir);
        $dimage = $dimager->getById(5);
        $this->assertTrue(true);
    }

    public function test_getById_notFound() {
        $dir = dirname(dirname(__FILE__))."/images";
        $dimager = new Dimager($dir);
        try {
            $dimage = $dimager->getById(235);
            $this->assertFalse(false, "Exception expected");
        } catch (DimageNotFoundException $ex) {
            $this->assertTrue(true);
        }
    }

    public function test_getSources() {
        $dir = dirname(dirname(__FILE__))."/images";
        $dimager = new Dimager($dir);
        $items = $dimager->getSources('videogames','resident-evil-vii');
        $this->assertGreaterThan(1, count($items));
    }
    
    public function test_getImage() {
        $dir = dirname(dirname(__FILE__))."/images";
        $dimager = new Dimager($dir);
        $dimage = Dimage::fromFileName("videogames.resident-evil-vii.001.org.org.101.jpg");
        $iimage = $dimager->getImage($dimage);
        $this->assertTrue(true);
    }
}