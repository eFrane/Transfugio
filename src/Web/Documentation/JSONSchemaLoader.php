<?php namespace EFrane\Transfugio\Web\Documentation;

class JSONSchemaLoader implements Loader
{
    public function loadForModel($modelName)
    {
        try {
            $path = base_path(config('transfugio.web.documentationRoot'));

            $json = file_get_contents(sprintf('%s/%s.json', $path, ucfirst($modelName)));

            $schema = json_decode($json, true);
        } catch (\ErrorException $e) {
            $schema = null;
            // TODO: better error handling, like, fallback to phpdoc for instance
        }

        $documentor = new DefaultDocumentor($schema['title'], $schema['description']);

        foreach ($schema['properties'] as $fieldName => $options) {
            $description = (isset($options['description'])) ? $options['description'] : '';

            $status = FieldDocumentor::STATUS_NONE;
            if (isset($schema['required']) && in_array($fieldName, $schema['required'])) {
                $status = FieldDocumentor::STATUS_REQUIRED;
            }

            $cardinality = FieldDocumentor::CARDINALITY_ONE;
            if (strtolower($options['type']) === 'array') {
                $cardinality = FieldDocumentor::CARDINALITY_UNBOUNDED;
            }

            // TODO: other field data (specific references, ...)

            $field = new DefaultFieldDocumentor($fieldName, $description, $cardinality, $status);

            if (isset($options['format'])) {
                $field->setFormat($options['format']);
            } else if ($field->getCardinality() == FieldDocumentor::CARDINALITY_UNBOUNDED && isset($options['items']['format'])) {
                $field->setFormat($options['items']['format']);
            }

            $documentor->addField($field);
        }

        return $documentor;
    }
}