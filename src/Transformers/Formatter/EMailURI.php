<?php namespace EFrane\Transfugio\Transformers\Formatter;

/**
 * EMailURI Formatter
 *
 * Validates and formats email addresses as URI
 *
 * @package EFrane\Transfugio\Transformers\Formatter
 **/
class EMailURI implements FormatHelper
{
  /**
   * Formats an email address as URI
   *
   * @param $value
   * @return string
   **/
  public function format($value)
  {
    if (!$this->validate($value))
      throw new \InvalidArgumentException("`{$value}` is not a valid E-Mail-Address");

    return strtolower("mailto:{$value}");
  }

  /**
   * Validates an email address
   *
   * @param $value the string in question
   * @see http://www.regular-expressions.info/email.html
   **/
  public function validate($value)
  {
    $regex = '/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i';
    return preg_match($regex, $value) === 1;
  }
}