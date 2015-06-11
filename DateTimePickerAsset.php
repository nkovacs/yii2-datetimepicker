<?php

namespace nkovacs\datetimepicker;

use yii\web\AssetBundle;

class DateTimePickerAsset extends AssetBundle
{
    public $sourcePath = '@bower/nkovacs-bootstrap-datetimepicker/';
    public $js = [
        'src/js/bootstrap-datetimepicker.js',
    ];
    public $css = [
        'build/css/bootstrap-datetimepicker.min.css',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
        'nkovacs\datetimepicker\MomentAsset',
    ];
}
