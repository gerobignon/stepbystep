<?php 
/**
 * Tinkal
 * Created by alvaro.
 * User: alvaro
 * Date: 06/01/16
 * Time: 11:27 AM
 */
namespace PlanetaDelEste\Swal\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

/**
 * Class add_global_settings_table
 * @package PlanetaDelEste\Swal\Updates
 */
class AddGlobalSettingsTable extends Migration
{

    public function up()
    {
        Schema::table('planetadeleste_swal_settings', function ($table) {
        	/**
             * @var $table \Illuminate\Database\Schema\Blueprint
             */
        	$table->boolean('is_global')->default(false);
        });
    }

    public function down()
    {
        Schema::table('planetadeleste_swal_settings', function ($table) {
            /**
             * @var $table \Illuminate\Database\Schema\Blueprint
             */
            $table->dropColumn('is_global');
        });
    }

}