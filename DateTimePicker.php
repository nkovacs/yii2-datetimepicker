<?php

namespace nkovacs\datetimepicker;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;

class DateTimePicker extends \yii\widgets\InputWidget
{
    /**
     * picker with date only
     */
    const TYPE_DATE = 'date';

    /**
     * picker with time only
     */
    const TYPE_TIME = 'time';

    /**
     * picker with both date and time
     */
    const TYPE_DATETIME = 'datetime';

    /**
     * @var array the options for the underlying Bootstrap JS plugin.
     */
    public $clientOptions = [];
    /**
     * @var array the event handlers for the underlying Bootstrap JS plugin.
     * The available events are:
     *     - dp.change: Fires when the datepicker changes or updates the date
     *     - dp.show: Fires when the widget is shown
     *     - dp.hide: Fires when the widget is hidden
     *     - dp.error: Fires when Moment cannot parse the date or when the timepicker cannot change because of a `disabledDates` setting.
     * NOTE: if an addon is used, these are registered on the input group,
     * not the input, so you should only use the dp events.
     */
    public $clientEvents = [];

    /**
     * @var false|string|string[] appended addon(s)
     * '{datepicker}' will be replaced with the datepicker button
     * '{clear}' will be replaced with the clear button
     */
    public $append = '{datepicker}';
    /**
     * @var false|string|string[] prepended addon(s)
     * '{datepicker}' will be replaced with the datepicker button
     * '{clear}' will be replaced with the clear button
     */
    public $prepend = false;

    /**
     * @var string html code for the date picker button
     */
    public $datePickerButton = '<span class="glyphicon glyphicon-calendar"></span>';

    /**
     * @var string html code for the clear button
     */
    public $clearButton = '<span class="glyphicon glyphicon-remove"></span>';

    /**
     * @var mixed datetimepicker locale
     * If null, it will use Yii::$app->language.
     * If a string, that locale will be used.
     * If false, locale will not be set and no locale files will be included.
     * @note the format will be overriden by Yii::$app->formatter's format if $format is null.
     */
    public $locale = null;

    /**
     * @var mixed datetimepicker format
     * If false, format will not be overriden, and will use moment's default for the locale.
     * If null, it will use the `timeFormat`, `dateFormat` or `datetimeFormat` from Yii::$app->formatter,
     * depending on $type.
     * If a string, it can either be an ICU format, a php format prefixed with `php:`,
     * or a moment format prefixed with `moment:`
     * @note the ICU formats "short", "medium", "long" and "full" do not
     * specify whether the format contains a date or a time or both.
     * In that case, $type must be used.
     */
    public $format = null;

    /**
     * @var string type of picker: 'date', 'time' or 'datetime'.
     * This is only used when the format is ambiguous (i.e. one of the ICU short formats),
     * otherwise it is ignored, and the format dictates which parts of the picker are shown.
     */
    public $type = self::TYPE_DATETIME;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->locale === null) {
            $this->locale = Yii::$app->language;
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        Html::addCssClass($this->options, 'form-control');
        $input = $this->hasModel()
            ? Html::activeTextInput($this->model, $this->attribute, $this->options)
            : Html::textInput($this->name, $this->value, $this->options);

        $html = '';
        $prepend = $this->renderAddon($this->prepend);
        $append = $this->renderAddon($this->append);
        $addon = false;
        if ($prepend !== '' || $append !== '') {
            $addon = true;
            $html .= Html::beginTag('div', ['class' => 'input-group date']);
            $html .= $prepend;
        }
        $html .= $input;
        if ($addon) {
            $html .= $append;
            $html .= Html::endTag('div');
        }
        $this->registerPlugin($addon);
        return $html;
    }

    protected function renderAddon($addons)
    {
        if ($addons === false) {
            return '';
        }
        if (!is_array($addons)) {
            $addons = [$addons];
        }
        if (!count($addons)) {
            return '';
        }
        $result= '';
        foreach ($addons as $addon) {
            if ($addon === '{datepicker}') {
                $result .= Html::tag('span', $this->datePickerButton, ['class' => 'input-group-addon datepickerbutton']);
            } elseif ($addon === '{clear}') {
                $result .= Html::tag('span', $this->clearButton, ['class' => 'input-group-addon clearbutton']);
            } else {
                $result .= Html::tag('span', $addon, ['class' => 'input-group-addon']);
            }
        }
        return $result;
    }

    protected function findLocaleFile($locale)
    {
        $localesDir = Yii::getAlias('@bower/moment/locale');
        $file = $localesDir . DIRECTORY_SEPARATOR . $locale . '.js';
        if (file_exists($file)) {
            return $file;
        }

        $sep = strpos($locale, '-');
        if (!$sep) {
            return false;
        }
        $locale = substr($locale, 0, $sep);
        $file = $localesDir . DIRECTORY_SEPARATOR . $locale . '.js';
        if (file_exists($file)) {
            return $file;
        }
        return false;
    }

    protected function registerLocale($view)
    {
        if ($this->locale === false) {
            return;
        }
        $locale = $this->locale;

        $locale = str_replace('_', '-', strtolower($locale));

        $file = $this->findLocaleFile($locale);
        if ($file === false) {
            return;
        }

        $view->registerJsFile(Yii::$app->assetManager->publish($file)[1], [
            'depends' => '\nkovacs\datetimepicker\MomentAsset',
        ]);

        // use the same strings intl uses, to avoid validation errors.
        if (extension_loaded('intl')) {
            $weekdays = LocaleConverter::getWeekdays('EEEE', $this->locale); // dddd
            $weekdaysShort = LocaleConverter::getWeekdays('EEE', $this->locale); // ddd
            $weekdaysMin = LocaleConverter::getWeekdays('EEEEEE', $this->locale); // dd
            $months = LocaleConverter::getMonths('MMMM', $this->locale);
            $monthsShort = LocaleConverter::getMonths('MMM', $this->locale);
            $override = Json::encode([
                'months' => $months,
                'monthsShort' => $monthsShort,
                'weekdays' => $weekdays,
                'weekdaysShort' => $weekdaysShort,
                'weekdaysMin' => $weekdaysMin,
            ]);
            // POS_END is the default for js files.
            $view->registerJs("moment.locale('$locale', $override)", \yii\web\View::POS_END);
        }

        $this->clientOptions['locale'] = $locale;
    }

    protected function registerFormat()
    {
        if ($this->format === false) {
            // do nothing, use moment's default
            return;
        }
        $format = $this->format;
        if ($format === null) {
            switch ($this->type) {
                case self::TYPE_DATE:
                    $format = Yii::$app->formatter->dateFormat;
                    break;
                case self::TYPE_TIME:
                    $format = Yii::$app->formatter->timeFormat;
                    break;
                case self::TYPE_DATETIME:
                    $format = Yii::$app->formatter->datetimeFormat;
                    break;
            }
        }

        if (is_string($format)) {
            if (strncmp($format, 'php:', 4) === 0) {
                $format = FormatConverter::convertDatePhpToMoment(substr($format, 4));
            } elseif (strncmp($format, 'moment:', 7) === 0) {
                $format = substr($format, 7);
            } else {
                $format = FormatConverter::convertDateIcuToMoment($format, $this->type, $this->locale);
            }
            $this->clientOptions['format'] = $format;
        }
    }

    /**
     * Registers Bootstrap plugin and the related events
     * @param boolean $addon whether to register plugin on addon instead of input
     */
    protected function registerPlugin($addon)
    {
        $view = $this->getView();

        DateTimePickerAsset::register($view);

        $this->registerLocale($view);
        $this->registerFormat();

        $id = $this->options['id'];

        $selector = "jQuery('#$id')";
        if ($addon) {
            $selector .= '.closest(".input-group.date")';
        }

        if ($this->clientOptions !== false) {
            $options = empty($this->clientOptions) ? '' : Json::htmlEncode($this->clientOptions);
            $js = "$selector.datetimepicker($options).find('.clearbutton').on('click', function() {
                $selector.data('DateTimePicker').clear();
            });";
            $view->registerJs($js);
        }

        if (!empty($this->clientEvents)) {
            $js = [];
            foreach ($this->clientEvents as $event => $handler) {
                $js[] = "$selector.on('$event', $handler);";
            }
            $view->registerJs(implode("\n", $js));
        }
    }
}
