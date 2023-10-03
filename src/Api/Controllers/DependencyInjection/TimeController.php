<?php

namespace UserApi\Api\Controllers\DependencyInjection;

use Carbon\Carbon;
use Packaged\Http\Responses\JsonResponse;
use UserApi\Context\UserApiContext;
use UserApi\Services\TimeService;
use UserApi\Services\TimeServiceInterface;

/**
 * @method UserApiContext getContext()
 */
class TimeController extends AbstractDiController
{
  protected function _generateRoutes()
  {
    yield self::_route('/di/time/ok', 'ok');
    yield self::_route('/di/time/meh', 'meh');
    yield self::_route('/di/time/not-good', 'notGood');
    yield self::_route('/di/time/bad', 'bad');
    return 'worst';
  }

  public function getOk(TimeServiceInterface $timeService): JsonResponse
  {
    return JsonResponse::create(['year' => $timeService->getYear()]);
  }

  public function getMeh(TimeService $timeService): JsonResponse
  {
    return JsonResponse::create(['date' => $timeService->getDateString()]);
  }

  public function getNotGood(): JsonResponse
  {
    $timeService = $this->getContext()->getPersistentTimeService();

    return JsonResponse::create(['date-time' => $timeService->getDateTimeString()]);
  }

  public function getBad(UserApiContext $context): JsonResponse
  {
    $timeService = $context->getTimeService();

    return JsonResponse::create(['weekday' => $timeService->getDayOfTheWeek()]);
  }

  public function getWorst(): JsonResponse
  {
    $timeService = new TimeService(Carbon::now(), $this->request()->query->getInt('subDays'));

    return JsonResponse::create(['timestamp' => $timeService->getTimestamp()]);
  }
}
