<?php namespace EFrane\Transfugio\Web\Documentation;

class DefaultFieldDocumentor extends FieldDocumentor
{
  protected $name = '';

  protected $description = '';

  protected $cardinality = 0;

  protected $status = FieldDocumentor::STATUS_NONE;

  protected $format = '';

  /**
   * DefaultFieldDocumentor constructor.
   *
   * @param string $name
   * @param string $description
   * @param int $cardinality
   * @param int $status
   */
  public function __construct($name, $description, $cardinality, $status)
  {
    $this->name = $name;
    $this->description = $description;
    $this->cardinality = $cardinality;
    $this->status = $status;
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
    return $this;
  }

  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }

  /**
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
    return $this;
  }

  /**
   * @return int
   */
  public function getCardinality()
  {
    return $this->cardinality;
  }

  /**
   * @param int $cardinality
   */
  public function setCardinality($cardinality)
  {
    $this->cardinality = $cardinality;
    return $this;
  }

  /**
   * @return int
   */
  public function getStatus()
  {
    return $this->status;
  }

  /**
   * @param int $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
    return $this;
  }

  /**
   * @return string
   */
  public function getFormat()
  {
    return $this->format;
  }

  /**
   * @param string $format
   */
  public function setFormat($format)
  {
    $this->format = $format;
  }
}