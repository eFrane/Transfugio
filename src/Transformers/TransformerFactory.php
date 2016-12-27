<?php namespace EFrane\Transfugio\Transformers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class TransformerFactory
{
    protected static $instance = null;

    protected function __construct()
    {
    }

    static public function makeForCollection(Collection $collection)
    {
        if ($collection->count() === 0) {
            throw new \OutOfRangeException("Requested collection does not contain any items");
        }

        return static::makeForModel($collection->first());
    }

    static public function makeForModel(Model $model)
    {
        /* @var TransformerFactory $instance */
        $instance = static::get();

        $className = $instance->determineTransformerClass($model);
        return $instance->makeTransformer($className);
    }

    protected static function get()
    {
        if (static::$instance === null) {
            static::$instance = new TransformerFactory;
        }

        return static::$instance;
    }

    protected function determineTransformerClass(Model $model)
    {
        $modelName = class_basename($model);
        $transformerNamespace = config('transfugio.transformers.namespace');

        $classPattern = config('transfugio.transformers.classPattern');
        $classBaseName = str_replace('[:modelName]', $modelName, $classPattern);

        $transformerClass = sprintf('%s\%s', $transformerNamespace, $classBaseName);

        return $transformerClass;
    }

    protected function makeTransformer($className)
    {
        if (class_exists($className)) {
            return new $className;
        } else {
            throw new \LogicException("Could not instantiate transformer {$className}.");
        }
    }

    private function __clone()
    {
    }
}
