<?php namespace Gero\Deposits;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
        return [
            'Gero\Deposits\Components\Deposit'           => 'deposit'
        ];
    }

    public function registerSettings()
    {
    }
}
