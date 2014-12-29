<?php

namespace Stackify\Log\Entities\Api;

use Stackify\Log\Entities\Api\StackifyError;

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
     * @var \Stackify\Log\Entities\Api\StackifyError
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

    public function __construct($level, $message, $milliseconds)
    {
        $this->Level = $level;
        $this->Msg = $message;
        $this->EpochMs = $milliseconds;
    }

    public function setError(StackifyError $error)
    {
        $this->Ex = $error;
        if (isset($error->Error)) {
            $this->SrcMethod = $error->Error->SourceMethod;
            if (isset($error->Error->StackTrace[0])) {
                $this->SrcLine = $error->Error->StackTrace[0]->LineNum;
            }
        }
    }

}