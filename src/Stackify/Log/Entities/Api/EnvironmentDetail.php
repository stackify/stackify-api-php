<?php

namespace Stackify\Log\Entities\Api;

class EnvironmentDetail
{

    /**
     * @var string
     */
    public $DeviceName;

    /**
     * @var string
     */
    public $AppName;

    /**
     * @var string
     */
    public $AppLocation;

    /**
     * @var string
     */
    public $ConfiguredAppName;

    /**
     * @var string
     */
    public $ConfiguredEnvironmentName;

    private function __construct($appName)
    {
        $this->AppName = $appName;
        $this->ConfiguredAppName = $appName;
        $this->DeviceName = gethostname();
        $this->AppLocation = getcwd();
        // @TODO environment?
    }

    /**
     * Singleton attributes
     */
    private function __clone() {}

    public static function getInstance($appName)
    {
        static $instance;
        if (null === $instance) {
            $instance = new self($appName);
        }
        return $instance;
    }

}