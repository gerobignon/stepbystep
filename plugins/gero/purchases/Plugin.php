<?php namespace Gero\Purchases;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
        return [
            'Gero\Purchases\Components\Crypto'           => 'crypto'
        ];
    }

    public function registerSettings()
    {
    }
}
