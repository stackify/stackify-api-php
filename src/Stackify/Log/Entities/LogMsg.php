<?php

namespace Stackify\Log\Entities;

class LogMsg
{
    /**
     * @var string
     */
    public $Msg;

    /**
     * @var string (JSON)
     */
    public $data;

    /**
     * @var \Stackify\Log\Entities\StackifyError
     */
    public $Ex;

    /**
     * @var string
     */
    public $Th;

    /**
     * @var integer
     */
    public $EpochMs;

    /**
     * @var string
     */
    public $Level;

    /**
     * @var string
     */
    public $TransID;

    /**
     * @var string
     */
    public $SrcMethod;

    /**
     * @var integer
     */
    public $SrcLine;

    public function __construct($level, $message, \DateTime $datetime)
    {
        $this->Level = $level;
        $this->Msg = $message;
        $this->EpochMs = $datetime->getTimestamp() * 1000;
    }

}