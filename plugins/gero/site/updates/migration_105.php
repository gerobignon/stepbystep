<?php namespace Gero\Site\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class Migration105 extends Migration
{
    public function up()
    {
        Schema::table('users', function($table)
        {
            $table->string('type')->nullable();
        });
    }

    public function down()
    {
            $table->dropColumn('type');
    }
}