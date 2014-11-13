<?php

namespace Stackify\Log\Entities;

class ErrorWrapper
{

    private $message;
    private $type;
    private $code;
    private $trace;

    const TYPE_STRING_EXCEPTION = 'StringException';
    const MAX_NESTING_LEVEL = 3;

    /**
     * @var \Stackify\Log\Entities\ErrorWrapper
     */
    private $innerError;

    public function __construct($object, $nestingLevel = 1)
    {
        if ($object instanceof \Exception) {
            $this->message = $object->getMessage();
            $this->type = get_class($object);
            $this->code = $object->getCode();
            $this->trace = $object->getTrace();
            $previous = $object->getPrevious();
            if (null !== $previous && $nestingLevel <= self::MAX_NESTING_LEVEL) {
                $this->innerError = new self($previous, ++$nestingLevel);
            }
        } elseif ($object instanceof NativeError) {
            $this->message = $object->getMessage();
            $this->type = $object->getType();
            $this->code = $object->getCode();
            $traceItem = array(
                'file' => $object->getFile(),
                'line' => $object->getLine(),
                // "method" is not defined in native error
                'function' => null,
            );
            $this->trace = array($traceItem);
            $this->innerError = null;
        } elseif ($object instanceof LogEntryInterface) {
            // this is a backtrace type
            $this->message = $object->getMessage();
            $this->type = self::TYPE_STRING_EXCEPTION;
            $this->code = null;
            $this->trace = $object->getBacktrace();
            $this->innerError = null;
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

    public function getInnerError()
    {
        return $this->innerError;
    }

}