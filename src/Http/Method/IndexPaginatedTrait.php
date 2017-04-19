<?php namespace EFrane\Transfugio\Http\Method;

use \Illuminate\Http\Request;

use EFrane\Transfugio\Query\QueryService;
use \EFrane\Transfugio\Query\QueryException;

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
 */
trait IndexPaginatedTrait
{
    /**
     * @var array default query parameters
     */
    protected $defaultQueryParameters = ['limit', 'where', 'include'];

    /**
     * It may sometimes be desirable to map a query to a specific parameter instead
     * of constructing a rather complicated where-clause. This may also be necessary
     * for compliance with third-party API specifications and the likes.
     *
     * Defining a parameter here results in this parameter being treated like
     * a regular where-clause parameter, i.e. if the query resolver happens upon it,
     * it will check wether a resolver for the parameter is registered and
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
    try
    {
        $queryParameters = $this->defaultQueryParameters;
        if (is_array($this->queryParameters) && count($this->queryParameters) > 0) {
            $queryParameters = array_merge($queryParameters, $this->queryParameters);
        }

      $parameters = $request->only($queryParameters);
      $query = QueryService::create($this->model, $parameters, [&$this, 'resolveQuery']);

      $paginated = $query->getQuery();
    } catch (QueryException $e)
    {
      return $this->respondWithNotAllowed("The requested query method is not allowed on `{$this->getModelName()}`.");
    }

    return $this->respondWithPaginated($paginated);
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
   * If a resolver method can not be called succesfully, the request will fail
   * into a `405 Method Not Allowed` explaining that the requested query method
   * is not allowed.
   *
   * NOTE: Although, `ValueExpression` delivers a lot of flexibility in terms of
   * evaluating input expressions, it is always possible to get the raw input expression with
   * `$valueExpression->getRaw()`.
   *
   * @param array $toResolve Conditions to resolve
   * @param QueryService $query
   * @throws QueryException on unresolveable queries
   * @return boolean resolve success
   **/
  protected function resolveQuery(array $toResolve, QueryService $query)
  {
    foreach ($toResolve as $field => $valueExpression) {
      $method = sprintf("query%s", ucfirst(camel_case($field)));

      if (method_exists($this, $method))
      {
        $this->{$method}($query, $valueExpression);
      } else
      {
        throw QueryException::queryMethodNotFoundException($method);
      }
    }

    return true;
  }

}