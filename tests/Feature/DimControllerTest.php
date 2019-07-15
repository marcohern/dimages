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
    $response = $this->get("mh/dim/api/_status");

    $response->assertOk()->assertExactJson([
      'success' => true
    ]);
  }

  public function test_upload() {
    Storage::fake('dimages');
    $image1 = UploadedFile::fake()->image('test1.jpg');
    $image2 = UploadedFile::fake()->image('test2.jpeg');
    $image3 = UploadedFile::fake()->image('test3.png');
    
    $this
      ->json('POST',"mh/dim/api/_upload/test/image", ['image' => $image1])
      ->assertOk()
      ->assertExactJson(['index' => 0]);
    
    $this
      ->json('POST',"mh/dim/api/_upload/test/image", ['image' => $image2])
      ->assertOk()
      ->assertExactJson(['index' => 1]);
    
    $this
      ->json('POST',"mh/dim/api/_upload/test/image", ['image' => $image3])
      ->assertOk()
      ->assertExactJson(['index' => 2]);

    Storage::disk('dimages')
      ->assertExists('img/test/image/000.jpg')
      ->assertExists('img/test/image/001.jpeg')
      ->assertExists('img/test/image/002.png');
  }

  public function test_upload_NoImage_UnprocesableEntity() {
    $this
      ->json('POST',"mh/dim/api/_upload/test/image")
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

  public function test_original() {
    $disk = Storage::disk('dimages');
    $image1 = UploadedFile::fake()->image('test1.jpg' , 1920, 1080);
    $image2 = UploadedFile::fake()->image('test2.jpeg', 1920, 1080);
    $image3 = UploadedFile::fake()->image('test3.png' , 1920, 1080);
    
    $this
      ->json('POST',"mh/dim/api/_upload/test/image", ['image' => $image1])
      ->assertOk()
      ->assertExactJson(['index' => 0]);
    
    $this
      ->json('POST',"mh/dim/api/_upload/test/image", ['image' => $image2])
      ->assertOk()
      ->assertExactJson(['index' => 1]);
    
    $this
      ->json('POST',"mh/dim/api/_upload/test/image", ['image' => $image3])
      ->assertOk()
      ->assertExactJson(['index' => 2]);
    
    $this->get("mh/dim/api/test/image")->assertOk();
    $this->get("mh/dim/api/test/image/1")->assertOk();
    $this->get("mh/dim/api/test/image/2")->assertOk();

    $disk->delete('seqs/test.image.id');
    $disk->deleteDirectory('img/test/image');
  }
}
