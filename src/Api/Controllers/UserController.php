<?php

namespace UserApi\Api\Controllers;

use Exception;
use Generator;
use Packaged\Dal\Exceptions\DataStore\DaoNotFoundException;
use Packaged\Dal\Ql\QlDaoCollection;
use Packaged\Http\Responses\JsonResponse;
use Packaged\QueryBuilder\Expression\Like\ContainsExpression;
use Packaged\QueryBuilder\Expression\ValueExpression;
use Packaged\QueryBuilder\Predicate\EqualPredicate;
use Packaged\QueryBuilder\Predicate\LikePredicate;
use UserApi\Context\UserApiContext;
use UserApi\Forms\UserForm;
use UserApi\Models\User;

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
   * @return QlDaoCollection
   * @throws Exception
   */
  protected function _search(): QlDaoCollection
  {
    $users = User::collection();
    $params = [];
    $orderBy = [];

    if($this->request()->query->has('limit'))
    {
      $users->limit($this->request()->query->get('limit'));
    }

    if($this->request()->query->has('firstName'))
    {
      $params[] = (new LikePredicate())->setField('firstName')->setExpression(
        ContainsExpression::create($this->request()->query->get('firstName'))
      );
    }

    if($this->request()->query->has('lastName'))
    {
      $params[] = (new LikePredicate())->setField('lastName')->setExpression(
        ContainsExpression::create($this->request()->query->get('lastName'))
      );
    }

    if($this->request()->query->has('username'))
    {
      $params[] = (new LikePredicate())->setField('username')->setExpression(
        ContainsExpression::create($this->request()->query->get('username'))
      );
    }

    if($this->request()->query->has('darkMode'))
    {
      $darkMode = $this->request()->query->get('darkMode') === 'true';
      $params[] = (new EqualPredicate())->setField('darkMode')->setExpression(
        ValueExpression::create($darkMode)
      );
    }

    if($this->request()->query->has('asc'))
    {
      $fields = explode(',', $this->request()->query->get('asc'));
      foreach($fields as $field)
      {
        $orderBy[$field] = 'asc';
      }
    }

    if($this->request()->query->has('desc'))
    {
      $fields = explode(',', $this->request()->query->get('desc'));
      foreach($fields as $field)
      {
        $orderBy[$field] = 'desc';
      }
    }

    if(count($orderBy))
    {
      $users->orderBy($orderBy);
    }

    if(count($params))
    {
      $users->where($params);
    }

    return $users->load();
  }

  /**
   * @return JsonResponse
   * @throws Exception
   */
  public function getIndex(): JsonResponse
  {
    return JsonResponse::create($this->_search());
  }

  /**
   * @param UserApiContext $context
   *
   * @return JsonResponse
   */
  public function postIndex(UserApiContext $context): JsonResponse
  {
    $userForm = new UserForm();
    $userForm->hydrate(json_decode($context->request()->getContent(), true));
    $errors = $context->getErrorMessages($userForm->validate());
    if(count($errors))
    {
      return JsonResponse::create(['errors' => $errors], 400);
    }

    $user = new User();
    $user->firstName = $userForm->firstName->getValue();
    $user->lastName = $userForm->lastName->getValue();
    $user->username = $userForm->username->getValue();
    $user->darkMode = $userForm->darkMode->getValue() === 'true';
    $user->dateCreated = date('Y-m-d H:i:s', time());
    $user->save();
    return JsonResponse::create($user);
  }

  /**
   * @param UserApiContext $context
   *
   * @return JsonResponse
   * @throws DaoNotFoundException
   */
  public function getUser(UserApiContext $context): JsonResponse
  {
    return JsonResponse::create(User::loadById($context->routeData()->getInt('id')));
  }

  /**
   * @param UserApiContext $context
   *
   * @return JsonResponse
   * @throws DaoNotFoundException
   */
  public function deleteUser(UserApiContext $context): JsonResponse
  {
    $user = User::loadById($context->routeData()->getInt('id'));
    $user->delete();
    return JsonResponse::create($user);
  }

  /**
   * @param UserApiContext $context
   *
   * @return JsonResponse
   * @throws DaoNotFoundException
   */
  public function patchName(UserApiContext $context): JsonResponse
  {
    $user = User::loadById($context->routeData()->getInt('id'));
    $userForm = new UserForm();
    $userForm->hydrate(json_decode($context->request()->getContent(), true));
    $errors = $context->getErrorMessages(['firstName' => $userForm->firstName->validate(), 'lastName' => $userForm->lastName->validate()]);
    if(count($errors))
    {
      return JsonResponse::create(['errors' => $errors], 400);
    }

    $user->firstName = $userForm->firstName->getValue();
    $user->lastName = $userForm->lastName->getValue();
    $user->save();

    return JsonResponse::create($user);
  }

  /**
   * @param UserApiContext $context
   *
   * @return JsonResponse
   * @throws DaoNotFoundException
   */
  public function patchDarkMode(UserApiContext $context): JsonResponse
  {
    $user = User::loadById($context->routeData()->getInt('id'));
    $user->darkMode = !$user->darkMode;
    $user->save();
    return JsonResponse::create($user);
  }

}
