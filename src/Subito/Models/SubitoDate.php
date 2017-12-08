<?php
namespace Subito\Models;

class SubitoDate
{
    // The separator can be easily replaced, or multiple separators can be allowed
    const DatePattern = '/^([0-9]{4})\/([0-9]{2})\/([0-9]{2})$/';
    // The epoch must be a year after a leap year for the leap year math to work
    const EpochYear = 1901;
    const MonthLengths = [
        SubitoMonths::January => 31,
        SubitoMonths::February => 28.25,
        SubitoMonths::March => 31,
        SubitoMonths::April => 30,
        SubitoMonths::May => 31,
        SubitoMonths::June => 30,
        SubitoMonths::July => 31,
        SubitoMonths::August => 31,
        SubitoMonths::September => 30,
        SubitoMonths::October => 31,
        SubitoMonths::November => 30,
        SubitoMonths::December => 31,
    ];
    const DaysSinceYearStartInMonth = [
        SubitoMonths::January => 0,
        SubitoMonths::February => 31,
        SubitoMonths::March => 59,
        SubitoMonths::April => 90,
        SubitoMonths::May => 120,
        SubitoMonths::June => 151,
        SubitoMonths::July => 181,
        SubitoMonths::August => 212,
        SubitoMonths::September => 243,
        SubitoMonths::October => 273,
        SubitoMonths::November => 304,
        SubitoMonths::December => 334
    ];
    const DaysInYear = 365.25;
    const DaysInLeapYear = 366;
    const DaysBetweenLeapDay = SubitoDate::DaysInYear * 4;
    const DaysFromYearStartToLeapDay = 31 + 29;
    const LeapOffset = SubitoDate::DaysInLeapYear - SubitoDate::DaysFromYearStartToLeapDay;

    private $year;
    private $month;
    private $day;

    public function getYear(): int {
        return $this->year;
    }

    public function getMonth(): int {
        return $this->month;
    }

    public function getDay(): int {
        return $this->day;
    }

    private function __construct($year, $month, $day) {
        $this->year = intval($year);
        $this->month = intval($month);
        $this->day = intval($day);
    }

    public function getDaysSinceEpoch(): int {
        // Calculate days from previous years (adds a day every four years to account for leap years)
        $days = floor(($this->getYear() - static::EpochYear) * 365.25);
        // Add days from current year
        $days += static::calculateDaysSinceYearStart($this->getYear(), $this->getMonth(), $this->getDay());
        return $days;
    }

    public static function parse($date) {
        $date = null;
        $matches = array();
        $isValid = preg_match(static::DatePattern, $date, $matches) === 1;
        if ($isValid === true) {
            list($_, $year, $month, $day) = $matches;
            $year = intval($year);
            $month = intval($month);
            $day = intval($day);
            if (static::isValidDate($year, $month, $day)) {
                $date = new SubitoDate($year, $month, $day);
            }
        }
        return $date;
    }

    public static function isValidDate($year, $month, $day): bool {
        $isValidMonth = static::isValidMonth($month);
        $isValidDay = static::isValidDay($year, $month, $day);
        return $isValidMonth === true && $isValidDay === true;
    }

    public static function isValidMonth($month): bool {
        return $month >= 1 && $month <= 12;
    }

    public static function isValidDay($year, $month, $day): bool {
        return $day >= 1 && $day <= static::calculateDaysInMonth($year, $month);
    }
    
    public static function isLeapYear($year): bool {
        return ($year % 4) === 0;
    }

    public static function calculateDaysInMonth($year, $month): int {
        $maxDay = 0;
        if ($month === SubitoMonths::February) {
            $maxDay = static::isLeapYear($year) ? 29 : 28;
        } elseif (array_key_exists($month, static::MonthLengths)) {
            $maxDay = static::MonthLengths[$month];
        }
        return $maxDay;
    }

    public static function calculateDaysSinceYearStart($year, $month, $day): int {
        $days = $day - 1;
        $days += static::DaysSinceYearStartInMonth[$month];
        if ($month > SubitoMonths::February && static::isLeapYear($year)) {
            $days += 1;
        }
        return $days;
    }

    public static function calculateLeapDaysBetween($daysSinceEpochStart, $daysSinceEpochEnd): int {
        // By dividing the offsetted day by the days between leap days, we get a value that, if floored, can tell us which leap "interval" that day belongs to.
        $leapStart = floor(($daysSinceEpochStart + static::LeapOffset) / static::DaysBetweenLeapDay);
        $leapEnd = floor(($daysSinceEpochEnd + static::LeapOffset) / static::DaysBetweenLeapDay);
        // By subtracting the interval values between them, we get the amount of leap days.
        return abs($leapEnd - $leapStart);
    }
}
