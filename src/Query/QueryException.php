<?php namespace EFrane\Transfugio\Query;

class QueryException extends \RuntimeException
{
    public static function queryMethodNotFoundException($methodName)
    {
        return new self("Query method {$methodName} not found.");
    }

    public static function unsuccesfulQueryException()
    {
        return new self("Query was not resolved successfully.");
    }

    public static function queryParseErrorException()
    {
        return new self("Query failed to evaluate into a ValueExpression");
    }
}
