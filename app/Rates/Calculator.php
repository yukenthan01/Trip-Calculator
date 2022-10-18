<?php

namespace App\Rates;

use App\Contracts\Calculator as CalculatorContract;
use App\Contracts\Result as ResultContract;
use Carbon\Carbon;

class Calculator implements CalculatorContract
{
   
    public static function hoursDifferent($start,$end)
    {
        // calculating time difference
        $to = Carbon::createFromFormat('Y-m-d H:s:i', clamp($start));
        $from = Carbon::createFromFormat('Y-m-d H:s:i', clamp($end));
  
        $diff_in_hours = $to->diffInHours($from);
       
        if($diff_in_hours==0)
        {
           
            $diff_in_milli = $to->diffInMilliseconds($from);
            $diff_in_hours =  ($diff_in_milli/1000)/60;
            return $diff_in_hours;
        }
        return $diff_in_hours;
    }
    
    public static function isWeekEnd($date)
    {
        // check as weekend
        $dateFormated = Carbon::createFromFormat('Y-m-d H:s:i', clamp($date));
        if($dateFormated->isWeekend())
        {
            return true;
        }
        return false;
    }
    /**
     * Calculate our rates.
     *
     * @param \Carbon\Carbon $start
     * @param \Carbon\Carbon $end
     * @param int $distance
     *
     * @return \App\Contracts\Result
     */
    public function calculate(Carbon $start, Carbon $end, int $distance): ResultContract
    {
        $duration = $this->hoursDifferent($start,$end);
        if($distance<=0)
        {
            // Scenario A
            $totalRate = 4 * 100 * $duration;
        }
        else{
            // Scenario B
            if($duration<24)
            {
               
                $totalRate = 15 * 100 * $duration;
               
                // reset to max rate
                if($totalRate >= 8500)
                {
                    $totalRate = 8500;
                }
            }
            else{
                $day = round($duration/24,0,PHP_ROUND_HALF_DOWN);
                $totalRate = 85 * 100 * $day + ($duration%24 * 15 * 100);
            }
            
            
        }
        
        return new Result($totalRate, new Distance($distance));
    }
/**
     * Calculate our rates.
     *
     * @param \Carbon\Carbon $start
     * @param \Carbon\Carbon $end
     * @param int $distance
     *
     * @return \App\Contracts\Result
     */
    public function calculateC(Carbon $start, Carbon $end, int $distance): ResultContract
    {
        // Scenario C
        $to = date("H:i:s",strtotime(clamp($start)));
        $from = date("H:i:s",strtotime(clamp($end)));
        
        $duration = $this->hoursDifferent(clamp($start),clamp($end));
        // $isWeekend = $this->isWeekEnd($start);
        $rate = 0;
        $totalRate = 0;
        $totalRateSub = 0;
        if($this->isWeekEnd($start) && $this->isWeekEnd($end))
        {
            $rate = 2;
            $totalRate = $rate * 100 * $duration;
        }
        else{

            // calculate start date hours
            $toDate = date("Y-m-d",strtotime(clamp($start)));
            $dayEnd = $toDate . " " . "23:59:59";
            $dayEnd = Carbon::createFromFormat('Y-m-d H:s:i', $dayEnd);
            $durationWeekEndStart = $this->hoursDifferent($start,$dayEnd);


            // calculate end date hours
            $toDate = date("Y-m-d",strtotime(clamp($end)));
            $dayEnd = $toDate . " " . "00:00:00";
            $dayEnd = Carbon::createFromFormat('Y-m-d H:s:i', $dayEnd);
            $durationWeekEndEnd = $this->hoursDifferent($dayEnd,$end);

            if($this->isWeekEnd($start))
            {
                // weekend start
                $totalRateSub = $durationWeekEndStart * 2;
            }
            else{
                // weekday start
                if($to >= Carbon::createFromFormat('H:s:i', "07:00:00") && $to <= Carbon::createFromFormat('H:s:i', "19:00:00"))
                {
                    $totalRateSub = $durationWeekEndStart * 6.65;
                    // $rate = 6.65;
                }
                else{
                    $totalRateSub = $durationWeekEndStart * 4;
                    //$rate = 4;
                }
            }

            if($this->isWeekEnd($end))
            {
                // weekend start
                $totalRateSub = $totalRateSub + $durationWeekEndEnd * 2;
            }
            else{
                // weekday start
                if($to >= Carbon::createFromFormat('H:s:i', "07:00:00") && $to <= Carbon::createFromFormat('H:s:i', "19:00:00"))
                {
                    $totalRateSub = $totalRateSub + $durationWeekEndEnd * 6.65;
                    // $rate = 6.65;
                }
                else{
                    $totalRateSub = $totalRateSub + $durationWeekEndEnd * 4;
                    //$rate = 4;
                }
            }

            $totalRate = $totalRateSub * 100;


            
        }
        // $totalRate = $rate * 100 * $duration;

        if($to >= Carbon::createFromFormat('H:s:i', "21:00:00") && $from >= Carbon::createFromFormat('H:s:i', "06:00:00") && $totalRate >= 1200)
        {
            $totalRate = 1200;
        }

        if($totalRate >= 3900 && $duration<=24)
        {
            $totalRate = 3900;
        }

        return new Result($totalRate, new DistanceC($distance));
    }
   
}
