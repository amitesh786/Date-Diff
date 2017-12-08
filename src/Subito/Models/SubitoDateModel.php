<?php
namespace Subito\Models;
use Subito\Interfaces\SubitoDateInterface;

class SubitoDateModel implements SubitoDateInterface
{
    private $startDate;
    private $endDate;

    public function __construct($startDate, $endDate) {
        $this->setStartDate($startDate);
        $this->setEndDate($endDate);
    }

    public function setStartDate($date) {
        if (!$this->isValidDate($date)) {
            throw new \Exception('Start date is not a valid date');
        }
        $this->startDate = $date;
    }

    public function getStartDate() {
        return $this->startDate;
    }

    public function setEndDate($date) {
        if (!$this->isValidDate($date)) {
            throw new \Exception('End date is not a valid date');
        }
        $this->endDate = $date;
    }

    public function getEndDate() {
        return $this->endDate;
    }

    public function isValidDate($date) {
        //this will also work fine
        // return SubitoDate::parse($date) !== null;
        $tempDate = explode("/", $date); 
        return checkdate($tempDate[1], $tempDate[2], $tempDate[0]);
    }

    public function diff() {

        $isReverse = false;
        $startDate = SubitoDate::parse($this->startDate);
        $endDate = SubitoDate::parse($this->endDate);

        $daysFromEpochStart = $startDate->getDaysSinceEpoch();
        $daysFromEpochEnd = $endDate->getDaysSinceEpoch();

        // Reverse the values if needed
        if ($daysFromEpochStart > $daysFromEpochEnd) {
            $isReverse = true;
            list($startDate, $endDate) = array($endDate, $startDate);
            list($daysFromEpochStart, $daysFromEpochEnd) = array($daysFromEpochEnd, $daysFromEpochStart);
        }

        $leapDays = SubitoDate::calculateLeapDaysBetween($daysFromEpochStart, $daysFromEpochEnd);
        $diffTotalDays = $daysFromEpochEnd - $daysFromEpochStart;
        $diffTotalMinusLeap = $diffTotalDays - $leapDays;

        // Subtracting the leap days from the total days should normalize the situation to allow us to simply divide by 365
        $diffYears = floor($diffTotalMinusLeap / 365);

        // Now we get the remainder of the previous operation
        $remainingDays = $diffTotalMinusLeap % 365;

        // Account for the remaining leap days
        $remainingLeapDays = SubitoDate::calculateLeapDaysBetween($daysFromEpochStart + $diffYears * 365, $daysFromEpochEnd);

        // Calculate the months using the remaining days ($diffDays gets decreased accordingly, leaving the remaining days)
        $diffDays = $remainingDays + $remainingLeapDays;
        $diffMonths = $this->calculateMonthsInDays($diffDays, $startDate->getMonth(), $startDate->getYear() + $diffYears);
        
        return (object) array(
            'years' => intval(abs($diffYears)),
            'months' => intval(abs($diffMonths)),
            'days' => intval(abs($diffDays)),
            'total_days' => intval(abs($diffTotalDays)),
            'invert' => $isReverse
        );
    }

    private function calculateMonthsInDays(&$diffDays, $currentMonth, $currentYear) {
        $months = 0;
        $daysInCurrentMonth = SubitoDate::calculateDaysInMonth($currentYear, $currentMonth);
        if ($diffDays >= $daysInCurrentMonth) {
            $diffDays -= $daysInCurrentMonth;
            $currentMonth++;
            if ($currentMonth > SubitoMonths::December) {
                $currentMonth = SubitoMonths::January;
                $currentYear++;
            }
            $months = $this->calculateMonthsInDays($diffDays, $currentMonth, $currentYear) + 1;
        }
        return $months;
    }
}
