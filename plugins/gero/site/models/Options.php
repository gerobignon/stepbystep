<?php namespace Gero\Site\Models;

use Model;

/**
 * Model
 */
class Options extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'gero_site_options';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
}
