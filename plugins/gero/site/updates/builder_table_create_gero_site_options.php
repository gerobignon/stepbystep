<?php namespace Gero\Site\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateGeroSiteOptions extends Migration
{
    public function up()
    {
        Schema::create('gero_site_options', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('gero_site_options');
    }
}
