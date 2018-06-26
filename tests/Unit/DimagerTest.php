<?php

namespace Marcohern\Dimages\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Intervention\Image\ImageManagerStatic as IImage;
use Intervention\Image\Image;
use Marcohern\Dimages\Lib\Dimager;
use Marcohern\Dimages\Lib\Dimage;
use Marcohern\Dimages\Exceptions\DimageNotFoundException;
use stdClass;

class DimagerTest extends TestCase
{

    public static function setUpBeforeClass() {
        $dir = dirname(dirname(__FILE__))."/images";
        $idfile = "$dir/dimageid.txt";
        $f = fopen($idfile, "w");
        fwrite($f, 123456);
        fclose($f);
    }
    
    public static function tearDownAfterClass()
    {
        $dir = dirname(dirname(__FILE__))."/images";
        $idfile = "$dir/dimageid.txt";
        if (file_exists($idfile)) unlink($idfile);
    }

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

    public function test_createImage_deleteImage() {
        $dir = dirname(dirname(__FILE__))."/images";
        $source = Dimage::fromFileName("videogames.resident-evil-vii.001.org.org.101.jpg");
        $sourcePath = "$dir/".$source->getFileName();
        $sourceImage = IImage::make($sourcePath);

        $dest = Dimage::fromFileName("temp.image.000.org.org.0.jpg");

        $dimager = new Dimager($dir);
        $dimage = $dimager->createImage($dest, $sourceImage);
        $this->assertInstanceOf(Dimage::class,$dimage);
        
        $this->assertTrue($dimager->deleteImage($dimage->id));
    }

    public function test_updateImage() {
        $dir = dirname(dirname(__FILE__))."/images";
        $source = Dimage::fromFileName("videogames.resident-evil-vii.001.org.org.101.jpg");
        $sourcePath = "$dir/".$source->getFileName();
        $sourceImage = IImage::make($sourcePath);
        
        $dest = Dimage::fromFileName("temp.image.000.org.org.0.jpg");

        $dimager = new Dimager($dir);
        $dimage = $dimager->createImage($dest, $sourceImage);
        $this->assertInstanceOf(Dimage::class,$dimage);
        
        $otherSource = Dimage::fromFileName("videogames.resident-evil-vii.005.org.org.105.jpg");
        $otherSourcePath = "$dir/".$source->getFileName();
        $otherSourceImage = IImage::make($otherSourcePath);

        $dimage = $dimager->updateImage($dest, $otherSourceImage);
        $this->assertInstanceOf(Dimage::class,$dimage);

        $this->assertTrue($dimager->deleteImage($dimage->id));
    }
}