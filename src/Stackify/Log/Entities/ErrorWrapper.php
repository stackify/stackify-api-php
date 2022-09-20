<?php

namespace Stackify\Log\Entities;

use Stackify\Log\Transport\Config\AbstractConfig;
use Stackify\Log\Transport\Config\Agent;

class ErrorWrapper
{

    private $message;
    private $type;
    private $code;
    private $trace;
    private $sourceMethod;
    /**
     * Agent config
     *
     * @var AbstractConfig
     */
    private $_config;

    const TYPE_STRING_EXCEPTION = 'StringException';
    const TRACE_UNKNOWN_ITEM = '{unknown}';

    /**
     * @var \Stackify\Log\Entities\ErrorWrapper
     */
    private $innerError;

    public function __construct($object, $nestingLevel = 1, $config = null)
    {
        $this->_config = !empty($config) ? $config: Agent::getInstance();

        if ($object instanceof \Exception) {
            $this->message = $object->getMessage();
            $this->type = get_class($object);
            $this->code = $object->getCode();
            $trace = $object->getTrace();
            $trace[] = array(
                'file' => $object->getFile(),
                'line' => $object->getLine(),
            );
            $this->setTrace($trace);
            $previous = $object->getPrevious();
            if (null !== $previous) {
                // limit nesting level if needed here
                $this->innerError = new self($previous, ++$nestingLevel);
            }
        } elseif ($object instanceof NativeError) {
            $this->message = $object->getMessage();
            $this->type = $object->getType();
            $this->code = $object->getCode();
            $this->setTrace($object->getTrace());
            $this->innerError = null;
        } elseif ($object instanceof LogEntryInterface) {
            // this is a backtrace type
            $this->message = $object->getMessage();
            $this->type = self::TYPE_STRING_EXCEPTION;
            $this->code = null;
            $this->setTrace(debug_backtrace());
            $this->innerError = null;
        }

        if ($this->_config) {
            $classBlacklist = $this->_config->getCaptureExceptionClassBlacklist();
            if (!empty($classBlacklist) && isset($classBlacklist[$this->type])) {
                $this->type = $this->message;
            }
        }
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getTrace()
    {
        return $this->trace;
    }

    public function getSourceMethod()
    {
        return $this->sourceMethod;
    }

    public function getInnerError()
    {
        return $this->innerError;
    }

    private function setTrace(array $trace)
    {
        $result = array();
        foreach ($this->filterTrace($trace) as $index => $item) {
            if (isset($item['function'])) {
                $function = $item['function'] . '()';
            } else {
                $function = self::TRACE_UNKNOWN_ITEM;
            }
            if (isset($item['class'])) {
                // type is -> or :: which means dynamic or static method call
                $type = $this->getTraceItem($item, 'type', '->');
                $function = $item['class'] . $type . $function;
                $sourceMethod = $function;
            } else {
                if (isset($item['file'])) {
                    $sourceMethod = $item['file'] . ':' . $function;
                } else {
                    $sourceMethod = $function;
                }
            }
            $result[] = array(
                'file' => $this->getTraceItem($item, 'file', self::TRACE_UNKNOWN_ITEM),
                'line' => $this->getTraceItem($item, 'line', 0),
                'function' => $function,
            );
            if (0 === $index) {
                $this->sourceMethod = $sourceMethod;
            }
        }
        $this->trace = $result;
    }

    private function getTraceItem($item, $keyName, $defaulValue = null)
    {
        return isset($item[$keyName]) ? $item[$keyName] : $defaulValue;
    }

    private function filterTrace(array $trace)
    {
        $filtered = array();
        $excludePath = $this->getFullPathThatContains('vendor');
        if (null === $excludePath) {
            // vendor path not found - library was installed without composer
            // maybe testing mode? exclude our package only
            $packageSrc = 'src' . DIRECTORY_SEPARATOR . 'Stackify';
            $excludePath  = $this->getFullPathThatContains($packageSrc);
        }
        foreach ($trace as $item) {
            // check if path starts with $excludePath
            if (!isset($item['file']) || false === strpos($item['file'], $excludePath)) {
                $filtered[] = $item;
            }
        }
        return $filtered;
    }

    private function getFullPathThatContains($searchPath)
    {
        $result = null;
        $currentPath = __FILE__;
        $foundPos = strrpos($currentPath, $searchPath);
        if (false !== $foundPos) {
            $result = substr($currentPath, 0, $foundPos + strlen($searchPath));
        }
        return $result;
    }

    /**
     * Set error message
     *
     * @param string $message New error message
     *
     * @return void
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

}
