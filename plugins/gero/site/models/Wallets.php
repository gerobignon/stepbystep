<?php namespace Gero\Site\Models;

use Model;

/**
 * Model
 */
class Wallets extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'gero_site_wallet';

    /**
     * @var array Validation rules
     */
    
    public $rules = [
    ];

    public $belongsTo = [
        'user' => 'RainLab\User\Models\User'
    ];
}
