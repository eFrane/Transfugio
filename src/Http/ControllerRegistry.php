<?php namespace EFrane\Transfugio\Http;

use Illuminate\Support\Collection;

class ControllerRegistry
{
    /**
     * @var Collection
     */
    protected $controllers = null;

    /*
     * array[string] mapping of controller method keywords to http verb
     */
    protected $verbs = [
        'index' => ['GET', 'HEAD'],
        'show'  => ['GET', 'HEAD'],
    ];

    public function __construct()
    {
        $this->controllers = new Collection();
    }

    public function push(APIController $controller)
    {
        $this->controllers->put(get_class($controller), $controller);
    }

    public function getRoutes()
    {
        $verbs = collect($this->verbs);

        return $this->controllers->flatMap(function (APIController $controller) {
            // map controllers to arrays of controller functions => http method
            $reflectionClass = new \ReflectionClass($controller);

            return $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
        })->flatMap(function (\ReflectionMethod $method) use ($verbs) {
            $verbs->map(function ($http, $controller) use ($method) {
                if (starts_with($method->getName(), $controller)) {
                    return [$http, $method];
                }

                return [];
            });
        }); // TODO: actually get the real routes
    }
}