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
    $this->get("/dimages2/status")
      ->assertOk()
      ->assertJson([
      'success' => true,
      'xFile' => true,
      'xUrl' => true
    ]);
  }

  public function test_session() {
    $this->get("/dimages2/session")
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

    $this->get("/dimages2")
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

    $this->get("/dimages2/marcohern@gmail.com")
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

    $this->get("/dimages2/marcohern@gmail.com/games")
      ->assertOk()
      ->assertJson([
        'death-stranding'
      ]);
  }
}