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

Once the extension is installed, simply use it in your code:

```php
<?= \nkovacs\datetimepicker\DateTimePicker::widget([
    'name' => 'time',
]); ?>
```

or as an active field:

```php
<?= $form->field($model, 'time')->widget(\nkovacs\datetimepicker\DateTimePicker::className(), [
    'clientOptions' => [
        'extraFormats' => ['YYYY-MM-DD HH:mm'],
    ],
]) ?>
```

To show just a datepicker:

```php
<?= $form->field($model, 'time')->widget(\nkovacs\datetimepicker\DateTimePicker::className(), [
    'type' => 'date',
]) ?>
```

or just a timepicker:

```php
<?= $form->field($model, 'time')->widget(\nkovacs\datetimepicker\DateTimePicker::className(), [
    'type' => 'time',
]) ?>
```

The widget will load the moment locale file for `Yii::$app->language` if it can find it.

It will also use `Yii::$app->formatter->dateFormat`, `Yii::$app->formatter->timeFormat` or `Yii::$app->formatter->datetimeFormat` depending on `type`.

Use the `timestampAttribute` property of the validator in this extension to get the parsed timestamp.

Both can be overriden using the `locale` and `format` options.

Validators
----------

The extension comes with an improved datetime validator. Yii's default DateValidator cannot handle
time values with the default ICU 'medium' format. The validator adds a `type` option that specifies what
kind of value should be accepted: `date`, `time` or `datetime`.

To register the validator, add the following line to your app config file:

```php
\nkovacs\datetimepicker\DateTimeValidator::register();
```

This replaces the built-in `date` validator and adds a `time` and `datetime` validator:

```php
    ...
    ['timestamp', 'datetime'],
    ['time', 'time'],
    ['date', 'date'],
    ...
```
