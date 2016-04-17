<?php namespace EFrane\Transfugio\Transformers;

use Illuminate\Support\Collection;
use League\Fractal\TransformerAbstract;

abstract class BaseTransformer extends TransformerAbstract
{
    private $availableFormatters = [];
    private $loadedFormatters = [];

    private $included = false;

    public function __construct($included = false)
    {
        $this->availableFormatters = config('transfugio.transformers.formatHelpers');
        $this->included = $included;
    }

    /**
     * @return array
     */
    public function getLoadedFormatters()
    {
        return $this->loadedFormatters;
    }

    /**
     * @param array $loadedFormatters
     */
    public function setLoadedFormatters($loadedFormatters)
    {
        $this->loadedFormatters = $loadedFormatters;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isIncluded()
    {
        return $this->included;
    }

    /**
     * @param boolean $included
     */
    public function setIncluded($included)
    {
        $this->included = $included;

        return $this;
    }

    /**
     * @return array
     */
    public function getAvailableFormatters()
    {
        return $this->availableFormatters;
    }

    /**
     * @param array $availableFormatters
     * @return BaseTransformer
     */
    public function setAvailableFormatters($availableFormatters)
    {
        $this->availableFormatters = $availableFormatters;

        return $this;
    }

    public function __call($formatHelperMethod, array $value)
    {
        // circumvent PHP sometimes prefering __call over existing methods
        if (method_exists($this, $formatHelperMethod)) {
            return call_user_func_array([&$this, $formatHelperMethod], $value);
        }

        $formatHelperName = strtolower(substr($formatHelperMethod, 6));

        if (array_key_exists($formatHelperName, $this->availableFormatters)) {
            // load the format helper
            if (!array_key_exists($formatHelperName, $this->loadedFormatters))
                $this->loadedFormatters[$formatHelperName] = new $this->availableFormatters[$formatHelperName];

            // check for null value
            if (is_null($value[0])) return null;

            return $this->loadedFormatters[$formatHelperName]->format($value[0]);
        }
    }

    /**
     * @inheritDoc
     */
    protected function item($data, $transformer, $resourceKey = null)
    {
        if ($this->isIncluded() && is_null($data)) return null;

        return parent::item($data, $transformer, $resourceKey);
    }

    /**
     * @inheritDoc
     */
    protected function collection($data, BaseTransformer $transformer, $resourceKey = null)
    {
        if ($transformer->isIncluded() && is_null($data)) return null;

        return parent::collection($data, $transformer, $resourceKey);
    }

    /**
     * Get a route list (object list) from a Collection
     *
     * @param string $routeName
     * @param Collection $collection
     * @param string $key
     * @return array
     **/
    protected function collectionRouteList($routeName, Collection $collection, $key = 'id')
    {
        $keys = $collection->pluck($key);

        return array_map(function ($key) use ($routeName) {
            return route($routeName, $key);
        }, $keys->all());
    }
}
