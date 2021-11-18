<?php

namespace App\Domain\Common;

use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeInterface;
use Exception;
use Grubie\Libs\DateRange;

final class DateFiller
{
    /** @var DateRange */
    private $dateRange;

    /**
     * DateFiller constructor.
     * @param DateRange $dateRange
     */
    public function __construct(DateRange $dateRange)
    {
        //To control problems of campaign of one day
        if ($dateRange->getStart()->format("Y-m-d") == $dateRange->getEnd()->format("Y-m-d")) {
            $this->dateRange = new DateRange($dateRange->getStart()->format("Y-m-d H:i:s"), $dateRange->getStart()->format("Y-m-d 23:59:59"));
        }
        else {
            $this->dateRange = $dateRange;
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    public function buildWeeks(): array
    {
        $weeks = [];
        $period = new DatePeriod($this->dateRange->getStart(), new DateInterval("P1W"), $this->dateRange->getEnd());
        foreach ($period as $date) {
            $week = $this->buildWeekByDateTime($date);
            $weeks[] = $week;
        }
        return $weeks;
    }

    /**
     * @param DateTimeInterface $date
     * @param DateTime|null $max_date
     * @return array
     */

    private function buildWeekByDateTime(DateTimeInterface $date, ?DateTime $max_date = null): array
    {
        $ret = [];
        $star_date = new DateTime();
        $star_date->setISODate($date->format('Y'), $date->format('W'));
        $ret['start'] = $star_date->setTime(0, 0, 0, 0);

        $end_date = clone $star_date;
        $end_date->modify('+6 days');
        if ($end_date > $this->dateRange->getEnd()) {
            $ret['end'] = $this->dateRange->getEnd();
        }
        else {
            $ret['end'] = $end_date->setTime(23, 59, 59);
        }
        return $ret;
    }
}