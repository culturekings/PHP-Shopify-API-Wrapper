<?php


namespace Shopify\Tests;

use Shopify\Description;
use GuzzleHttp\Url;

/**
 * Class DescriptionTest
 * @package Shopify\Tests
 */
class DescriptionTest extends AbstractTestCase
{
    public function testSetter()
    {
        $description = new Description([
            Description::CONFIG_BASE_URL_KEY => 'http://constructed.example.com',
        ]);
        $baseUrl = Url::fromString('http://updated.example.com');
        $description->setBaseUrl($baseUrl);
        $this->assertEquals($baseUrl, $description->getBaseUrl());
    }
}
