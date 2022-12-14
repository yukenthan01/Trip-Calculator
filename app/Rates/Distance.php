<?php

namespace App\Rates;

use App\Contracts\Distance as DistanceContract;

class Distance implements DistanceContract
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
        // distance for B 
        if($this->value>=0)
        {
            if($this->value>=50)
            {
                $totalRate = ($this->value-50) * 50 ;
                return $totalRate;
            }
            else{
                return 0;
            }
        }
        
       
        return $this->value;
    }
}
