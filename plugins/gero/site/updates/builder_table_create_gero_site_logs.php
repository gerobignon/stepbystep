<?php namespace Gero\Site\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateGeroSiteLogs extends Migration
{
    public function up()
    {
        Schema::create('gero_site_logs', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->text('msg');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('gero_site_logs');
    }
}
