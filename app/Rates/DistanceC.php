<?php

namespace App\Rates;

use App\Contracts\Distance as DistanceContract;

class DistanceC implements DistanceContract
{
    /**
     * The value of this trip, as an integer (base currency, e.g. pennies).
     *
     * @var int
     */
    protected $value;

    /**
     * Create a new Result.
     *
     * @param int $value
     */
    public function __construct(int $value)
    {
        $this->value = $value;
    }

    /**
     * Get the amount for this result, before tax.
     *
     * @return int
     */
    public function value(): int
    {     
        // distance for C  
        if($this->value>=0)
        {
            if($this->value>100)
            {
                $totalRate = ($this->value-100) * 20  + 100;
                return $totalRate;
            }
            else{
                $totalRate = $this->value * 1 ;
                return $totalRate;
            }
        }
        
       
        return $this->value;
    }
}
