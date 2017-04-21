<?php namespace EFrane\Transfugio\Http;

use Illuminate\Http\Request;

class APIOutputFormatMiddleware
{
    public function handle($request, \Closure $next)
    {
        config(['transfugio.http.format' => $this->determineOutputFormat($request)]);

        return $next($request);
    }

    private function determineOutputFormat(Request $request)
    {
        if ($request->wantsJson()) {
            return 'json_accept';
        }

        $requestFormat = $request->input('format');
        if (in_array($requestFormat, ['json', 'xml', 'yaml', 'html'])) {
            return $requestFormat;
        }

        return 'json_accept';
    }
}