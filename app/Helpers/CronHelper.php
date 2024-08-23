<?php

namespace App\Helpers;

use Carbon\Carbon;

class CronHelper
{
    public static function getNextRunDate($cronExpression, Carbon $currentDate)
    {
        $parts = explode(' ', $cronExpression);
        if (count($parts) !== 5) {
            throw new \InvalidArgumentException('Invalid cron expression');
        }

        list($minute, $hour, $day, $month, $weekday) = $parts;

        $nextRun = $currentDate->copy();
        $nextRun->second(0);

        while (!self::matchesCron($nextRun, $minute, $hour, $day, $month, $weekday)) {
            $nextRun->addMinute();
        }

        return $nextRun;
    }

    private static function matchesCron($date, $minute, $hour, $day, $month, $weekday)
    {
        return self::matchesPart($date->format('i'), $minute) &&
            self::matchesPart($date->format('H'), $hour) &&
            self::matchesPart($date->format('d'), $day) &&
            self::matchesPart($date->format('m'), $month) &&
            self::matchesPart($date->format('w'), $weekday);
    }

    private static function matchesPart($value, $pattern)
    {
        if ($pattern === '*') {
            return true;
        }

        $values = explode(',', $pattern);
        foreach ($values as $v) {
            if (strpos($v, '-') !== false) {
                list($start, $end) = explode('-', $v);
                if ($value >= $start && $value <= $end) {
                    return true;
                }
            } elseif (strpos($v, '/') !== false) {
                list($start, $step) = explode('/', $v);
                if ($start === '*' || $value % $step === 0) {
                    return true;
                }
            } elseif ($v == $value) {
                return true;
            }
        }

        return false;
    }
}
