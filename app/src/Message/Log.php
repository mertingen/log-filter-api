<?php

namespace App\Message;

use App\Entity\ServiceHttpLog;

class Log
{

    public function __construct(private readonly ServiceHttpLog $serviceHttpLog)
    {
    }

    /**
     * @return ServiceHttpLog
     */
    public function getServiceHttpLog(): ServiceHttpLog
    {
        return $this->serviceHttpLog;
    }


}