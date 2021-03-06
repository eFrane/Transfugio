<?php namespace EFrane\Transfugio\Http\Method;

use EFrane\Transfugio\Query\QueryException;
use EFrane\Transfugio\Query\QueryService;
use EFrane\Transfugio\Query\ValueExpression;
use Illuminate\Http\Request;

/**
 * Controller method for paginated indexes
 *
 * This represents the most common index method use case where
 * one needs listings of a model's entities.
 *
 * NOTE: Most of the work of this trait is done by the `QueryService`.
 *
 * @package EFrane\Transfugio\Http\Method
 * @see \EFrane\Transfugio\Query\QueryService
 * @see \EFrane\Transfugio\Http\APIController
 *
 * @property string $model
 * @method respondWithPaginated($data)
 * @method respondWithNotAllowed($message)
 * @method getModelName()
 */
trait IndexPaginatedTrait
{
    /**
     * It may sometimes be desirable to map a query to a specific parameter instead
     * of constructing a rather complicated where-clause. This may also be necessary
     * for compliance with third-party API specifications and the likes.
     *
     * Defining a parameter here results in this parameter being treated like
     * a regular where-clause parameter, i.e. if the query resolver happens upon it,
     * it will check whether a resolver for the parameter is registered and
     * call it.
     *
     * @var array additional query parameters
     */
    protected $queryParameters = [];

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $parameters = $request->only($this->getAllowedQueryParameters());

            $query = QueryService::create($this->model, $parameters, [&$this, 'resolveQuery']);

            return $this->respondWithPaginated($query->getQuery());
        } catch (QueryException $e) {
            return $this->respondWithNotAllowed("The requested query method is not allowed on `{$this->getModelName()}`.");
        }
    }

    /**
     * Resolve unfinished queries
     *
     * If the `QueryService` is unable to resolve all conditions on a query
     * the query remains unresolved. This gives entity controllers the
     * opportunity to implement resolvers for custom conditions that may
     * for instance require joins on the database or other information that is
     * not accessible to the `QueryService`.
     *
     * Controllers implementing custom resolvers have to follow the below
     * naming scheme for resolver methods:
     *
     * <code>
     *   protected function query<Field>(QueryService $query, ValueExpression $valueExpression);
     * </code>
     *
     * with `Field` being the camel case version of the field for the queried
     * constraint.
     *
     * If a resolver method can not be called successfully, the request will fail
     * into a `405 Method Not Allowed` explaining that the requested query method
     * is not allowed.
     *
     * NOTE: Although, `ValueExpression` delivers a lot of flexibility in terms of
     * evaluating input expressions, it is always possible to get the raw input expression with
     * `$valueExpression->getRaw()`.
     *
     * @param array $toResolve Conditions to resolve
     * @param QueryService $query
     * @throws QueryException on unresolvable queries
     * @return boolean resolve success
     **/
    public function resolveQuery(array $toResolve, QueryService $query)
    {
        foreach ($toResolve as $field => $valueExpression) {
            if (!is_a($valueExpression, ValueExpression::class)) {
                throw QueryException::queryParseErrorException();
            }

            $method = sprintf("query%s", ucfirst(camel_case($field)));

            if (!method_exists($this, $method)) {
                throw QueryException::queryMethodNotFoundException($method);
            }

            $this->{$method}($query, $valueExpression);
        }

        return true;
    }

    /**
     * Get the query parameters for the current controller
     *
     * @return array
     */
    protected function getAllowedQueryParameters()
    {
        $queryParameters = QueryService::getDefaultQueryParameters();
        $customParameters = $this->getCustomQueryParameters();

        if (is_array($customParameters) && count($customParameters) > 0) {
            $queryParameters = array_merge($queryParameters, $customParameters);
        }

        return $queryParameters;
    }

    protected function getCustomQueryParameters()
    {
        return [];
    }
}
