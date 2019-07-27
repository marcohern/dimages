<?php

namespace Marcohern\Dimages\Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\Passport;
use App\User;

class DimControllerTest extends TestCase
{
  /**
   * Test dimage status
   *
   * @return void
   */
  public function test_status()
  {
    $response = $this->get("/dimages/status");

    $response->assertOk()->assertJson([
      'success' => true,
      'xFileName' => true,
      'xUrl' => true
    ]);
  }

  public function test_store() {
    Passport::actingAs(factory(User::class)->create(),['*']);
    Storage::fake('dimages');
    $image1 = UploadedFile::fake()->image('test1.jpg');
    $image2 = UploadedFile::fake()->image('test2.jpeg');
    $image3 = UploadedFile::fake()->image('test3.png');
    
    $this
      ->json('POST',"/dimages/test/image", ['image' => $image1])
      ->assertOk()
      ->assertExactJson(['index' => 0]);
    
    $this
      ->json('POST',"/dimages/test/image", ['image' => $image2])
      ->assertOk()
      ->assertExactJson(['index' => 1]);
    
    $this
      ->json('POST',"/dimages/test/image", ['image' => $image3])
      ->assertOk()
      ->assertExactJson(['index' => 2]);

    Storage::disk('dimages')
      ->assertExists('img/test/image/000.jpg')
      ->assertExists('img/test/image/001.jpeg')
      ->assertExists('img/test/image/002.png');
  }

  public function test_store_NoImage_UnprocesableEntity() {
    Passport::actingAs(factory(User::class)->create(),['*']);
    $this
      ->json('POST',"/dimages/test/image")
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
    Passport::actingAs(factory(User::class)->create(),['*']);
    Storage::fake('dimages');
    $disk = Storage::disk('dimages');
    $image1 = UploadedFile::fake()->image('test1.jpg' , 1920, 1080);
    $image2 = UploadedFile::fake()->image('test2.jpeg', 1920, 1080);
    $image3 = UploadedFile::fake()->image('test3.png' , 1920, 1080);
    
    $this
      ->json('POST',"/dimages/konami/contra-3", ['image' => $image1])
      ->assertOk()
      ->assertExactJson(['index' => 0]);
    
    $this
      ->json('POST',"/dimages/konami/contra-3", ['image' => $image2])
      ->assertOk()
      ->assertExactJson(['index' => 1]);
    
    $this
      ->json('POST',"/dimages/konami/contra-3", ['image' => $image3])
      ->assertOk()
      ->assertExactJson(['index' => 2]);
    
    $this->get("/dimages/konami/contra-3/ref/hdpi")->assertOk();
    $this->get("/dimages/konami/contra-3/ref/hdpi/1")->assertOk();
    $this->get("/dimages/konami/contra-3/ref/hdpi/2")->assertOk();

  }

  public function test_source() {
    Passport::actingAs(factory(User::class)->create(),['*']);
    Storage::fake('dimages');
    $disk = Storage::disk('dimages');
    $image1 = UploadedFile::fake()->image('test1.jpg' , 1920, 1080);
    $image2 = UploadedFile::fake()->image('test2.jpeg', 1920, 1080);
    $image3 = UploadedFile::fake()->image('test3.png' , 1920, 1080);
    
    $this
      ->json('POST',"/dimages/test/image", ['image' => $image1])
      ->assertOk()
      ->assertExactJson(['index' => 0]);
    
    $this
      ->json('POST',"/dimages/test/image", ['image' => $image2])
      ->assertOk()
      ->assertExactJson(['index' => 1]);
    
    $this
      ->json('POST',"/dimages/test/image", ['image' => $image3])
      ->assertOk()
      ->assertExactJson(['index' => 2]);
    
    $this->get("/dimages/test/image")->assertOk();
    $this->get("/dimages/test/image/1")->assertOk();
    $this->get("/dimages/test/image/2")->assertOk();

  }

  public function test_update() {
    Passport::actingAs(factory(User::class)->create(),['*']);
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

    $response = $this->json('POST',"/dimages/bands/kiss/1", ['image' => $image4 ]);
    $response->assertOk();

    $disk->assertExists ('img/bands/kiss/001.jpeg');
    $disk->assertMissing('img/bands/kiss/001.jpg');
    $disk->assertMissing('img/bands/kiss/001.cover.hdpi.jpg');
    $disk->assertMissing('img/bands/kiss/001.cover.mdpi.jpg');
  }

  public function test_images() {
    Passport::actingAs(factory(User::class)->create(),['*']);
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

    $this->json('GET',"/dimages/bands/kiss/images")
      ->assertOk()
      ->assertExactJson([
        '/dimages/bands/kiss/0',
        '/dimages/bands/kiss/1',
        '/dimages/bands/kiss/2'
      ]);
  }

  public function test_switch() {
    Passport::actingAs(factory(User::class)->create(),['*']);
    Storage::fake('dimages');
    $disk = Storage::disk('dimages');
    $image1 = UploadedFile::fake()->image('test1.jpg' , 1920, 1080);
    $image2 = UploadedFile::fake()->image('test2.jpeg', 1920, 1080);
    $image3 = UploadedFile::fake()->image('test3.png' , 1920, 1080);
    
    $this
      ->json('POST',"/dimages/test/image", ['image' => $image1])
      ->assertOk()
      ->assertExactJson(['index' => 0]);
    
    $this
      ->json('POST',"/dimages/test/image", ['image' => $image2])
      ->assertOk()
      ->assertExactJson(['index' => 1]);
    
    $this
      ->json('POST',"/dimages/test/image", ['image' => $image3])
      ->assertOk()
      ->assertExactJson(['index' => 2]);
    
    $this->post("/dimages/test/image/switch/1/with/2")->assertOk();

    $disk->assertExists('img/test/image/000.jpg');
    $disk->assertExists('img/test/image/001.png');
    $disk->assertExists('img/test/image/002.jpeg');
  }

  public function test_normalize() {
    Passport::actingAs(factory(User::class)->create(),['*']);
    Storage::fake('dimages');
    $image1 = UploadedFile::fake()->image('test1.jpg' , 1920, 1080);
    $image2 = UploadedFile::fake()->image('test2.jpeg', 1920, 1080);
    $image3 = UploadedFile::fake()->image('test3.png' , 1920, 1080);
    
    $this
      ->json('POST',"/dimages/test/image", ['image' => $image1])
      ->assertOk()
      ->assertExactJson(['index' => 0]);
    
    $this
      ->json('POST',"/dimages/test/image", ['image' => $image2])
      ->assertOk()
      ->assertExactJson(['index' => 1]);
    
    $this
      ->json('POST',"/dimages/test/image", ['image' => $image3])
      ->assertOk()
      ->assertExactJson(['index' => 2]);

    $disk = Storage::disk('dimages');

    $disk->assertExists('img/test/image/001.jpeg');
    $disk->delete('img/test/image/001.jpeg');

    $disk->assertExists('img/test/image/000.jpg');
    $disk->assertExists('img/test/image/002.png');
    
    $this->json('POST',"/dimages/test/image/normalize")->assertOk();

    $disk->assertExists('img/test/image/000.jpg');
    $disk->assertExists('img/test/image/001.png');
  }

  public function test_move() {
    Passport::actingAs(factory(User::class)->create(),['*']);
    Storage::fake('dimages');
    $disk = Storage::disk('dimages');
    $image1 = UploadedFile::fake()->image('test1.jpg' , 1920, 1080);
    $image2 = UploadedFile::fake()->image('test2.jpeg', 1920, 1080);
    $image3 = UploadedFile::fake()->image('test3.png' , 1920, 1080);
    
    $this
      ->json('POST',"dimages/staging/a639b1e1789f4be", ['image' => $image1])
      ->assertOk()
      ->assertExactJson(['index' => 0]);
    
    $this
      ->json('POST',"dimages/staging/a639b1e1789f4be", ['image' => $image2])
      ->assertOk()
      ->assertExactJson(['index' => 1]);
    
    $this
      ->json('POST',"/dimages/staging/a639b1e1789f4be", ['image' => $image3])
      ->assertOk()
      ->assertExactJson(['index' => 2]);

    $disk->assertExists('img/staging/a639b1e1789f4be/000.jpg');
    $disk->assertExists('img/staging/a639b1e1789f4be/001.jpeg');
    $disk->assertExists('img/staging/a639b1e1789f4be/002.png');

    $this->json('POST',"/dimages/move/staging/a639b1e1789f4be/to/konami/contra-3")
      ->assertOk();

    $disk->assertExists('img/konami/contra-3/000.jpg');
    $disk->assertExists('img/konami/contra-3/001.jpeg');
    $disk->assertExists('img/konami/contra-3/002.png');
    $disk->assertMissing('img/staging/a639b1e1789f4be');
  }
}
