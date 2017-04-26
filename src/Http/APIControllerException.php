<?php

namespace EFrane\Transfugio\Http;

class APIControllerException extends \LogicException {
    public static function invalidModelPropertyException()
    {
        return new self("API controllers must have a valid \$model property.");
    }

    public static function invalidItemIdPropertyException()
    {
        return new self("The provided item_id property cannot be evaluated, please check if the model is configured correctly.");
    }
}