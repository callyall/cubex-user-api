<?php

namespace UserApi\Api\Controllers;

use Cubex\Controller\Controller;
use Packaged\Http\Responses\JsonResponse;
use UserApi\Context\UserApiContext;

class BarController extends Controller
{
  protected function _generateRoutes()
  {
    yield self::_route('/foo/bar/baz/qux', QuxController::class);
    yield self::_route('/baz', 'baz');

    return 'bar';
  }

  public function getBar(UserApiContext $context): JsonResponse
  {
    return JsonResponse::create(['message' => "A response from {$context->request()->path()}"]);
  }

  public function getBaz(UserApiContext $context): JsonResponse
  {
    return JsonResponse::create(['message' => "A response from {$context->request()->path()}"]);
  }
}
