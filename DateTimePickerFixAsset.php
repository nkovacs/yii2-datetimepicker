<?php

namespace nkovacs\datetimepicker;

use yii\web\AssetBundle;

class DateTimePickerFixAsset extends AssetBundle
{
    public $sourcePath = '@nkovacs/datetimepicker/assets';
    public $js = [
        'js/dtp.js',
    ];
    public $css = [];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
