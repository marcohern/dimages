<?php

namespace Marcohern\Dimages\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

use Marcohern\Dimages\Lib\DimageSequencer;

class DimageSequencerTest extends TestCase
{
  public function test_get() {
    Storage::fake('dimages');
    Storage::disk('dimages')->put('seqs/test.image.id', 5);

    $sequencer = new DimageSequencer('test','image');
    $this->assertEquals($sequencer->get(), 5);

    $sequencer = new DimageSequencer('othertest','image');
    $this->assertEquals($sequencer->get(), 0);
  }

  public function test_put() {
    Storage::fake('dimages');

    $sequencer = new DimageSequencer('test','image');
    $sequencer->put(3);
    $this->assertEquals($sequencer->get(), 3);
  }

  public function test_next() {
    Storage::fake('dimages');
    Storage::disk('dimages')->put('seqs/othertest.image.id', 8);

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
    Storage::fake('dimages');
    Storage::disk('dimages')->put('seqs/test.image.id', 3);

    Storage::disk('dimages')->assertExists('seqs/test.image.id');

    $sequencer = new DimageSequencer('test','image');
    $sequencer->drop();
    
    Storage::disk('dimages')->assertMissing('seqs/test.image.id');

  }
}
