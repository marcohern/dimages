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
    $image1 = UploadedFile::fake()->image('test1.jpg', 1920, 1080);
    $image2 = UploadedFile::fake()->image('test2.jpeg', 1920, 1080);
    $image3 = UploadedFile::fake()->image('test3.png', 1920, 1080);

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
    $image1 = UploadedFile::fake()->image('test1.jpg', 1920, 1080);
    $image2 = UploadedFile::fake()->image('test2.jpeg', 1920, 1080);
    $image3 = UploadedFile::fake()->image('test3.png', 1920, 1080);
    
    $this->disk->putFileAs('user/_staging/abcdefg', $image1, '000.jpg');
    $this->disk->putFileAs('user/_staging/abcdefg', $image2, '001.jpeg');
    $this->disk->putFileAs('user/_staging/abcdefg', $image3, '002.png');

    $this
      ->json('POST','/dimage/attach/user/abcdefg/games/death-stranding')
      ->assertOk();
    
    $this->disk->assertExists('user/games/death-stranding/000.jpg');
    $this->disk->assertExists('user/games/death-stranding/001.jpeg');
    $this->disk->assertExists('user/games/death-stranding/002.png');
  }

  public function test_derive() {
    $image1 = UploadedFile::fake()->image('test1.jpg', 1920, 1080);
    $image2 = UploadedFile::fake()->image('test2.jpeg', 1920, 1080);
    $image3 = UploadedFile::fake()->image('test3.png', 1920, 1080);
    
    $this->disk->putFileAs('user/games/death-stranding', $image1, '000.jpg');
    $this->disk->putFileAs('user/games/death-stranding', $image2, '001.jpeg');
    $this->disk->putFileAs('user/games/death-stranding', $image3, '002.png');

    $this->json('GET','/dimage/user/games/death-stranding/landscape/ldpi')->assertOk();
    $this->disk->assertExists('user/games/death-stranding/000/landscape/ldpi.jpg');

    $this->json('GET','/dimage/user/games/death-stranding/landscape/mdpi/1')->assertOk();
    $this->disk->assertExists('user/games/death-stranding/001/landscape/mdpi.jpeg');

    $this->json('GET','/dimage/user/games/death-stranding/landscape/hdpi/2')->assertOk();
    $this->disk->assertExists('user/games/death-stranding/002/landscape/hdpi.png');

    $this->json('GET','/dimage/user/games/death-stranding/landscape/hdpi/5')->assertNotFound();
    $this->json('GET','/dimage/user/games/death-stranding/fakeprofile/hdpi')->assertNotFound();
    $this->json('GET','/dimage/user/games/death-stranding/landscape/fakedpi')->assertNotFound();
  }

  public function test_update() {
    $image1 = UploadedFile::fake()->image('test1.jpg', 1920, 1080);
    $image2 = UploadedFile::fake()->image('test2.jpeg', 1920, 1080);
    $image3 = UploadedFile::fake()->image('test3.png', 1920, 1080);
    $image4 = UploadedFile::fake()->image('test4.jpeg', 1920, 1080);
    
    $this->disk->putFileAs('user/games/death-stranding', $image1, '000.jpg');
    $this->disk->putFileAs('user/games/death-stranding', $image2, '001.jpeg');
    $this->disk->putFileAs('user/games/death-stranding', $image3, '002.png');

    $this->json('POST','/dimage/user/games/death-stranding/2',[
      'image' => $image4
    ])->assertOk();
    $this->disk->assertMissing('user/games/death-stranding/002.png');
    $this->disk->assertExists('user/games/death-stranding/002.jpeg');
  }

  public function test_destroyIndex() {
    $image1 = UploadedFile::fake()->image('test1.jpg', 1920, 1080);
    $image2 = UploadedFile::fake()->image('test2.jpeg', 1920, 1080);
    $image3 = UploadedFile::fake()->image('test3.png', 1920, 1080);
    
    $this->disk->putFileAs('user/games/death-stranding', $image1, '000.jpg');
    $this->disk->putFileAs('user/games/death-stranding/000/landscape', $image2, 'ldpi.jpeg');
    $this->disk->putFileAs('user/games/death-stranding/000/portrait', $image3, 'mdpi.png');

    $this->disk->putFileAs('user/games/death-stranding', $image1, '002.jpg');
    $this->disk->putFileAs('user/games/death-stranding/002/landscape', $image2, 'ldpi.jpeg');
    $this->disk->putFileAs('user/games/death-stranding/002/portrait', $image3, 'mdpi.png');

    $this->json('DELETE','/dimage/user/games/death-stranding/0')->assertOk();
    $this->disk->assertMissing('user/games/death-stranding/000.jpg');
    $this->disk->assertMissing('user/games/death-stranding/000/landscape/ldpi.jpeg');
    $this->disk->assertMissing('user/games/death-stranding/000/portrait/mdpi.png');

    $this->json('DELETE','/dimage/user/games/death-stranding/2')->assertOk();
    $this->disk->assertMissing('user/games/death-stranding/002.jpg');
    $this->disk->assertMissing('user/games/death-stranding/002/landscape/ldpi.jpeg');
    $this->disk->assertMissing('user/games/death-stranding/002/portrait/mdpi.png');
  }
}