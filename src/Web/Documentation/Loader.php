<?php namespace EFrane\Transfugio\Web\Documentation;

interface Loader
{
  /**
   * @param $modelName
   * @return Documentor
   */
  public function loadForModel($modelName);
}
