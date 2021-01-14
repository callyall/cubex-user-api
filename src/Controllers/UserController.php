<?php

namespace UserApi\Controllers;

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
   * @throws \Exception
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

    if($this->request()->query->has('first_name'))
    {
      $params[] = (new LikePredicate())->setField('first_name')->setExpression(
        ContainsExpression::create($this->request()->query->get('first_name'))
      );
    }

    if($this->request()->query->has('last_name'))
    {
      $params[] = (new LikePredicate())->setField('last_name')->setExpression(
        ContainsExpression::create($this->request()->query->get('last_name'))
      );
    }

    if($this->request()->query->has('username'))
    {
      $params[] = (new LikePredicate())->setField('username')->setExpression(
        ContainsExpression::create($this->request()->query->get('username'))
      );
    }

    if($this->request()->query->has('dark_mode'))
    {
      $dark_mode = $this->request()->query->get('dark_mode') === 'true';
      $params[] = (new EqualPredicate())->setField('dark_mode')->setExpression(
        ValueExpression::create($dark_mode)
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
   */
  public function getIndex(): JsonResponse
  {
    try
    {
      $users = $this->_search();
    }
    catch(\Exception $e)
    {
      return JsonResponse::create(['error' => 'Something went wrong!'], 500);
    }
    return JsonResponse::create($users);
  }

  /**
   * @param UserApiContext $context
   *
   * @return JsonResponse
   */
  public function postIndex(UserApiContext $context): JsonResponse
  {
    $userForm = new UserForm();
    $errors = $userForm->hydrate(json_decode($context->request()->getContent(), true));

    if(!$userForm->isValid())
    {
      return JsonResponse::create(['errors' => $errors], 400);
    }

    $user = new User();
    $user->first_name = $userForm->first_name->getValue();
    $user->last_name = $userForm->last_name->getValue();
    $user->username = $userForm->username->getValue();
    $user->dark_mode = $userForm->dark_mode->getValue() === 'true';
    $user->date_created = date('Y-m-d H:i:s', time());
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

    if(!$userForm->first_name->isValid() || !$userForm->last_name->isValid())
    {
      return JsonResponse::create(
        ['first_name' => $userForm->first_name->getErrors(), 'last_name' => $userForm->last_name->getErrors()],
        400
      );
    }

    $user->first_name = $userForm->first_name->getValue();
    $user->last_name = $userForm->last_name->getValue();
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
    $user->dark_mode = !$user->dark_mode;
    $user->save();
    return JsonResponse::create($user);
  }

}