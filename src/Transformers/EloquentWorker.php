<?php namespace EFrane\Transfugio\Transformers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class EloquentWorker implements TransformerWorker
{
    protected $manager = null;

    public function __construct(array $includes = [])
    {
        $this->manager = new Manager;

        $serializerClass = config('transfugio.transformers.serializer');

        if (!class_exists($serializerClass)) {
            throw new \LogicException("Serializer {$serializerClass} does not exist.");
        }

        $serializer = new $serializerClass;

        $this->manager->setSerializer($serializer);

        if (count($includes) > 0) {
            $this->manager->parseIncludes($includes);
        }

        $this->manager->setRecursionLimit(config('transfugio.transformers.recursionLimit'));
    }

    public function transformModel(Model $model)
    {
        $transformer = TransformerFactory::makeForModel($model);
        $resource = new Item($model, $transformer);

        if ($transformer->isIncluded()) {
            $resource->setResourceKey('included');
        }

        return $this->manager->createData($resource)->toArray();
    }

    public function transformPaginated(LengthAwarePaginator $paginator)
    {
        $collection = $paginator->getCollection();

        try {
            $transformer = TransformerFactory::makeForCollection($collection);

            $resource = new Collection($collection, $transformer);

            if ($transformer->isIncluded()) {
                $resource->setResourceKey('included');
            }

            $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));

            return $this->manager->createData($resource)->toArray();
        } catch (\OutOfRangeException $e) {
            $emptyResource = new Collection([], []);
            $emptyResource->setPaginator(new IlluminatePaginatorAdapter($paginator));

            return $this->manager->createData($emptyResource)->toArray();
        }
    }
}