<?php namespace EFrane\Transfugio\Web\Documentation;

interface Documentor
{
  public function getModelName();
  public function getModelDescription();

  public function addField(FieldDocumentor $field);
  public function getRequiredFields();
  public function getOptionalFields();
  public function getDeprecatedFields();
  public function getFieldByName($fieldName);
}