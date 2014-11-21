<?php

namespace Stackify\Log\Entities;

interface LogEntryInterface
{

    /**
     * @return string
     */
    public function getLevel();

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @return integer  Timestamp in milliseconds
     */
    public function getMilliseconds();

    /**
     * @return array  Additional log data
     */
    public function getContext();

    /**
     * @return \Exception|null
     */
    public function getException();

    /**
     * Get native PHP error (e.g. E_NOTICE or E_WARNING)
     * @return \Stackify\Log\Entities\NativeError
     */
    public function getNativeError();

    /**
     * Check if log entry has level "error" or above 
     * (e.g. "critical" or "alert")
     * @return boolean
     */
    public function isErrorLevel();

}