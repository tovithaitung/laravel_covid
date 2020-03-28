<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Keywords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('keywords', function (Blueprint $table) {
            $table->bigIncrements('keyword_id');
            $table->string('keyword')->index();
            $table->string('appid')->index();
            $table->integer('search_volumn')->nullable();
            $table->integer('kwplaner')->nullable();
            $table->integer('difficult')->nullable();
            $table->integer('allintitle')->nullable();
            $table->integer('status')->nullable();
            $table->integer('statusAhref')->nullable();
            $table->integer('statusKw')->nullable();
            $table->string('nameAhref')->nullable();
            $table->string('nameKW')->nullable();
            $table->string('url')->nullable();
            $table->string('country')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('keywords');
    }
}
