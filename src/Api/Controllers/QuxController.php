<?php

namespace UserApi\Api\Controllers;

use Cubex\Controller\Controller;
use Packaged\Http\Responses\JsonResponse;
use UserApi\Context\UserApiContext;

class QuxController extends Controller
{
  // THIS CONTROLLER SERVES /foo/bar/baz/qux
  protected function _generateRoutes()
  {
    // WON'T WORK!!! IT SHOULD BE /foo/bar/baz/qux/fred
    yield self::_route('/fred', 'fred');

    return 'qux';
  }

  public function getQux(UserApiContext $context): JsonResponse
  {
    return JsonResponse::create(['message' => "A response from {$context->request()->path()}"]);
  }

  // SPOILER ALERT - WON'T WORK
  public function getFred(UserApiContext $context): JsonResponse
  {
    return JsonResponse::create(['message' => "A response from {$context->request()->path()}"]);
  }
}
