<?php namespace Gero\Site;

use System\Classes\PluginBase;
use Rainlab\User\Controllers\Users as UsersController;
use Rainlab\User\Models\User as UserModel;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
        return [
            'Gero\Site\Components\Site'           => 'site',
            'Gero\Site\Components\Loader'           => 'loader',
            'Gero\Site\Components\Validator'        => 'validator'
        ];
    }

    public function registerSettings()
    {
    }

    public function boot(){

        UserModel::extend (function ($model){
            $model->addFillable([
                'country',
                'city',
                'phone',
                'parrain',
                'idnumber',
                'idexp',
                'address',
                'validate',
                'type'
            ]);

        });

        UsersController::extendFormFields(function($form, $model, $context){
            $form->addTabFields([
                'country' => [
                    'label' => 'Country',
                    'type'  => 'text',
                    'tab'   => 'KYC'
                ],
                'city' => [
                    'label' => 'City',
                    'type'  => 'text',
                    'tab'   => 'KYC'
                ],
                'phone' => [
                    'label' => 'Whatsapp number',
                    'type'  => 'text',
                    'tab'   => 'KYC'
                ],
                'address' => [
                    'label' => 'Address',
                    'type'  => 'text',
                    'tab'   => 'KYC'
                ],
                'type' => [
                    'label' => 'Type of ID',
                    'type'  => 'text',
                    'tab'   => 'KYC'
                ],
                'idnumber' => [
                    'label' => 'ID Card Number',
                    'type'  => 'text',
                    'tab'   => 'KYC'
                ],
                'idexp' => [
                    'label' => 'ID card expiration',
                    'type'  => 'text',
                    'tab'   => 'KYC'
                ],
                'parrain' => [
                    'label' => 'Sponsor',
                    'type'  => 'text',
                    'tab'   => 'KYC'
                ],
                'validate' => [
                    'label' => 'Validation State',
                    'type'  => 'text',
                    'tab'   => 'KYC',
                    'comment'=> '1: Validated, 2:Waiting, 0:Unvalided'
                ]
            ]);
        });
    }
}
