<?php namespace EFrane\Transfugio\Transformers\Formatter;

use League\Uri\Schemes\Http;
use League\Url\Url;

/**
 * HTTP URI Formatter
 *
 * Validates and formats URLs.
 *
 * @package EFrane\Transfugio\Transformers\Formatter
 **/
class HttpURI implements FormatHelper
{
    /**
     * Format an URL to be fully-qualified
     *
     * @param string $value
     * @return string
     **/
    public function format($value)
    {
        if (!$this->validate($value))
            throw new \InvalidArgumentException("`{$value}` is not a valid HTTP URI");

        $url = Http::createFromString($value);

        if ($url->getScheme() == '') {
            $url = $url->withScheme('http');
        }

        return (String)$url;
    }

    /**
     * Validate an expression to a valid http url
     *
     * @param string $value
     * @return bool
     */
    public function validate($value)
    {
        $regex = '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,})([\/\w \.-]*)*\/?$/i';

        return preg_match($regex, $value) === 1;
    }
}