<?php
namespace JWeiland\Maps2\Tests\Unit\ViewHelpers\Widget;

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

use JWeiland\Maps2\Configuration\ExtConf;
use JWeiland\Maps2\Service\GoogleRequestService;
use JWeiland\Maps2\Service\GoogleMapsService;
use JWeiland\Maps2\Service\MapProviderRequestService;
use JWeiland\Maps2\ViewHelpers\Widget\PoiCollectionViewHelper;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Class PoiCollectionViewHelperTest
 */
class PoiCollectionViewHelperTest extends UnitTestCase
{
    /**
     * @var ExtConf
     */
    protected $extConf;

    /**
     * @var \Prophecy\Prophecy\ObjectProphecy|GoogleMapsService
     */
    protected $googleMapsService;

    /**
     * @var MapProviderRequestService
     */
    protected $mapProviderRequestService;

    /**
     * @var PoiCollectionViewHelper
     */
    protected $subject;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $_SESSION['mapProviderRequestsAllowedForMaps2'] = false;

        $this->extConf = new ExtConf();
        $this->extConf->setExplicitAllowMapProviderRequests(1);
        $this->extConf->setExplicitAllowMapProviderRequestsBySessionOnly(1);

        $this->googleMapsService = $this->prophesize(GoogleMapsService::class);
        $this->mapProviderRequestService = new MapProviderRequestService($this->extConf);

        $this->subject = new PoiCollectionViewHelper();
        $this->subject->injectGoogleRequestService($this->mapProviderRequestService);
        $this->subject->injectGoogleMapsService($this->googleMapsService->reveal());
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unset($this->extConf, $this->googleMapsService, $this->mapProviderRequestService, $this->subject);
        parent::tearDown();
    }

    /**
     * @test
     */
    public function renderWillCallShowAllowMapFormWhenGoogleRequestsAreNotAllowed()
    {
        $this->googleMapsService->showAllowMapForm()->shouldBeCalled()->willReturn('Please activate maps2');

        $this->assertSame(
            'Please activate maps2',
            $this->subject->render()
        );
    }

    /**
     * @test
     */
    public function renderWillReturnAWidgetSubRequestObjectWhenGoogleRequestsAreAllowed()
    {
        $this->extConf->setExplicitAllowMapProviderRequests(0);
        $this->extConf->setExplicitAllowMapProviderRequestsBySessionOnly(0);
        $this->googleMapsService->showAllowMapForm()->shouldNotBeCalled();

        /** @var \Prophecy\Prophecy\ObjectProphecy|PoiCollectionViewHelper $object */
        $object = $this->prophesize(PoiCollectionViewHelper::class);
        $object->injectGoogleRequestService($this->mapProviderRequestService)->shouldBeCalled();
        $object->injectGoogleMapsService($this->googleMapsService->reveal())->shouldBeCalled();
        $object->render()->shouldBeCalled()->willReturn('maps2 output');

        /** @var PoiCollectionViewHelper $subject */
        $subject = $object->reveal();
        $subject->injectGoogleMapsService($this->googleMapsService->reveal());
        $subject->injectGoogleRequestService($this->mapProviderRequestService);

        $this->assertSame(
            'maps2 output',
            $subject->render()
        );
    }
}
