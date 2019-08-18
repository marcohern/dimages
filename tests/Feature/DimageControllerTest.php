<?php

namespace Marcohern\Dimages\Tests\Feature;

use Marcohern\Dimages\Lib\DimageFile;
use Marcohern\Dimages\Lib\Factory;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\Passport;
use App\User;

class DimageControllerTest extends TestCase
{
  protected $disk;

  protected function setUp():void {
    parent::setUp();
    Storage::fake('dimages');
    $this->disk = Storage::disk('dimages');
  }

  protected function tearDown():void {
    unset($this->disk);
    parent::tearDown();
  }

  public function test_store() {
    $image1 = UploadedFile::fake()->image('test1.jpg');
    $image2 = UploadedFile::fake()->image('test2.jpeg');
    $image3 = UploadedFile::fake()->image('test3.png');

    $this
      ->json('POST',"/dimage/user/test/image", ['image' => $image1])
      ->assertOk()
      ->assertExactJson(['index' => 0]);
    
    $this
      ->json('POST',"/dimage/user/test/image", ['image' => $image2])
      ->assertOk()
      ->assertExactJson(['index' => 1]);
    
    $this
      ->json('POST',"/dimage/user/test/image", ['image' => $image3])
      ->assertOk()
      ->assertExactJson(['index' => 2]);
    
    $this->disk->assertExists('user/test/image/000.jpg');
    $this->disk->assertExists('user/test/image/001.jpeg');
    $this->disk->assertExists('user/test/image/002.png');

  }

  public function test_stage() {
    $image1 = UploadedFile::fake()->image('test1.jpg');
    $image2 = UploadedFile::fake()->image('test2.jpeg');
    $image3 = UploadedFile::fake()->image('test3.png');

    $this
      ->json('POST',"/dimage/stage/user/abcdefg", ['image' => $image1])
      ->assertOk()
      ->assertExactJson(['index' => 0]);
    
    $this
      ->json('POST',"/dimage/stage/user/abcdefg", ['image' => $image2])
      ->assertOk()
      ->assertExactJson(['index' => 1]);
    
    $this
      ->json('POST',"/dimage/stage/user/abcdefg", ['image' => $image3])
      ->assertOk()
      ->assertExactJson(['index' => 2]);
    
    $this->disk->assertExists('user/_staging/abcdefg/000.jpg');
    $this->disk->assertExists('user/_staging/abcdefg/001.jpeg');
    $this->disk->assertExists('user/_staging/abcdefg/002.png');

  }

  public function test_attach() {
    $image1 = UploadedFile::fake()->image('test1.jpg');
    $image2 = UploadedFile::fake()->image('test2.jpeg');
    $image3 = UploadedFile::fake()->image('test3.png');
    
    $this->disk->put('user/_staging/abcdefg/000.jpg', $image1);
    $this->disk->put('user/_staging/abcdefg/001.jpeg', $image2);
    $this->disk->put('user/_staging/abcdefg/002.png', $image3);

    $this
      ->json('POST','/dimage/attach/user/abcdefg/games/death-stranding')
      ->assertOk();
    
    $this->disk->assertExists('user/games/death-stranding/000.jpg');
    $this->disk->assertExists('user/games/death-stranding/001.jpeg');
    $this->disk->assertExists('user/games/death-stranding/002.png');
  }
}