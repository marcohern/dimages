<?php

namespace Marcohern\Dimages\Tests\Feature;

use Marcohern\Dimages\Lib\Files\DimageFile;

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

  public function test_status() {
    $this->get("/dimages/status")
      ->assertOk()
      ->assertJson([
      'success' => true,
      'xFile' => true
    ]);
  }

  public function test_session() {
    $this->get("/dimages/session")
      ->assertOk()
      ->assertJson([
      'session' => true
    ]);
  }

  public function test_tenants() {
    $this->disk->put('marcohern@gmail.com/games/death-stranding/000.txt','HELLO DMIAGE!');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/001.txt','HELLO DMIAGE!');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/002.txt','HELLO DMIAGE!');
    $this->disk->put('giovanni/games/darksouls-3/000.txt','HELLO DMIAGE!');

    $this->get("/dimages")
      ->assertOk()
      ->assertJson([
        'giovanni',
        'marcohern@gmail.com'
      ]);
  }

  public function test_entities() {
    $this->disk->put('marcohern@gmail.com/games/death-stranding/000.txt','HELLO DMIAGE!');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/001.txt','HELLO DMIAGE!');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/002.txt','HELLO DMIAGE!');
    $this->disk->put('giovanni/games/darksouls-3/000.txt','HELLO DMIAGE!');

    $this->get("/dimages/marcohern@gmail.com")
      ->assertOk()
      ->assertJson([
        'games'
      ]);
  }

  public function test_identities() {
    $this->disk->put('marcohern@gmail.com/games/death-stranding/000.txt','HELLO DMIAGE!');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/001.txt','HELLO DMIAGE!');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/002.txt','HELLO DMIAGE!');
    $this->disk->put('giovanni/games/darksouls-3/000.txt','HELLO DMIAGE!');

    $this->get("/dimages/marcohern@gmail.com/games")
      ->assertOk()
      ->assertJson([
        'death-stranding'
      ]);
  }

  public function test_sources() {
    $this->disk->put('marcohern@gmail.com/games/death-stranding/000.txt','HELLO DMIAGE!');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/001.txt','HELLO DMIAGE!');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/002.txt','HELLO DMIAGE!');
    $this->disk->put('giovanni/games/darksouls-3/000.txt','HELLO DMIAGE!');

    $this->get("/dimages/marcohern@gmail.com/games/death-stranding/sources")
      ->assertOk()
      ->assertJson([
        ['tenant' => 'marcohern@gmail.com','entity'=>'games','identity'=>'death-stranding','index'=>0],
        ['tenant' => 'marcohern@gmail.com','entity'=>'games','identity'=>'death-stranding','index'=>1],
        ['tenant' => 'marcohern@gmail.com','entity'=>'games','identity'=>'death-stranding','index'=>2]
      ]);
  }

  public function test_store() {
    $image1 = UploadedFile::fake()->image('test1.jpg');
    $image2 = UploadedFile::fake()->image('test2.jpeg');
    $image3 = UploadedFile::fake()->image('test3.png');

    $this
      ->json('POST',"/dimages/user/test/image", ['image' => $image1])
      ->assertOk()
      ->assertExactJson(['index' => 0]);
    
    $this
      ->json('POST',"/dimages/user/test/image", ['image' => $image2])
      ->assertOk()
      ->assertExactJson(['index' => 1]);
    
    $this
      ->json('POST',"/dimages/user/test/image", ['image' => $image3])
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
      ->json('POST',"/dimages/user/test/image", ['image' => $image1])
      ->assertOk()
      ->assertExactJson(['index' => 0]);
    
    $this
      ->json('POST',"/dimages/user/test/image", ['image' => $image2])
      ->assertOk()
      ->assertExactJson(['index' => 1]);
    
    $this
      ->json('POST',"/dimages/user/test/image", ['image' => $image3])
      ->assertOk()
      ->assertExactJson(['index' => 2]);
    
    $this->disk->assertExists('user/test/image/000.jpg');
    $this->disk->assertExists('user/test/image/001.jpeg');
    $this->disk->assertExists('user/test/image/002.png');

  }
}