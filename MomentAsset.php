<?php

namespace nkovacs\datetimepicker;

use yii\web\AssetBundle;

class MomentAsset extends AssetBundle
{
    public $sourcePath = '@bower/moment/min/';
    public $js = [
        'moment.min.js',
    ];
}
