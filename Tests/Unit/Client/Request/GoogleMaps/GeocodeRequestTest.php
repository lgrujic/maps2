<?php
namespace JWeiland\Maps2\Tests\Unit\Client\Request\GoogleMaps;

/*
 * This file is part of the maps2 project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use JWeiland\Maps2\Client\Request\GoogleMaps\GeocodeRequest;
use JWeiland\Maps2\Configuration\ExtConf;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Test Google Maps Geocode Request class
 */
class GeocodeRequestTest extends UnitTestCase
{
    /**
     * @var ExtConf
     */
    protected $extConf;

    /**
     * @var GeocodeRequest
     */
    protected $subject;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->extConf = new ExtConf();
        $this->subject = new GeocodeRequest($this->extConf);
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unset(
            $this->subject,
            $this->extConf
        );
        parent::tearDown();
    }

    /**
     * @test
     */
    public function setUriSetsUri()
    {
        $uri = 'https://www.jweiland.net';
        $this->subject->setUri($uri);
        $this->assertSame(
            $uri,
            $this->subject->getUri()
        );
    }

    /**
     * @test
     */
    public function setParametersSetsParameters()
    {
        $parameters = [
            'uri' => 'https://www.jweiland.net',
            'address' => 'Echterdinger Straße 57'
        ];
        $this->subject->setParameters($parameters);
        $this->assertSame(
            $parameters,
            $this->subject->getParameters()
        );
    }

    /**
     * @test
     */
    public function addParameterSetsParameter()
    {
        $parameters = [
            'uri' => 'https://www.jweiland.net',
            'address' => 'Echterdinger Straße 57'
        ];
        $this->subject->setParameters($parameters);
        $this->subject->addParameter('city', 'Filderstadt');
        $this->assertSame(
            'Filderstadt',
            $this->subject->getParameter('city')
        );
        $this->assertCount(
            3,
            $this->subject->getParameters()
        );
    }

    /**
     * @test
     */
    public function hasParameterReturnsTrue()
    {
        $parameters = [
            'uri' => 'https://www.jweiland.net',
            'address' => 'Echterdinger Straße 57'
        ];
        $this->subject->setParameters($parameters);
        $this->assertTrue(
            $this->subject->hasParameter('uri')
        );
    }

    /**
     * @test
     */
    public function hasParameterReturnsFalse()
    {
        $parameters = [
            'uri' => 'https://www.jweiland.net',
            'address' => 'Echterdinger Straße 57'
        ];
        $this->subject->setParameters($parameters);
        $this->assertFalse(
            $this->subject->hasParameter('city')
        );
    }

    /**
     * @test
     */
    public function isValidRequestWithEmptyUriReturnsFalse()
    {
        $this->subject->setUri('  ');
        $this->assertFalse(
            $this->subject->isValidRequest()
        );
    }

    /**
     * @test
     */
    public function isValidRequestWithInvalidUriReturnsFalse()
    {
        $this->subject->setUri('nice try');
        $this->assertFalse(
            $this->subject->isValidRequest()
        );
    }

    /**
     * @test
     */
    public function isValidRequestWithValidUriReturnsTrue()
    {
        $this->subject->setUri('https://www.jweiland.net/%s/what/ever/%s.html');
        $this->assertTrue(
            $this->subject->isValidRequest()
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function getUriWillAddAddressAndApiKeyToUri()
    {
        $this->extConf->setGoogleMapsGeocodeApiKey('MyApiKey');
        $this->subject->setUri('%s:%s');
        $this->subject->addParameter('address', 'My Address');
        $this->assertSame(
            'My%20Address:MyApiKey',
            $this->subject->getUri()
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function getUriAddsAddressAndApiKeyToUriButUriIsInvalid()
    {
        $this->extConf->setGoogleMapsGeocodeApiKey('MyApiKey');
        $this->subject->setUri('%s:%s');
        $this->subject->addParameter('address', 'My Address');
        $this->assertFalse(
            $this->subject->isValidRequest()
        );
    }
}
