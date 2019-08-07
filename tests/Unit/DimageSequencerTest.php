<?php

namespace Marcohern\Dimages\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

use Marcohern\Dimages\Lib\DimageSequencer;

class DimageSequencerTest extends TestCase
{
  protected $disk = null;

  protected function setUp():void {
    parent::setUp();
    Storage::fake('dimages');
    $this->disk = Storage::disk('dimages');
  }

  protected function tearDown():void {
    unset($this->disk);
    parent::tearDown();
  }
  public function test_get() {
    $this->disk->put('marco-hernandez/_sequence/test.image.id', 5);
    $this->disk->put('_global/_sequence/test.image.id', 13);

    $sequencer = new DimageSequencer('test','image');
    $this->assertEquals(13, $sequencer->get());

    $sequencer = new DimageSequencer('test','image','marco-hernandez');
    $this->assertEquals(5, $sequencer->get());

    $sequencer = new DimageSequencer('othertest','image','marco-hernandez');
    $this->assertEquals(0, $sequencer->get());
  }

  public function test_put() {
    $sequencer = new DimageSequencer('test','image');
    $sequencer->put(3);
    $this->disk->assertExists('_global/_sequence/test.image.id');
    $this->assertEquals($sequencer->get(), 3);
  }

  public function test_next() {
    $this->disk->put('_global/_sequence/othertest.image.id', 8);

    $sequencer = new DimageSequencer('test','image');
    
    $this->assertEquals($sequencer->next(), 0);
    $this->assertEquals($sequencer->next(), 1);
    $this->assertEquals($sequencer->next(), 2);

    $sequencer = new DimageSequencer('othertest','image');
    $this->assertEquals($sequencer->next(), 8);
    $this->assertEquals($sequencer->next(), 9);
    $this->assertEquals($sequencer->next(), 10);
  }

  public function test_drop() {
    
    $this->disk->put('_global/_sequence/othertest.image.id', 3);

    $this->disk->assertExists('_global/_sequence/othertest.image.id');

    $sequencer = new DimageSequencer('othertest','image');
    $sequencer->drop();
    
    $this->disk->assertMissing('_global/_sequence/othertest.image.id');

  }
}
