Datetime picker widget
======================
Bootstrap datetimepicker widget for Yii 2, based on [Eonasdan/bootstrap-datetimepicker](https://github.com/Eonasdan/bootstrap-datetimepicker).

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist nkovacs/yii2-datetimepicker "*"
```

or add

```
"nkovacs/yii2-datetimepicker": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
<?= \nkovacs\datetimepicker\DateTimePicker::widget([
    'name' => 'time',
]); ?>
```

or as an active field:

```php
<?= $form->field($model, 'time')->widget(\nkovacs\datetimepicker\DateTimePicker::className(), [
    'clientOptions' => [
        'format' => 'YYYY-MM-DD HH:mm',
    ],
]) ?>
```

Moment
------

This extension includes [Moment.js](http://momentjs.com/). If you wish to disable the included moment.js library
(e.g. because you have another widget that includes it or you are using it directly),
you can override the bundle configuration in config/web.php:

```php
'components' => [
    ...
    'assetManager' => [
        ...
        'bundles' => [
            ...
            'nkovacs\datetimepicker\DateTimePickerAsset' => [
                'js' => [
                    'js/bootstrap-datetimepicker.min.js',
                ],
                'depends' => [
                    'yii\web\JqueryAsset',
                    'yii\bootstrap\BootstrapAsset',
                    'app\assets\MomentAsset',
                ],
            ],
        ],
    ],
],
```

Make sure you add the asset bundle that includes Moment.js to `depends`.
