<?php

namespace Marcohern\Dimages\Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Storage;

class DimControllerTest extends TestCase
{
  /**
   * Test dimage status
   *
   * @return void
   */
  public function test_status()
  {
    $response = $this->get("mh/dim/api/status");

    $response->assertOk()->assertJson([
      'success' => true,
      'xFileName' => true,
      'xUrl' => true
    ]);
  }

  public function test_store() {
    Storage::fake('dimages');
    $image1 = UploadedFile::fake()->image('test1.jpg');
    $image2 = UploadedFile::fake()->image('test2.jpeg');
    $image3 = UploadedFile::fake()->image('test3.png');
    
    $this
      ->json('POST',"mh/dim/api/test/image", ['image' => $image1])
      ->assertOk()
      ->assertExactJson(['index' => 0]);
    
    $this
      ->json('POST',"mh/dim/api/test/image", ['image' => $image2])
      ->assertOk()
      ->assertExactJson(['index' => 1]);
    
    $this
      ->json('POST',"mh/dim/api/test/image", ['image' => $image3])
      ->assertOk()
      ->assertExactJson(['index' => 2]);

    Storage::disk('dimages')
      ->assertExists('img/test/image/000.jpg')
      ->assertExists('img/test/image/001.jpeg')
      ->assertExists('img/test/image/002.png');
  }

  public function test_store_NoImage_UnprocesableEntity() {
    $this
      ->json('POST',"mh/dim/api/test/image")
      ->assertStatus(422)
      ->assertExactJson([
        'message' => 'The given data was invalid.',
        'errors' => [
          'image' => [
            'The image field is required.'
          ]
        ]
      ]);
  }

  public function test_derive() {
    Storage::fake('dimages');
    $disk = Storage::disk('dimages');
    $image1 = UploadedFile::fake()->image('test1.jpg' , 1920, 1080);
    $image2 = UploadedFile::fake()->image('test2.jpeg', 1920, 1080);
    $image3 = UploadedFile::fake()->image('test3.png' , 1920, 1080);
    
    $this
      ->json('POST',"mh/dim/api/konami/contra-3", ['image' => $image1])
      ->assertOk()
      ->assertExactJson(['index' => 0]);
    
    $this
      ->json('POST',"mh/dim/api/konami/contra-3", ['image' => $image2])
      ->assertOk()
      ->assertExactJson(['index' => 1]);
    
    $this
      ->json('POST',"mh/dim/api/konami/contra-3", ['image' => $image3])
      ->assertOk()
      ->assertExactJson(['index' => 2]);
    
    $this->get("mh/dim/api/konami/contra-3/ref/hdpi")->assertOk();
    $this->get("mh/dim/api/konami/contra-3/ref/hdpi/1")->assertOk();
    $this->get("mh/dim/api/konami/contra-3/ref/hdpi/2")->assertOk();

  }

  public function test_source() {
    Storage::fake('dimages');
    $disk = Storage::disk('dimages');
    $image1 = UploadedFile::fake()->image('test1.jpg' , 1920, 1080);
    $image2 = UploadedFile::fake()->image('test2.jpeg', 1920, 1080);
    $image3 = UploadedFile::fake()->image('test3.png' , 1920, 1080);
    
    $this
      ->json('POST',"mh/dim/api/test/image", ['image' => $image1])
      ->assertOk()
      ->assertExactJson(['index' => 0]);
    
    $this
      ->json('POST',"mh/dim/api/test/image", ['image' => $image2])
      ->assertOk()
      ->assertExactJson(['index' => 1]);
    
    $this
      ->json('POST',"mh/dim/api/test/image", ['image' => $image3])
      ->assertOk()
      ->assertExactJson(['index' => 2]);
    
    $this->get("mh/dim/api/test/image")->assertOk();
    $this->get("mh/dim/api/test/image/1")->assertOk();
    $this->get("mh/dim/api/test/image/2")->assertOk();

  }

  public function test_update() {
    Storage::fake('dimages');
    $disk = Storage::disk('dimages');
    $image1 = UploadedFile::fake()->image('test1.jpeg' , 1920, 1080);
    $image2 = UploadedFile::fake()->image('test2.jpg', 1920, 1080);
    $image3 = UploadedFile::fake()->image('test3.png' , 1920, 1080);

    $disk->putFileAs('img/bands/kiss', $image1, '000.jpeg');
    $disk->putFileAs('img/bands/kiss', $image2, '001.jpg');
    $disk->putFileAs('img/bands/kiss', $image2, '001.cover.hdpi.jpg');
    $disk->putFileAs('img/bands/kiss', $image2, '001.cover.mdpi.jpg');
    $disk->putFileAs('img/bands/kiss', $image3, '002.png');

    $image4 = UploadedFile::fake()->image('test4.jpeg' , 1920, 1080);
    
    $disk->assertExists('img/bands/kiss/001.jpg');
    $disk->assertExists('img/bands/kiss/001.cover.hdpi.jpg');
    $disk->assertExists('img/bands/kiss/001.cover.mdpi.jpg');

    $response = $this->json('POST',"mh/dim/api/bands/kiss/1", ['image' => $image4 ]);
    $response->assertOk();

    $disk->assertExists ('img/bands/kiss/001.jpeg');
    $disk->assertMissing('img/bands/kiss/001.jpg');
    $disk->assertMissing('img/bands/kiss/001.cover.hdpi.jpg');
    $disk->assertMissing('img/bands/kiss/001.cover.mdpi.jpg');
  }

  public function test_switch() {
    Storage::fake('dimages');
    $disk = Storage::disk('dimages');
    $image1 = UploadedFile::fake()->image('test1.jpg' , 1920, 1080);
    $image2 = UploadedFile::fake()->image('test2.jpeg', 1920, 1080);
    $image3 = UploadedFile::fake()->image('test3.png' , 1920, 1080);
    
    $this
      ->json('POST',"mh/dim/api/test/image", ['image' => $image1])
      ->assertOk()
      ->assertExactJson(['index' => 0]);
    
    $this
      ->json('POST',"mh/dim/api/test/image", ['image' => $image2])
      ->assertOk()
      ->assertExactJson(['index' => 1]);
    
    $this
      ->json('POST',"mh/dim/api/test/image", ['image' => $image3])
      ->assertOk()
      ->assertExactJson(['index' => 2]);
    
    $this->post("mh/dim/api/test/image/switch/1/with/2")->assertOk();

    $disk->assertExists('img/test/image/000.jpg');
    $disk->assertExists('img/test/image/001.png');
    $disk->assertExists('img/test/image/002.jpeg');
  }

  public function test_normalize() {
    Storage::fake('dimages');
    $image1 = UploadedFile::fake()->image('test1.jpg' , 1920, 1080);
    $image2 = UploadedFile::fake()->image('test2.jpeg', 1920, 1080);
    $image3 = UploadedFile::fake()->image('test3.png' , 1920, 1080);
    
    $this
      ->json('POST',"mh/dim/api/test/image", ['image' => $image1])
      ->assertOk()
      ->assertExactJson(['index' => 0]);
    
    $this
      ->json('POST',"mh/dim/api/test/image", ['image' => $image2])
      ->assertOk()
      ->assertExactJson(['index' => 1]);
    
    $this
      ->json('POST',"mh/dim/api/test/image", ['image' => $image3])
      ->assertOk()
      ->assertExactJson(['index' => 2]);

    $disk = Storage::disk('dimages');

    $disk->assertExists('img/test/image/001.jpeg');
    $disk->delete('img/test/image/001.jpeg');

    $disk->assertExists('img/test/image/000.jpg');
    $disk->assertExists('img/test/image/002.png');
    
    $this->json('POST',"mh/dim/api/test/image/normalize")->assertOk();

    $disk->assertExists('img/test/image/000.jpg');
    $disk->assertExists('img/test/image/001.png');
  }
}
