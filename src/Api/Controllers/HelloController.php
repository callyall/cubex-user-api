<?php

namespace UserApi\Api\Controllers;

use Cubex\Controller\Controller;
use Packaged\Http\Responses\JsonResponse;

class HelloController extends Controller
{
  // SERVES /hello, but will also serve /hello/world, /hello/world/and/everything/else/passed...
  protected function _generateRoutes()
  {
    return 'hello';
  }

  public function getHello(): string
  {
    return JsonResponse::create(['message' => 'Hello World!']);
  }
}