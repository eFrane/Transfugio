<?php namespace EFrane\Transfugio;

use EFrane\Transfugio\Http\APIController;
use Illuminate\Support\Collection;

class ControllerRegistry
{
    /**
     * @var Collection
     */
    protected $controllers = null;

    public function __construct()
    {
        $this->controllers = new Collection();
    }

    public function push(APIController $controller)
    {
        $this->controllers->push($controller);
    }

    public function registerRoutes()
    {
        
    }
}