<?php

namespace Shopify;
	
use GuzzleHttp\Command\Guzzle\Description as GuzzleDescription;

class Description extends GuzzleDescription
{
    /** @var string $baseUrl */
    protected $baseUrl;

     /**
     *
     * @param  string  $url
     * @return void
     */
     public function setBaseUrl($url)
     {
          $this->baseUrl = $url;
     }
}
