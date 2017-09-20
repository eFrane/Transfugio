<?php

if (!function_exists('encode_where')) {
    /**
     * @param array $where
     * @return string
     **/
    function encode_where(array $where)
    {
        $parameters = [];

        foreach ($where as $key => $value) {
            $parameters[] = sprintf('%s_%s', $key, $value);
        }

        return implode(',', $parameters);
    }
}

if (!function_exists('decode_where')) {
    /**
     * @param string $where
     * @return array
     **/
    function decode_where($where = '')
    {
        $where = explode(',', $where);

        $clauses = [];
        foreach ($where as $clause) {
            $colon = strpos($clause, '_');

            if ($colon > 0) {
                $key = substr($clause, 0, $colon);
                $value = substr($clause, $colon + 1);

                $clauses[$key] = $value;
            }
        }

        return $clauses;
    }
}

if (!function_exists('route_where')) {
    /**
     * @param $name
     * @param array $where
     * @param bool $absolute
     * @param null $route
     * @return string
     **/
    function route_where($name, $where = [], $absolute = true, $route = null)
    {
        $where = encode_where($where);

        $url = route($name, [], $absolute, $route);
        $url = sprintf('%s?where=%s', $url, $where);

        return $url;
    }
}

if (!function_exists('remove_empty_keys')) {
    function remove_empty_keys(array $array) {
        return collect($array)->filter(function ($elem) {
            return !(is_null($elem) || (is_bool($elem) && !$elem));
        })->toArray();
    }
};
