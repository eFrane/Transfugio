<?php namespace EFrane\Transfugio\Http;

use Illuminate\Http\Request;
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
        $this->checkForValidImplementingClass($request);
        $this->configureOutputLimit($request);
        $this->configureOutputFormat();
    }

    protected function checkForValidImplementingClass(Request $request)
    {
        $this->validateModel();
        $this->validateItemId();

        $this->request = $request;
    }

    protected function validateModel()
    {
        if (!is_string($this->model)) {
            $controllerName = get_called_class();
            $controllerNameComponents = explode('\\', $controllerName);

            $baseName = array_pop($controllerNameComponents);
            $baseName = substr($baseName, 0, strrpos($baseName, 'Controller'));

            if (strlen($baseName) > 0) {
                $modelClass = sprintf('%s%s', config('transfugio.modelNamespace'), $baseName);
                if (class_exists($modelClass)) {
                    $this->model = $modelClass;
                } else {
                    throw new \LogicException("API controllers must have a valid \$model property.");
                }
            }
        }
    }

    protected function validateItemId()
    {
        if (((property_exists($this, 'item_id') && $this->item_id !== 0)
                || !property_exists($this, 'item_id')) && !class_exists($this->model)
        ) {
            throw new \LogicException("The requested model {$this->model} is invalid.");
        }
    }

    /**
     * @param Request $request
     */
    protected function configureOutputLimit(Request $request)
    {
        if ($request->input('only')) {
            $only = explode(',', $request->input('only'));

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

    protected function configureOutputFormat()
    {
        $this->format = config('transfugio.http.format');
    }

    public function __call($method, $parameters)
    {
        if (starts_with($method, 'respond')) {
            $responseBuilder = new ResponseBuilder($this->format, [
                'only'      => $this->only,
                'modelName' => $this->getModelName(),
                'includes'  => ($this->request->has('includes')) ? $this->request->get('includes') : [],
            ]);

            return call_user_func_array([$responseBuilder, $method], $parameters);
        }

        return parent::__call($method, $parameters);
    }

    public function getModelName()
    {
        return class_basename($this->model);
    }
}