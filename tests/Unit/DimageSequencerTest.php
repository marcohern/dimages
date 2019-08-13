<?php

namespace Marcohern\Dimages\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

use Marcohern\Dimages\Lib\DimageSequencer;
use Marcohern\Dimages\Lib\Fs;

class DimageSequencerTest extends TestCase
{
  protected $disk = null;
  protected $fs;

  protected function setUp():void {
    parent::setUp();
    Storage::fake('dimages');
    $this->disk = Storage::disk('dimages');
    $this->fs = new Fs;
  }

  protected function tearDown():void {
    unset($this->fs);
    unset($this->disk);
    parent::tearDown();
  }
  public function test_get() {
    $this->disk->put('marco-hernandez/_sequence/test.image.id', 5);
    $this->disk->put('_anyone/_sequence/test.image.id', 13);

    $sequencer = new DimageSequencer($this->fs, 'image', 'test');
    $this->assertEquals(13, $sequencer->get());

    $sequencer = new DimageSequencer($this->fs, 'image','test','marco-hernandez');
    $this->assertEquals(5, $sequencer->get());

    $sequencer = new DimageSequencer($this->fs, 'image','othertest','marco-hernandez');
    $this->assertEquals(0, $sequencer->get());
  }

  public function test_put() {
    $sequencer = new DimageSequencer($this->fs, 'image', 'test');
    $sequencer->put(3);
    $this->disk->assertExists('_anyone/_sequence/test.image.id');
    $this->assertEquals($sequencer->get(), 3);
  }

  public function test_next() {
    $this->disk->put('_anyone/_sequence/othertest.image.id', 8);

    $sequencer = new DimageSequencer($this->fs, 'image', 'test');
    
    $this->assertEquals($sequencer->next(), 0);
    $this->assertEquals($sequencer->next(), 1);
    $this->assertEquals($sequencer->next(), 2);

    $sequencer = new DimageSequencer($this->fs, 'image', 'othertest');
    $this->assertEquals($sequencer->next(), 8);
    $this->assertEquals($sequencer->next(), 9);
    $this->assertEquals($sequencer->next(), 10);
  }

  public function test_drop() {
    
    $this->disk->put('_anyone/_sequence/othertest.image.id', 3);

    $this->disk->assertExists('_anyone/_sequence/othertest.image.id');

    $sequencer = new DimageSequencer($this->fs, 'image', 'othertest');
    $sequencer->drop();
    
    $this->disk->assertMissing('_anyone/_sequence/othertest.image.id');

  }
}
