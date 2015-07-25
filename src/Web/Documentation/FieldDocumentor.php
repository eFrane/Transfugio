<?php namespace EFrane\Transfugio\Web\Documentation;

abstract class FieldDocumentor
{
  const CARDINALITY_ONE       = -1;
  const CARDINALITY_OPTIONAL  = -2;
  const CARDINALITY_UNBOUNDED = -3;

  const STATUS_NONE       = 0;
  const STATUS_OPTIONAL   = 1;
  const STATUS_REQUIRED   = 2;
  const STATUS_DEPRECATED = 4;

  abstract public function getName();

  /**
   * @return int static::CARDINALITY_* or a positive integer denoting a specific number of occurences
   */
  abstract public function getCardinality();
  abstract public function getDescription();

  /**
   * A Field status indicator (required, optional, deprecated, none)
   *
   * @return int static::STATUS_*
   */
  abstract public function getStatus();

  public function isRequired()
  {
    return $this->getStatus() == self::STATUS_REQUIRED;
  }

  public function isOptional()
  {
    return $this->getStatus() == self::STATUS_OPTIONAL;
  }

  public function isDeprecated()
  {
    return $this->getStatus() == self::STATUS_DEPRECATED;
  }
}