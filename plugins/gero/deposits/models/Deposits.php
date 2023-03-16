<?php namespace Gero\Deposits\Models;

use Model;

/**
 * Model
 */
class Deposits extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'gero_deposits_';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
    
    public $belongsTo = [
        'user' => 'RainLab\User\Models\User'
    ];
}
