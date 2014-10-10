<?php

namespace nkovacs\datetimepicker;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;

class DateTimePicker extends \yii\widgets\InputWidget
{
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
     */
    public $append = '<span class="glyphicon glyphicon-calendar"></span>';
    /**
     * @var false|string|string[] prepended addon(s)
     */
    public $prepend = false;

    /**
     * @var mixed datetimepicker langauge
     * If null, it will use Yii::$app->language.
     * If a string, that language will be used.
     * If false, language will not be set and no locale files will be included.
     */
    public $language = null;

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
        if ($prepend !== '' || $append !== '') {
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
             $result .= Html::tag('span', $addon, ['class' => 'input-group-addon']);
        }
        return $result;
    }

    protected function findLanguageFile($language)
    {
        $localesDir = Yii::getAlias('@bower/moment/locale');
        $file = $localesDir . DIRECTORY_SEPARATOR . $language . '.js';
        if (file_exists($file)) {
            return $file;
        }

        $sep = strpos($language, '-');
        if (!$sep) {
            return false;
        }
        $language = substr($language, 0, $sep);
        $file = $localesDir . DIRECTORY_SEPARATOR . $language . '.js';
        if (file_exists($file)) {
            return $file;
        }
        return false;
    }

    protected function registerLanguage($view)
    {
        if ($this->language === false) {
            return;
        }
        $language = $this->language;
        if ($language === null) {
            $language = Yii::$app->language;
        }

        $language = str_replace('_', '-', strtolower($language));

        $file = $this->findLanguageFile($language);
        if ($file === false) {
            return;
        }
        $view->registerJsFile(Yii::$app->assetManager->publish($file)[1], [
            'depends' => '\nkovacs\datetimepicker\MomentAsset',
        ]);
        $this->clientOptions['language'] = $language;
    }

    /**
     * Registers Bootstrap plugin and the related events
     * @param boolean $addon whether to register plugin on addon instead of input
     */
    protected function registerPlugin($addon)
    {
        $view = $this->getView();

        DateTimePickerAsset::register($view);

        $this->registerLanguage($view);

        $id = $this->options['id'];

        $selector = "jQuery('#$id')";
        if ($addon) {
            $selector .= '.closest(".input-group.date")';
        }

        if ($this->clientOptions !== false) {
            $options = empty($this->clientOptions) ? '' : Json::encode($this->clientOptions);
            $js = "$selector.datetimepicker($options);";
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
