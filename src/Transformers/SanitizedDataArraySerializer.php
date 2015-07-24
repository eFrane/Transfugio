<?php namespace EFrane\Transfugio\Transformers;

use League\Fractal\Serializer\DataArraySerializer;
use League\Url\Url;

class SanitizedDataArraySerializer extends DataArraySerializer
{
  public function item($resourceKey, array $data)
  {
    $data = $this->reformatData($data);

    return parent::item($resourceKey, $data);
  }

  public function collection($resourceKey, array $data)
  {
    $data = $this->reformatData($data);

    return parent::collection($resourceKey, $data);
  }

  protected function applyFormatParameterToUrls(array $data)
  {
    return array_map(function ($value) {
      if (is_array($value))
        return $this->applyFormatParameterToUrls($value);

      $pattern = sprintf('/%s.+/', preg_quote(url(config('transfugio.rootURL')), '/'));
      $format = config('transfugio.http.format');

      if (is_string($value)           // urls are string values
      && preg_match($pattern, $value) // that match the api's base url
      && $format !== 'json_accept')   // the format parameter is not applied for json_accept
      {
        $url = Url::createFromUrl($value);
        $url->getQuery()->modify(['format' => $format]);

        $value = (string)$url;
      }

      return $value;
    }, $data);
  }

  /**
   * @param array $data
   * @return array
   **/
  protected function reformatData(array $data)
  {
    foreach ($data as $key => $value) {
      if (is_null($value)
        || (is_array($value) && count($value) == 0)
      ) unset($data[$key]);
    }

    return $this->applyFormatParameterToUrls($data);
  }
}