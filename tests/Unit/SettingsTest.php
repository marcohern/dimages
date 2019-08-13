<?php

namespace Marcohern\Dimages\Tests\Unit;


use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

use Marcohern\Dimages\Lib\Fs;
use Marcohern\Dimages\Lib\Settings;

class SettingsTest extends TestCase {
  protected $fs;
  protected $disk;

  protected function setUp():void {
    parent::setUp();
    $this->fs = new Fs;
    $this->disk = Storage::fake('dimages');
  }

  protected function tearDown():void {
    unset($this->disk);
    unset($this->fs);
    parent::tearDown();
  }

  public function test_construct() {
    $settings = new Settings($this->fs, 'user', [], []);
    $this->assertTrue(true);
  }

  public function test_save() {
    $settings = new Settings($this->fs, 'user',
      ['single' => 1.00, 'double'=>2.00],
      ['landscape' => [375, 818], 'portrait' => [818, 375]]
    );
    $settings->save();

    $this->disk->assertExists('user/settings.cfg');
    $this->assertSame(
      ['single' => 1.00, 'double'=>2.00],
      $settings->getDensities()
    );
    $this->assertSame(
      ['landscape' => [375, 818], 'portrait' => [818, 375]],
      $settings->getProfiles()
    );
  }
}