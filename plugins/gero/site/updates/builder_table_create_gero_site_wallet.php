<?php namespace Gero\Site\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateGeroSiteWallet extends Migration
{
    public function up()
    {
        Schema::create('gero_site_wallet', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('user_id');
            $table->string('name');
            $table->string('currency');
            $table->string('wallet');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('gero_site_wallet');
    }
}
