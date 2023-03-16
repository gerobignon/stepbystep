<?php namespace Gero\Deposits\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateGeroDeposits extends Migration
{
    public function up()
    {
        Schema::create('gero_deposits_', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('user_id');
            $table->double('amount', 10, 0);
            $table->string('ref');
            $table->string('via');
            $table->string('status');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('gero_deposits_');
    }
}
