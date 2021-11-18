<?php

namespace App\Domain\Common;

//use App\Domain\DTO\i18n\i18n;

use DateInterval;
use DatePeriod;
use DateTime;
use Exception;
use Grubie\Libs\DateRange;

final class Functions
{
    //TODO: Remove this constants when i18n is implemented
    public const DECIMAL_POINT = ",";
    public const THOUSANDS_POINT = ".";


    /**
     * @param mixed $number
     * @param bool $type
     * @param int $decimals
     * @param bool $exclude_unnecessary_decimals
     * @return array|int|mixed|string|string[]
     */
    public static function format_big_number(
        $number,
        bool $type = false,
        int $decimals = 0,
        bool $exclude_unnecessary_decimals = false
    ) {
        if (strpos($number, ',') === false) {
            if (is_numeric($number)) {
                if ($decimals === 0) {
                    if (is_float($number)) {
                        if ($number > 0 and $number < 1) {
                            if ($number < 0.01) {
                                $decimals = 4;
                            } elseif ($number < 0.1) {
                                $decimals = 3;
                            } elseif ($number < 1) {
                                $decimals = 2;
                            }
                        }
                    }
                }

                $n = number_format(
                    $number,
                    $decimals,
                    self::DECIMAL_POINT,
                    self::THOUSANDS_POINT
                //TODO: i18n::getValue('TXT_DECIMAL_POINT'),
                //TODO: i18n::getValue('TXT_THOUSANDS_POINT')
                );

                if (str_replace(array('0', ',', '.'), array(''), $n) === '') {
                    $n = 0;
                }
            } else {
                $n = 0;
            }
            /*
             * TODO: WRONG!!!! Used in KPI's far now.
             */
            if ($exclude_unnecessary_decimals) {
                $n = self::format_percentage_number($n, false);
            }

            if ($type == 'percentage') {
                $n = self::format_percentage_number($n, false);
            }

            return $n;
        } else {
            return $number;
        }
    }

    /**
     * @param mixed $num
     * @param bool $reformat
     * @return array|int|mixed|string|string[]
     */
    public static function format_percentage_number($num, bool $reformat = true)
    {
        if ($reformat) {
            return self::format_big_number($num, false, 2, true);
        }
        return str_replace(self::DECIMAL_POINT . '00', '', $num);
    }


    /**
     * FillMissingDate predecessor (callback mapper like)
     * @param array $data Array with all the data obtained from MONGO
     * @param DateRange $range Range of date to process obtained from URL
     * @param DateInterval $interval The interval to filled this a new DateInterval | new DateInterval("P1D") | new DateInterval("P1W") | new DateInterval("P1M")
     * @param mixed $map_func_for_missing Anonymus Function to fill the step when is missing
     * @param string $date_key Field where the function can find the date inside of $data
     * @return array Return the $data but all the missing dates filled by  $map_func_for_missing
     * @throws Exception
     */
    public static function fillMissingDates(array $data, DateRange $range, DateInterval $interval, $map_func_for_missing, string $date_key = 'date'): array
    {
        $res = [];

        // build period
        $period = new DatePeriod($range->getStart(), $interval, new DateTime($range->getEnd()->format("Y-m-d 23:59:59")));

        // find key_format for date field
        $key_format = '';
        if ($interval->y > 0) {
            $key_format = 'Y';
        }
        if ($interval->m > 0) {
            $key_format = 'Y-m';
        }
        if ($interval->d > 0) {
            $key_format = 'Y-m-d';
        }

        // put formatted date key as key for fastest search
        $data = array_combine(
            array_map(function ($v) use ($key_format) {
                return date($key_format, strtotime($v));
            }, array_column($data, $date_key)),
            $data
        );

        // iterate through date to find missing and perform user map
        /** @var DateTime $date */
        foreach ($period as $date) {
            $key = $date->format($key_format);
            if (array_key_exists($key, $data)) {
                $res[] = $data[$key];
            } else {
                // call user_map like function to fill missing entries
                $fill_entry = $map_func_for_missing($date);
                // date key are internally build
                $fill_entry[$date_key] = $date->format($key_format);
                $res[] = $fill_entry;
            }
        }

        return $res;
    }


    //Para las semanas


    /**
     * Example de aca
     * Si tengo un array que de mongo de devuelve asi:
     * $data = [
     *  [ 'date'=>'2021-01-01','followers'=>10],
     *  [ 'date'=>'2021-01-03','followers'=>12]
     * ]
     * LLamo a esta funcion de esta manera
     * unifiedByWeek($data,['followers']);
     * Y te va a devolver
     * $data = [
     *  [ 'date'=>'2021-01-01','followers'=>10],
     *  [ 'date'=>'2021-01-02','followers'=>0],
     *  [ 'date'=>'2021-01-03','followers'=>12]
     * ]
     *
     * El va agarrar la fecha que falte y le ponge todos los que este en $keys_data como clave y valor 0
     */

    /**
     * @param array $data Array with all the data obtained from MONGO
     * @param array $keys_data Array with all the keys to filled with 0 in the period
     * @return array
     * @throws Exception
     */
    public static function unifiedByWeek(array $data, array $keys_data, DateRange $range): array
    {
        $return = [];
        $weeks = (new DateFiller($range))->buildWeeks();
        foreach ($weeks as $week) {
            /** @var DateTime[] $week */
            $week_string = $week['start']->format("Y-m-d") . "-" . $week['end']->format("Y-m-d");
            $week_data = array_filter($data, function ($row) use ($week) {
                $datetime = new DateTime($row['date']);
                return ($datetime >= $week['start'] && $datetime <= $week['end']);
            });
            if (empty($week_data)) {
                $row = [];
                $row['date'] = $week['end']->format("Y-m-d 00:00:00");
                $row['max_date'] = $week['end']->format("Y-m-d 00:00:00");
                $row['min_date'] = $week['start']->format("Y-m-d 00:00:00");
                $return[$week_string] = $row;
            } else {
                foreach ($week_data as $row) {
                    if (!isset($return[$week_string])) {
                        $row['max_date'] = $week['end']->format("Y-m-d 00:00:00");
                        $row['min_date'] = $week['start']->format("Y-m-d 00:00:00");
                        $return[$week_string] = $row;
                    } else {
                        foreach ($keys_data as $key) {
                            if (!isset($row[$key])) {
                                continue;
                            }
                            if (isset($return[$week_string][$key])) {
                                $return[$week_string][$key] += $row[$key];
                            } else {
                                $return[$week_string][$key] = intval($row[$key]);
                            }
                        }
                    }
                }
            }
        }
        return array_values($return);
    }
}
