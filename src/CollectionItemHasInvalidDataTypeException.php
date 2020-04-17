<?php


namespace Advance\Helper;


use Throwable;

class CollectionItemHasInvalidDataTypeException extends \Exception
{

    public function __construct($className, $code = 0, Throwable $previous = null)
    {
        parent::__construct('', $code, $previous);
    }

}
