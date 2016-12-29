<?php namespace EFrane\Transfugio\Transformers\Formatter;

use Carbon\Carbon;

class DateISO8601 implements FormatHelper
{
    public function format($value)
    {
        if ($value instanceof Carbon) {
            return $value->format('Y-m-d');
        } else {
            throw new \InvalidArgumentException("`$value` is not of type Carbon\\Carbon");
        }
    }
}