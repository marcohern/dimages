<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMhnDimagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mhn_dimages', function (Blueprint $table) {
            $table->increments('id');
            
            $table->enum('attached',['TRUE','FALSE'])->default('TRUE')->index();
            $table->string('domain',32)->default('global');
            $table->string('slug',128);
            $table->integer('index')->default(0);
            $table->string('profile',32)->default('original');
            $table->string('density',32)->default('original');
            
            $table->string('filename',128);
            $table->string('type',64); //mime type
            $table->integer('width');
            $table->integer('height');
            $table->integer('parent_id')->nullable()->index();

            $table->timestamps();
        });
        //DB::statement("ALTER TABLE mhn_dimages ADD bytes LONGBLOB NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mhn_dimages');
    }
}
