<?php namespace EFrane\Transfugio\Http\Formatter;

use Illuminate\Support\Collection;

class FormatterFactory
{
    /**
     * @var Collection
     */
    protected $enabledFormats = null;

    public function __construct(Collection $enabledFormats)
    {
        $this->enabledFormats = $enabledFormats;
    }

    public function get($format)
    {
        $format = strtolower($format);

        // remove "underscore specifier part" from format
        if (strpos($format, '_') > 0) {
            $format = substr($format, 0, strpos($format, '_'));
        }

        if (!$this->enabledFormats->contains($format)) {
            throw FormatterException::formatDisabledException($format);
        }

        switch ($format) {
            case 'json':
                return new JSONFormatter();
            case 'yaml':
                return new YAMLFormatter();
            case 'html':
                return new HTMLFormatter();

            default:
                throw FormatterException::formatUnknownException($format);
        }
    }
}
