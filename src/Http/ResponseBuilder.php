<?php namespace EFrane\Transfugio\Http;

use EFrane\Transfugio\Http\Formatter\FormatterException;
use EFrane\Transfugio\Http\Formatter\FormatterFactory;
use EFrane\Transfugio\Transformers\EloquentWorker;
use EFrane\Transfugio\Web\WebView;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ResponseBuilder
{
    protected $responseFormatter = null;
    protected $options = [];

    public function __construct($format, array $options = [])
    {
        try {
            $enabledFormatters = collect(config('transfugio.enabledFormatters'))->filter(function ($value) {
                return !!$value;
            });

            $formatterFactory = new FormatterFactory($enabledFormatters);

            $this->responseFormatter = $formatterFactory->get($format);
        } catch (FormatterException $e) {
            // TODO: this needs to actually store that there was an error and return it once respond() is called
            $this->respondWithError('The requested output format is disabled.');
        }

        $this->extractOptions($format, $options);
    }

    public function respondWithError($message = "An unknown error occurred, please try again later.", $status = 400)
    {
        $data = collect(['error' => ['message' => $message, 'status' => $status]]);

        return $this->respond($data, $status, true);
    }

    /**
     * @param Collection $data
     * @param int $status
     * @param bool $processed
     * @return Response
     */
    public function respond(Collection $data, $status = 200, $processed = false)
    {
        try {
            if (!$processed) {
                // remove unwanted output data
                if (count($this->options['only']) > 0) {
                    $data = collect(array_diff_key($data->toArray(), $this->options['only']));
                }

                // TODO: add reusable request parameters to output
            }

            // transform to output format
            $data = $this->responseFormatter->format($data);

            $headers = ['Content-type' => $this->responseFormatter->getContentType()];

            if (config('transfugio.http.enableCORS')) {
                $headers['Access-Control-Allow-Origin'] = '*';
            }

            $response = new Response($data, $status);

            foreach ($headers as $name => $value) {
                $response->header($name, $value);
            }

            return $response;
        } catch (\Exception $e)
        {
            return $this->respondWithError('An unknown error occurred.');
        }
    }

    /**
     * @param $format
     * @param array $options
     **/
    protected function extractOptions($format, array $options)
    {
        $this->options = array_merge([
            'only'      => [],
            'format'    => $format,
            'includes'  => [],
            'modelName' => '',
        ], $options);
    }

    public function respondWithPaginated(LengthAwarePaginator $paginated, $status = 200)
    {
        $this->options['paginationCode'] = $paginated->render();

        $this->prepareEloquentResult($paginated);

        return $this->respond(collect($paginated), $status);
    }

    protected function prepareEloquentResult(&$result)
    {
        $worker = new EloquentWorker($this->options['includes']);

        $result = ($result instanceof LengthAwarePaginator)
            ? $worker->transformPaginated($result)
            : $worker->transformModel($result);
    }

    public function respondWithModel(Model $item, $status = 200)
    {
        $this->prepareEloquentResult($item);

        return $this->respond(collect($item), $status);
    }

    /**
     * Return an empty response.
     *
     * APIs should never return nothing, thus a response stating that there is no
     * content is returned.
     *
     * The issue is, that the correct HTTP Status code for "No Content" is 204, which
     * is being used for all machine-readable output formats, however, if the
     * output will be a web view, 200 is set as status code in order to get
     * browsers to actually display the data.
     *
     * @param string $message
     * @param int $status
     * @return null
     */
    public function respondWithEmpty($message = "The requested result set is empty.", $status = 204)
    {
        return $this->respondWithError($message, $status);
    }

    public function respondWithForbidden($message = "Access to this resource is forbidden.", $status = 403)
    {
        return $this->respondWithError($message, $status);
    }

    public function respondWithNotFound($message = "The requested resource was not found.", $status = 404)
    {
        return $this->respondWithError($message, $status);
    }

    public function respondWithNotAllowed($message = "Access to the requested resource is not allowed.", $status = 405)
    {
        return $this->respondWithError($message, $status);
    }
}
