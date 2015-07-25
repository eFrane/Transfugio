<?php namespace EFrane\Transfugio\Web\Documentation;

use Illuminate\Support\Collection;

class DefaultDocumentor implements Documentor
{
  protected $modelName = '';
  protected $modelDescription = '';

  /**
   * @var \Illuminate\Support\Collection
   */
  protected $fields = null;

  /**
   * DefaultDocumentor constructor.
   *
   * @param string $modelName
   * @param string $modelDescription
   */
  public function __construct($modelName, $modelDescription)
  {
    $this->modelName = $modelName;
    $this->modelDescription = $modelDescription;

    $this->fields = new Collection();
  }

  public function addField(FieldDocumentor $field)
  {
    $this->fields->put($field->getName(), $field);
  }

  /**
   * @return string
   */
  public function getModelName()
  {
    return $this->modelName;
  }

  /**
   * @param string $modelName
   * @return DefaultDocumentor
   */
  public function setModelName($modelName)
  {
    $this->modelName = $modelName;
    return $this;
  }

  /**
   * @return string
   */
  public function getModelDescription()
  {
    return $this->modelDescription;
  }

  /**
   * @param string $modelDescription
   * @return DefaultDocumentor
   */
  public function setModelDescription($modelDescription)
  {
    $this->modelDescription = $modelDescription;
    return $this;
  }

  public function getRequiredFields()
  {
    // TODO: Implement getRequiredFields() method.
  }

  public function getOptionalFields()
  {
    // TODO: Implement getOptionalFields() method.
  }

  public function getDeprecatedFields()
  {
    // TODO: Implement getDeprecatedFields() method.
  }

  public function getFieldByName($fieldName)
  {
    // TODO: Implement getFieldByName() method.
  }
}