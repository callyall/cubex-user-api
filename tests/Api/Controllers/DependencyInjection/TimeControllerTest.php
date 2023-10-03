<?php

namespace UserApi\Tests\Api\Controllers\DependencyInjection;

use Carbon\Carbon;
use Packaged\Http\Request;
use PHPUnit\Framework\TestCase;
use UserApi\Api\Controllers\DependencyInjection\TimeController;
use UserApi\Services\TimeService;
use UserApi\Tests\Mock\Services\TimeServiceMock;
use UserApi\Tests\Mock\UserApiContextMock;

class TimeControllerTest extends TestCase
{
  public function testGetOk(): void
  {
    $service = new TimeServiceMock(2023);
    $controller = new TimeController();

    $response = json_decode($controller->getOk($service)->getContent());

    $this->assertEquals(2023, $response->year);

    $service->setYear(2024);

    $response = json_decode($controller->getOk($service)->getContent());

    $this->assertEquals(2024, $response->year);
  }

  public function testGetMeh(): void
  {
    $controller = new TimeController();
    $response = json_decode($controller->getMeh(new TimeService(Carbon::now(), 0))->getContent());

    $this->assertEquals(Carbon::now()->toDateString(), $response->date);

    $response = json_decode($controller->getMeh(new TimeService(Carbon::now(), 1))->getContent());

    $this->assertEquals(Carbon::now()->subDays(1)->toDateString(), $response->date);
  }

  public function testGetNotGood(): void
  {
    $controller = new TimeController();
    $context = new UserApiContextMock();
    $request = Request::create('/');

    $context->setRequest($request);
    $controller->setContext($context);
    $response = json_decode($controller->getNotGood()->getContent());

    $this->assertEquals(Carbon::now()->toDateTimeString(), ($response->{'date-time'}));

    $context = new UserApiContextMock();

    $request->query->add(['subDays' => 1]);
    $context->setRequest($request);
    $controller->setContext($context);
    $response = json_decode($controller->getNotGood()->getContent());

    $this->assertEquals(Carbon::now()->subDays(1)->toDateTimeString(), ($response->{'date-time'}));
  }

  public function testGetBad(): void
  {
    $controller = new TimeController();
    $context = new UserApiContextMock();
    $request = Request::create('/');

    $context->setRequest($request);
    $controller->setContext($context);
    $response = json_decode($controller->getBad($context)->getContent());

    $this->assertEquals(Carbon::now()->dayName, $response->weekday);

    $request->query->add(['subDays' => 1]);
    $response = json_decode($controller->getBad($context)->getContent());

    $this->assertEquals(Carbon::now()->subDays(1)->dayName, $response->weekday);
  }

  public function testGetWorst(): void
  {
    $controller = new TimeController();
    $context = new UserApiContextMock();
    $request = Request::create('/');

    $context->setRequest($request);
    $controller->setContext($context);
    $response = json_decode($controller->getWorst()->getContent());

    $this->assertEquals(Carbon::now()->timestamp, $response->timestamp);

    $request->query->add(['subDays' => 1]);
    $response = json_decode($controller->getWorst()->getContent());

    $this->assertEquals(Carbon::now()->subDays(1)->timestamp, $response->timestamp);
  }
}