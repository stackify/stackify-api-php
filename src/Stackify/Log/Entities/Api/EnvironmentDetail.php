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

    /**
     * Singleton attributes
     */
    private function __construct() {}
    private function __clone() {}

    public function init($appName, $environmentName)
    {
        $this->AppName = $appName;
        $this->ConfiguredAppName = $appName;
        $this->DeviceName = gethostname();
        $this->AppLocation = getcwd();
        $this->ConfiguredEnvironmentName = $environmentName;
        return $this;
    }

    public static function getInstance()
    {
        static $instance;
        if (null === $instance) {
            $instance = new self();
        }
        return $instance;
    }

}