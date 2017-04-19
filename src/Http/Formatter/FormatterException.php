<?php
/**
 * Created by PhpStorm.
 * User: sgraupner
 * Date: 05/09/16
 * Time: 12:20
 */

namespace EFrane\Transfugio\Http\Formatter;


class FormatterException extends \LogicException
{
    public static function formatDisabledException($format)
    {
        return new self("'{$format}' is disabled in transfugio's config");
    }

    public static function formatUnknownException($format)
    {
        return new self("Requested unresolvable output format '{$format}'");
    }
}