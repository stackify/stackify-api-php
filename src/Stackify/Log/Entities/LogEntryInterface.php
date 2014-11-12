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
     * @TODO add description
     * @return \Stackify\Log\Entities\NativeError
     */
    public function getNativeError();

}