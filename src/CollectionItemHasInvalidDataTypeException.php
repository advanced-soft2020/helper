<?php


namespace Advance\Helper;


class CollectionItemHasInvalidDataTypeException extends \Exception
{

    public function __construct($className, $code = 0, \Throwable $previous = null)
    {
        parent::__construct('Collection element must be an instance '.$className, $code, $previous);
    }

}
