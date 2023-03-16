<?php namespace Gero\Site\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class Migration103 extends Migration
{
    public function up()
    {
        Schema::table('users', function($table)
        {
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('idnumber')->nullable();
            $table->string('idexp')->nullable();
            $table->integer('parrain')->default(1);
            $table->integer('validate')->default(0);
        });
    }

    public function down()
    {
            $table->dropColumn('country');
            $table->dropColumn('city');
            $table->dropColumn('phone');
            $table->dropColumn('address');
            $table->dropColumn('idnumber');
            $table->dropColumn('idexp');
            $table->dropColumn('parrain');
            $table->dropColumn('validate');
    }
}