<?php

namespace UserApi\Api\Controllers;

use Backend\Api\Customer\CustomerResponse;
use Backend\Api\Customer\IDRequest;
use Nikolaybrankov\BackendPhpClient\BackendProvider;
use Packaged\Http\Request;
use Grpc;
use Packaged\Http\Responses\JsonResponse;
use UserApi\Api\Services\CustomerService;

class CustomerController extends AbstractController
{

  protected function _generateRoutes()
  {
    yield self::_route('{id}', 'user');

    return 'index';
  }

  public function postIndex(Request $request)
  {

  }

  public function getUser(int $id, CustomerService $service)
  {
    /**
     * @var CustomerResponse $response
     * @var \stdClass        $status
     */
    try
    {
      return JsonResponse::create(
        [
          'error'    => null,
          'customer' => $service
            ->getById($id),
        ]
      );

    }
    catch (\Exception $e)
    {
      return JsonResponse::create(
        [
          'error'    => $e->getMessage(),
          'customer' => null,
        ]
      );
    }

  }
}
