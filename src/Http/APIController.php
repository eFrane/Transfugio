<?php namespace EFrane\Transfugio\Http;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

/**
 * Class APIController
 * @package App\Http\Controllers\API
 */
class APIController extends Controller
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request = null;

    /**
     * @var \Illuminate\Database\Eloquent\Model
     **/
    protected $model = null;

    /**
     * @var string output format
     **/
    protected $format = '';

    /**
     * @var array
     **/
    protected $only = [];

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;

        $this->checkForValidImplementingClass();

        $this->configureOutputLimit();
        $this->configureOutputFormat();
    }

    /**
     * Check wether the model class exists and, when given, if the
     * item id is a valid id.
     *
     * @throws APIControllerException
     */
    protected function checkForValidImplementingClass()
    {
        $this->validateModel();
        $this->validateItemId();
    }

    /**
     * Validate the existence of the controller's corresponding model
     * or determine the model based on the controller's name.
     *
     * @throws APIControllerException
     */
    protected function validateModel()
    {
        if (!is_string($this->model)) {
            $baseName = $this->getControllerBaseName();
            $this->findModelClass($baseName);
        }
    }

    /**
     * Determine the Controller's base name
     *
     * @return string
     */
    protected function getControllerBaseName()
    {
        $controllerName = get_called_class();
        $controllerNameComponents = explode('\\', $controllerName);

        $baseName = array_pop($controllerNameComponents);
        $baseName = substr($baseName, 0, strrpos($baseName, 'Controller'));

        return $baseName;
    }

    /**
     * @param $baseName
     * @throws APIControllerException
     */
    protected function findModelClass($baseName)
    {
        if (strlen($baseName) > 0) {
            $modelClass = sprintf('%s%s', config('transfugio.modelNamespace'), $baseName);
            if (class_exists($modelClass)) {
                $this->model = $modelClass;
            } else {
                throw APIControllerException::invalidModelPropertyException();
            }
        }
    }

    /**
     * @throws APIControllerException
     */
    protected function validateItemId()
    {
        if (((property_exists($this, 'item_id') && $this->item_id !== 0)
                || !property_exists($this, 'item_id')) && !class_exists($this->model)
        ) {
            throw APIControllerException::invalidItemIdPropertyException();
        }
    }

    /**
     * Limit what's returned
     */
    protected function configureOutputLimit()
    {
        if ($this->request->input('only')) {
            $only = explode(',', $this->request->input('only'));

            $validator = app('validator')->make(
                ['only' => $only],
                ['only' => 'array|in:data,meta']
            );

            if ($validator->failed()) {
                $this->respondWithError("Invalid value for `only`.");
            } else {
                $this->only = array_flip(array_diff(['data', 'meta'], $only));
            }
        }
    }

    /**
     * Configure the output format
     */
    protected function configureOutputFormat()
    {
        $requestFormat = $this->request->input('format');

        $this->format = config('transfugio.http.format');

        if ($this->request->wantsJson()) {
            $this->format = 'json_accept';
        }

        if (in_array($requestFormat, ['json', 'xml', 'yaml', 'html'])) {
            $this->format = $requestFormat;
        }
    }

    /**
     * @param string $method
     * @param array $parameters
     * @return Response|mixed
     */
    public function __call($method, $parameters)
    {
        if (starts_with($method, 'respond')) {
            return $this->buildResponse($method, $parameters);
        }

        return parent::__call($method, $parameters);
    }

    /**
     * @param $method
     * @param $parameters
     * @return Response
     */
    protected function buildResponse($method, $parameters)
    {
        $responseBuilder = new ResponseBuilder($this->format, [
            'only'      => $this->only,
            'modelName' => $this->getModelName(),
            'includes'  => ($this->request->has('includes')) ? $this->request->get('includes') : [],
        ]);

        return call_user_func_array([$responseBuilder, $method], $parameters);
    }

    public function getModelName()
    {
        return class_basename($this->model);
    }
}
