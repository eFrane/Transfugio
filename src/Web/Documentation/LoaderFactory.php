<?php namespace EFrane\Transfugio\Web\Documentation;

class LoaderFactory
{
  public static function getForType($type)
  {
    switch ($type)
    {
      case 'JSONSchema':
        return new JSONSchemaLoader();

      case 'PHPDoc':
        return new PHPDocLoader();

      default:
        throw new \InvalidArgumentException("Documentation type {$type} is not supported.");
    }
  }
}