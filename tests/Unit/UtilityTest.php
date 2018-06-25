<?php
namespace Marcohern\Dimages\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Marcohern\Dimages\Lib\Utility;
use stdClass;

class UtilityTest extends TestCase {

    public function test_idx_valid() {
        $idx1 = Utility::idx(8);
        $idx2 = Utility::idx(24);
        $idx3 = Utility::idx(256);
        $this->assertEquals($idx1, "008");
        $this->assertEquals($idx2, "024");
        $this->assertEquals($idx3, "256");
    }

    public function test_idx_more() {
        $idx = Utility::idx(1024);
        $this->assertEquals($idx, "1024");
    }

    public function test_idx_null() {
        $idx = Utility::idx(null);
        $this->assertEquals($idx, "000");
    }
}