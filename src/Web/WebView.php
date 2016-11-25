<?php namespace EFrane\Transfugio\Web;

use Illuminate\Http\Response;

class WebView extends Response
{
  protected $json = [];

  protected $modelName = '';

  protected $paginationCode = '';

  protected $collectionClass = '';

  protected $error = false;

  public function __construct($data, $status)
  {
    $this->json = $data;
    parent::__construct('', $status);
  }

  public function setIsCollection($isCollection = true)
  {
    $this->collectionClass = ($isCollection) ? 'collection' : '';
  }

  /**
   * @param string $modelName
   */
  public function setModelName($modelName)
  {
    $this->modelName = $modelName;
  }

  public function setPaginationCode($paginationCode)
  {
    $this->paginationCode = $paginationCode;
  }

  public function setIsError($isError = true)
  {
    $this->error = $isError;
  }

  public function render()
  {
    $data = [
      'url'             => app('request')->url(),
      'json'            => $this->json,
      'module'          => $this->modelName,
      'isError'         => $this->error,
      'paginationCode'  => $this->paginationCode,
      'collectionClass' => $this->collectionClass,
    ];

    if (config('transfugio.web.documentationEnabled')) {
      $data = array_merge($data, [
        'documentationEnabled' => true,
        'documentation' => $this->loadDocumentation(),
      ]);
    }

    $this->setContent(view('transfugio::api.base', $data));
  }

  protected function loadDocumentation()
  {
    $loader = Documentation\LoaderFactory::getForType(config('transfugio.web.documentationType'));

    return $loader->loadForModel($this->modelName);
  }
}
