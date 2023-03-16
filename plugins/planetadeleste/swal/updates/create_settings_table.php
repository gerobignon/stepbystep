<?php namespace PlanetaDelEste\Swal\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateSettingsTable extends Migration
{

    public function up()
    {
        Schema::create('planetadeleste_swal_settings', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();
            $table->string('title', 100)->unique();
            $table->text('options');
        });
    }

    public function down()
    {
        Schema::dropIfExists('planetadeleste_swal_settings');
    }

}
