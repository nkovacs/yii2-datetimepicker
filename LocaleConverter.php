<?php

namespace nkovacs\datetimepicker;

use Yii;
use IntlDateFormatter;

class LocaleConverter
{
    protected static $cache = [];

    public static function getMonths($format, $locale = null) {
        if ($locale === null) {
            $locale = Yii::$app->language;
        }

        if (!isset(static::$cache['months'][$locale][$format])) {
            $formatter = new IntlDateFormatter($locale, IntlDateFormatter::NONE, IntlDateFormatter::NONE, Yii::$app->timeZone, null, $format);
            $localized = [];
            for ($month = 0; $month < 12; $month++) {
                $localized[] = $formatter->format([
                    'tm_sec' => 0,
                    'tm_min' => 0,
                    'tm_hour' => 12,
                    'tm_mday' => 15,
                    'tm_mon' => $month,
                    'tm_year' => 2016,
                ]);
            }
            static::$cache['months'][$locale][$format] = $localized;
        }
        return static::$cache['months'][$locale][$format];
    }

    public static function getWeekdays($format, $locale = null) {
        if ($locale === null) {
            $locale = Yii::$app->language;
        }
        if (!isset(static::$cache['weekdays'][$locale][$format])) {
            $formatter = new IntlDateFormatter($locale, IntlDateFormatter::NONE, IntlDateFormatter::NONE, Yii::$app->timeZone, null, $format);
            $localized = [];
            for ($day = 0; $day < 7; $day++) {
                $localized[] = $formatter->format([
                    'tm_sec' => 0,
                    'tm_min' => 0,
                    'tm_hour' => 12,
                    'tm_mon' => 6,
                    'tm_year' => 2016,
                    'tm_mday' => 2 + $day, // 2016-06-02 is a sunday
                ]);
            }
            static::$cache['weekdays'][$locale][$format] = $localized;
        }
        return static::$cache['weekdays'][$locale][$format];
    }
}
