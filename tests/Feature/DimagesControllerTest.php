<?php

namespace Marcohern\Dimages\Tests\Feature;

use Marcohern\Dimages\Lib\DimageFile;
use Marcohern\Dimages\Lib\Dimage;
use Marcohern\Dimages\Lib\Factory;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\Passport;
use App\User;

class DimagesControllerTest extends TestCase
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
    $r = $this->json('GET',"/dimages/status");
    $r->assertOk()
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

    $this->json('GET','/dimages/marcohern@gmail.com/games/death-stranding/sources')
      ->assertOk()
      ->assertJson([
        ['tenant' => 'marcohern@gmail.com','entity'=>'games','identity'=>'death-stranding','index'=>0],
        ['tenant' => 'marcohern@gmail.com','entity'=>'games','identity'=>'death-stranding','index'=>1],
        ['tenant' => 'marcohern@gmail.com','entity'=>'games','identity'=>'death-stranding','index'=>2]
      ]);
  }

  public function test_normalize() {
    $this->disk->put('marcohern@gmail.com/games/death-stranding/001.txt','HELLO DMIAGE 1!');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/005.txt','HELLO DMIAGE 2!');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/007.txt','HELLO DMIAGE 3!');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/012.txt','HELLO DMIAGE 4!');

    $this->json('post','/dimages/marcohern@gmail.com/games/death-stranding/normalize')->assertOk();

    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/000.txt');
    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/001.txt');
    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/002.txt');
    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/003.txt');

    $this->assertEquals($this->disk->get('marcohern@gmail.com/games/death-stranding/000.txt'), 'HELLO DMIAGE 1!');
    $this->assertEquals($this->disk->get('marcohern@gmail.com/games/death-stranding/001.txt'), 'HELLO DMIAGE 2!');
    $this->assertEquals($this->disk->get('marcohern@gmail.com/games/death-stranding/002.txt'), 'HELLO DMIAGE 3!');
    $this->assertEquals($this->disk->get('marcohern@gmail.com/games/death-stranding/003.txt'), 'HELLO DMIAGE 4!');
  }

  public function test_switch() {
    $this->disk->put('marcohern@gmail.com/games/death-stranding/001.txt','HELLO ONE!');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/003.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/003/cover/hdpi.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/003/boxart/mdpi.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/003/icon/xxhdpi.txt','HELLO DIMAGE');
    $this->disk->put('marcohern@gmail.com/games/death-stranding/005.txt','HELLO FIVE!');

    $this->json('POST','/dimages/marcohern@gmail.com/games/death-stranding/switch/3/with/0')->assertOk();

    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/000.txt');
    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/000/cover/hdpi.txt');
    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/000/boxart/mdpi.txt');
    $this->disk->assertExists('marcohern@gmail.com/games/death-stranding/000/icon/xxhdpi.txt');

    $this->json('POST','/dimages/marcohern@gmail.com/games/death-stranding/switch/1/with/5')->assertOk();

    $this->assertEquals('HELLO FIVE!',$this->disk->get('marcohern@gmail.com/games/death-stranding/001.txt'));
    $this->assertEquals('HELLO ONE!',$this->disk->get('marcohern@gmail.com/games/death-stranding/005.txt'));

    $this->json('POST','/dimages/marcohern@gmail.com/games/death-stranding/switch/123/with/342')
      ->assertStatus(500);
  }
}