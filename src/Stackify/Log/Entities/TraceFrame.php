<?php

namespace Stackify\Log\Entities;

class TraceFrame
{
    /**
     * @var string
     */
    public $CodeFileName;

    /**
     * @var integer
     */
    public $LineNum;

    /**
     * @var string
     */
    public $Method;

    public function __construct($file, $line, $method)
    {
        $this->CodeFileName = $file;
        $this->LineNum = $line;
        $this->Method = $method;
    }

}