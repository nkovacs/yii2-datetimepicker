<?php

namespace nkovacs\datetimepicker;

use Yii;
use yii\validators\Validator;

/**
 * An improved version of Yii's DateValidator that supports parsing time as well.
 */
class DateTimeValidator extends \yii\validators\DateValidator
{
    /**
     * @var string the type of value to accept: 'date', 'time' or 'datetime'.
     * This is only used when the format is ambiguous (i.e. one of the ICU short formats),
     * otherwise it is ignored, and the format dictates what is accepted.
     */
    public $type = 'datetime';

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->format === null) {
            switch ($this->type) {
                case 'date':
                    $this->format = Yii::$app->formatter->dateFormat;
                    break;
                case 'time':
                    $this->format = Yii::$app->formatter->timeFormat;
                    break;
                case 'datetime':
                    $this->format = Yii::$app->formatter->datetimeFormat;
                    break;
            }
        }

        // if $this->format is a short format,
        // convert it to a pattern, so that DateValidator will respect $this->type.
        $this->format = FormatConverter::convertIcuShortFormatToPattern($this->format, $this->type, $this->locale);

        parent::init();
    }

    /**
     * Replace built-in `date` validator and add `time` and `datetime` validator.
     */
    public static function register()
    {
        Validator::$builtInValidators['date'] = [
            'class' => static::className(),
            'type' => 'date',
        ];
        Validator::$builtInValidators['time'] = [
            'class' => static::className(),
            'type' => 'time',
        ];
        Validator::$builtInValidators['datetime'] = static::className();
    }
}
