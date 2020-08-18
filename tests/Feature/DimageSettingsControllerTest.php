<?php

namespace Marcohern\Dimages\Tests\Feature;

use Marcohern\Dimages\Lib\DimageFile;
use Marcohern\Dimages\Lib\Factory;
use Marcohern\Dimages\Lib\Fs;
use Marcohern\Dimages\Lib\Settings;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\Passport;
use App\User;

class DimageSettingsControllerTest extends TestCase
{
  use RefreshDatabase;
  
  protected $disk;
  protected $factory;
  protected $fs;

  protected function setUp():void {
    parent::setUp();
    Storage::fake('dimages');
    $this->disk = Storage::disk('dimages');
    $this->fs = new Fs;
    $this->factory = new Factory($this->fs);
    Passport::actingAs(factory(User::class)->create(), ['*']);
  }

  protected function tearDown():void {
    unset($this->disk);
    unset($this->fs);
    unset($this->factory);
    parent::tearDown();
  }

  protected function createTestSettings(): void {
    $settings = new Settings($this->fs, 'marco',[
      'ldpi' => 0.75,
      'mdpi' => 1.00,
      'hdpi' => 1.50
    ],[
      'icon' => [32,32],
      'landscape' => [1920,1080],
      'portrait' => [1080,1920]
    ]);
    $this->disk->put('marco/settings.cfg', serialize($settings));
  }

  public function test_get() {
    $this->json('GET','dimagesettings/marco')->assertOk();

    $this->createTestSettings();

    $this->json('GET','dimagesettings/marco')
    ->assertOk()
    ->assertExactJson([
      'settings' => [
        'densities' => [
          'ldpi' => 0.75,
          'mdpi' => 1.00,
          'hdpi' => 1.50
        ],
        'profiles' => [
          'icon' => [32,32],
          'landscape' => [1920,1080],
          'portrait' => [1080,1920]
        ],
        'tenant' => 'marco'
      ]
    ]);
  }

  public function test_storeDensity() {

    $this->createTestSettings();

    $this->json('POST', 'dimagesettings/marco/density', ['name' => 'uikit3','value' => 4.00])
      ->assertOk();

    $settings = $this->factory->loadSettings('marco');
    

    $this->assertEquals(new Settings($this->fs, 'marco', [
      'ldpi' => 0.75,
      'mdpi' => 1.00,
      'hdpi' => 1.50,
      'uikit3' => 4.00,
    ],[
      'icon' => [32,32],
      'landscape' => [1920,1080],
      'portrait' => [1080,1920]    
    ]), $settings);
  }

  public function test_storeProfile() {

    $this->createTestSettings();

    $this->json('POST', 'dimagesettings/marco/profile', [
      'name' => 'cover',
      'width' => 400,
      'height' => 300
    ])->assertOk();

    $settings = $this->factory->loadSettings('marco');

    $this->assertEquals(new Settings($this->fs, 'marco', [
      'ldpi' => 0.75,
      'mdpi' => 1.00,
      'hdpi' => 1.50,
    ],[
      'icon' => [32,32],
      'landscape' => [1920,1080],
      'portrait' => [1080,1920],
      'cover' => [400,300]
    ]), $settings);
  }

  public function test_deleteDensity() {
    $this->createTestSettings();

    $this->json('DELETE','dimagesettings/marco/density/ldpi')
      ->assertOk();

    $settings = $this->factory->loadSettings('marco');
    $this->assertEquals(new Settings($this->fs, 'marco', [
      'mdpi' => 1.00,
      'hdpi' => 1.50,
    ],[
      'icon' => [32,32],
      'landscape' => [1920,1080],
      'portrait' => [1080,1920]
    ]), $settings);
  }

  public function test_deleteProfile() {
    $this->createTestSettings();

    $this->json('DELETE','dimagesettings/marco/profile/landscape')
      ->assertOk();

    $settings = $this->factory->loadSettings('marco');
    $this->assertEquals(new Settings($this->fs, 'marco', [
      'ldpi' => 0.75,
      'mdpi' => 1.00,
      'hdpi' => 1.50,
    ],[
      'icon' => [32,32],
      'portrait' => [1080,1920]
    ]), $settings);
  }

  public function test_reset() {
    $this->createTestSettings();

    $this->json('POST','dimagesettings/marco/reset')->assertOk();

    $settings = $this->factory->loadSettings('marco');
    $this->assertEquals(new Settings($this->fs, 'marco', [
      'ldpi' => 0.75,
      'mdpi' => 1.00,
      'hdpi' => 1.50,
      'xhdpi' => 2.00,
      'xxhdpi' => 3.00,
      'xxxhdpi' => 4.00,
      'uikit1' => 1.00,
      'uikit2' => 2.00,
      'uikit3' => 3.00,
    ],[
      'landscape'          => [375, 818],
      'portrait'           => [818, 375],
      'launcher-icons'     => [48, 48],
      'actionbar-icons'    => [24, 24],
      'small-icons'        => [16, 16],
      'notification-icons' => [22, 22],
    ]), $settings);
  }
}