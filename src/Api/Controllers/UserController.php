<?php

namespace UserApi\Api\Controllers;

use Exception;
use Generator;
use Packaged\Dal\Exceptions\DataStore\DaoNotFoundException;
use Packaged\Http\Request;
use Packaged\Http\Responses\JsonResponse;
use UserApi\Api\Services\UserService;
use UserApi\Forms\UserForm;

class UserController extends AuthenticatedController
{

  protected function _generateRoutes(): Generator
  {

    yield self::_route('{id}/name', 'name');
    yield self::_route('{id}/dark-mode', 'darkMode');
    yield self::_route('{id}', 'user');

    return 'index';
  }

  /**
   * @param Request     $request
   * @param UserService $service
   *
   * @return JsonResponse
   * @throws Exception
   */
  public function getIndex(Request $request, UserService $service): JsonResponse
  {
    return JsonResponse::create($service->search($request->query));
  }

  /**
   * @param UserForm    $form
   * @param UserService $service
   *
   * @return JsonResponse
   */
  public function postIndex(UserForm $form, UserService $service): JsonResponse
  {
    $result = $service->createUser($form);

    if(!empty($result['errors']))
    {
      return JsonResponse::create(['errors' => $result['errors']], 400);
    }

    return JsonResponse::create($result['user']);
  }

  /**
   * @param int         $id
   * @param UserService $service
   *
   * @return JsonResponse
   * @throws DaoNotFoundException
   */
  public function getUser(int $id, UserService $service): JsonResponse
  {
    return JsonResponse::create($service->getUserById($id));
  }

  /**
   * @param int         $id
   * @param UserService $service
   *
   * @return JsonResponse
   * @throws DaoNotFoundException
   */
  public function deleteUser(int $id, UserService $service): JsonResponse
  {
    return JsonResponse::create($service->deleteUserById($id));
  }

  /**
   * @param int         $id
   * @param UserService $service
   * @param UserForm    $userForm
   *
   * @return JsonResponse
   * @throws DaoNotFoundException
   */
  public function patchName(int $id, UserService $service, UserForm $userForm): JsonResponse
  {
    $result = $service->updateUserName($id, $userForm);

    if(!empty($result['errors']))
    {
      return JsonResponse::create(['errors' => $result['errors']], 400);
    }

    return JsonResponse::create($result['user']);
  }

  /**
   * @param int         $id
   * @param UserService $service
   *
   * @return JsonResponse
   * @throws DaoNotFoundException
   */
  public function patchDarkMode(int $id, UserService $service): JsonResponse
  {
    return JsonResponse::create($service->toggleDarkMode($id));
  }

}
