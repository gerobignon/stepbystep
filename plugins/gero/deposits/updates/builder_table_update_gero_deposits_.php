<?php namespace Gero\Deposits\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateGeroDeposits extends Migration
{
    public function up()
    {
        Schema::table('gero_deposits_', function($table)
        {
            $table->string('real')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('gero_deposits_', function($table)
        {
            $table->dropColumn('real');
        });
    }
}
