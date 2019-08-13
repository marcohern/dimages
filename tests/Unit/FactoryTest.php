<?php

namespace Marcohern\Dimages\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

use Marcohern\Dimages\Lib\Fs;
use Marcohern\Dimages\Lib\Factory;


class FactoryTest extends TestCase {
  protected $fs;
  protected $factory;
  protected $disk;

  protected function setUp():void {
    parent::setUp();
    $this->fs = new Fs;
    $this->factory = new Factory($this->fs);
    $this->disk = Storage::fake('dimages');
  }

  protected function tearDown():void {
    unset($this->disk);
    unset($this->factory);
    unset($this->fs);
    parent::tearDown();
  }

  public function test_dimageFile() {
    $dimage = $this->factory->dimageFile('dimage', 'jpg');
    $this->assertSame('_anyone', $dimage->tenant);
    $this->assertSame('_anything', $dimage->entity);
    $this->assertSame('dimage', $dimage->identity);
    $this->assertSame(0, $dimage->index);
    $this->assertSame('', $dimage->profile);
    $this->assertSame('', $dimage->density);
    $this->assertSame('jpg', $dimage->ext);

    $dimage = $this->factory->dimageFile('dimage', 'jpeg', 14);
    $this->assertSame('_anyone', $dimage->tenant);
    $this->assertSame('_anything', $dimage->entity);
    $this->assertSame('dimage', $dimage->identity);
    $this->assertSame(14, $dimage->index);
    $this->assertSame('', $dimage->profile);
    $this->assertSame('', $dimage->density);
    $this->assertSame('jpeg', $dimage->ext);

    $dimage = $this->factory->dimageFile('dimage', 'png', 14, 'group');
    $this->assertSame('_anyone', $dimage->tenant);
    $this->assertSame('group', $dimage->entity);
    $this->assertSame('dimage', $dimage->identity);
    $this->assertSame(14, $dimage->index);
    $this->assertSame('', $dimage->profile);
    $this->assertSame('', $dimage->density);
    $this->assertSame('png', $dimage->ext);

    $dimage = $this->factory->dimageFile('dimage', 'jpg', 14, 'group','lanscape');
    $this->assertSame('_anyone', $dimage->tenant);
    $this->assertSame('group', $dimage->entity);
    $this->assertSame('dimage', $dimage->identity);
    $this->assertSame(14, $dimage->index);
    $this->assertSame('lanscape', $dimage->profile);
    $this->assertSame('', $dimage->density);
    $this->assertSame('jpg', $dimage->ext);

    $dimage = $this->factory->dimageFile('dimage', 'jpeg', 14, 'group', 'lanscape', 'uikit3');
    $this->assertSame('_anyone', $dimage->tenant);
    $this->assertSame('group', $dimage->entity);
    $this->assertSame('dimage', $dimage->identity);
    $this->assertSame(14, $dimage->index);
    $this->assertSame('lanscape', $dimage->profile);
    $this->assertSame('uikit3', $dimage->density);
    $this->assertSame('jpeg', $dimage->ext);

    $dimage = $this->factory->dimageFile('dimage', 'png', 14, 'group', 'lanscape', 'uikit3', 'user');
    $this->assertSame('user', $dimage->tenant);
    $this->assertSame('group', $dimage->entity);
    $this->assertSame('dimage', $dimage->identity);
    $this->assertSame(14, $dimage->index);
    $this->assertSame('lanscape', $dimage->profile);
    $this->assertSame('uikit3', $dimage->density);
    $this->assertSame('png', $dimage->ext);
  }

  public function test_dimageFileFromPath() {
    $dimage = $this->factory->dimageFileFromPath('user/group/dimage/013.jpg');
    $this->assertSame('user', $dimage->tenant);
    $this->assertSame('group', $dimage->entity);
    $this->assertSame('dimage', $dimage->identity);
    $this->assertSame(13, $dimage->index);
    $this->assertSame('', $dimage->profile);
    $this->assertSame('', $dimage->density);
    $this->assertSame('jpg', $dimage->ext);

    $dimage = $this->factory->dimageFileFromPath('user/group/dimage/014/landscape/hdpi.jpeg');
    $this->assertSame('user', $dimage->tenant);
    $this->assertSame('group', $dimage->entity);
    $this->assertSame('dimage', $dimage->identity);
    $this->assertSame(14, $dimage->index);
    $this->assertSame('landscape', $dimage->profile);
    $this->assertSame('hdpi', $dimage->density);
    $this->assertSame('jpeg', $dimage->ext);
  }

  public function test_sequencer() {
    $sequencer = $this->factory->sequencer('dimage');
    $this->assertEquals(0, $sequencer->get());
  }

  public function test_settings() {
    $settings = $this->factory->settings('user');
    $this->assertTrue(true);
  }

  public function test_loadSettings() {
    $settings = $this->factory->settings('user');
    $settings->setDensity('ultra',5.00);
    $settings->setProfile('hd',1920,1080);
    $settings->save();
    $this->disk->assertExists('user/settings.cfg');

    $settings = $this->factory->loadSettings('user');
    $this->assertSame(5.00, $settings->density('ultra'));
    $this->assertSame([1920,1080], $settings->profile('hd'));
  }
}