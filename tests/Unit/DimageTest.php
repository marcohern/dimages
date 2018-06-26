<?php

namespace Marcohern\Dimages\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Marcohern\Dimages\Lib\Dimage;
use Marcohern\Dimages\Exceptions\FileNameInvalidException;
use stdClass;

class DimageTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_constructor()
    {
        $dimage = new Dimage;
        $this->assertInstanceOf(Dimage::class, $dimage);
        $this->assertEquals($dimage->id, 0);
        $this->assertNull($dimage->domain);
        $this->assertNull($dimage->slug);
        $this->assertNull($dimage->index);
        $this->assertNull($dimage->profile);
        $this->assertNull($dimage->density);
        $this->assertNull($dimage->ext);
    }

    public function test_fromStdClass() {
        $obj = (object) [
            'id' => 99,
            'domain' => 'bars',
            'slug' => 'tu-jaus-bar',
            'index' => 1,
            'profile' => 'cover',
            'density' => 'hdpi',
            'ext' => 'jpeg'
        ];
        $dimage = Dimage::fromStdClass($obj);
        $this->assertInstanceOf(Dimage::class, $dimage);
        $this->assertEquals($dimage->id, 99);
        $this->assertEquals($dimage->domain, 'bars');
        $this->assertEquals($dimage->slug, 'tu-jaus-bar');
        $this->assertEquals($dimage->index, 1);
        $this->assertEquals($dimage->profile, 'cover');
        $this->assertEquals($dimage->density, 'hdpi');
        $this->assertEquals($dimage->ext, 'jpeg');
    }

    public function test_fromFileName() {
        $filename = "path/to/valid/filename/bars.tu-jaus-bar.001.cover.hdpi.99.jpeg";
        $dimage = Dimage::fromFileName($filename);
        $this->assertInstanceOf(Dimage::class, $dimage);
        $this->assertEquals($dimage->id, 99);
        $this->assertEquals($dimage->domain, 'bars');
        $this->assertEquals($dimage->slug, 'tu-jaus-bar');
        $this->assertEquals($dimage->index, 1);
        $this->assertEquals($dimage->profile, 'cover');
        $this->assertEquals($dimage->density, 'hdpi');
        $this->assertEquals($dimage->ext, 'jpeg');
    }

    public function test_fromFileName_FilenameInvalid() {
        $filename = "path/to/regular/filename.jpeg";
        try {
            $dimage = Dimage::fromFileName($filename);
            $this->assertFalse(true, "Exception expected");
        } catch(FileNameInvalidException $ex) {
            $this->assertTrue(true);
        }
    }

    public function test_getFileName() {
        $obj = (object) [
            'id' => 99,
            'domain' => 'bars',
            'slug' => 'tu-jaus-bar',
            'index' => 1,
            'profile' => 'cover',
            'density' => 'hdpi',
            'ext' => 'jpeg'
        ];
        $dimage = Dimage::fromStdClass($obj);

        $this->assertEquals($dimage->getFileName(), "bars.tu-jaus-bar.001.cover.hdpi.99.jpeg");
    }
}
