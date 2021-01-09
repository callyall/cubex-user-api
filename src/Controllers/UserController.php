<?php

namespace UserApi\Controllers;

use Generator;
use Packaged\Dal\Exceptions\DataStore\DaoNotFoundException;
use Packaged\Dal\Ql\QlDaoCollection;
use Packaged\Http\Response;
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
   * @return Response
   */
  public function getIndex(): Response
  {
    try
    {
      $users = $this->_search();
    }
    catch(\Exception $e)
    {
      return Response::create(
        json_encode(['error' => 'Something went wrong!']),
        500,
        ['Content-Type' => 'application/json']
      );
    }
    return Response::create($users, 200, ['Content-Type' => 'application/json']);
  }

  /**
   * @return Response
   */
  public function postIndex(UserApiContext $context): Response
  {
    $userForm = new UserForm();
    $errors = $userForm->hydrate(json_decode($context->request()->getContent(), true));

    if(!$userForm->isValid())
    {
      return Response::create(json_encode(['errors' => $errors]), 400, ['Content-Type' => 'application/json']);
    }

    $user = new User();
    $user->first_name = $userForm->first_name->getValue();
    $user->last_name = $userForm->last_name->getValue();
    $user->username = $userForm->username->getValue();
    $user->dark_mode = $userForm->dark_mode->getValue() === 'true';
    $user->date_created = date('Y-m-d H:i:s', time());
    $user->save();

    return Response::create($user, 200, ['Content-Type' => 'application/json']);
  }

  /**
   * @param UserApiContext $context
   *
   * @return Response
   */
  public function getUser(UserApiContext $context): Response
  {
    try
    {
      $user = User::loadById($context->routeData()->getInt('id'));
      return Response::create($user, 200, ['Content-Type' => 'application/json']);
    }
    catch(DaoNotFoundException $e)
    {
      return Response::create(
        json_encode(['error' => 'Resource not found!']),
        404,
        ['Content-Type' => 'application/json']
      );
    }
  }

  /**
   * @param UserApiContext $context
   *
   * @return Response
   */
  public function deleteUser(UserApiContext $context): Response
  {
    try
    {
      $user = User::loadById($context->routeData()->getInt('id'));
      $user->delete();
      return Response::create($user, 200, ['Content-Type' => 'application/json']);
    }
    catch(DaoNotFoundException $e)
    {
      return Response::create(
        json_encode(['error' => 'Resource not found!']),
        404,
        ['Content-Type' => 'application/json']
      );
    }
  }

  /**
   * @param UserApiContext $context
   *
   * @return Response
   */
  public function patchName(UserApiContext $context): Response
  {
    try
    {
      $user = User::loadById($context->routeData()->getInt('id'));
      $userForm = new UserForm();
      $userForm->hydrate(json_decode($context->request()->getContent(), true));

      if(!$userForm->first_name->isValid() || !$userForm->last_name->isValid())
      {
        return Response::create(
          json_encode(
            ['first_name' => $userForm->first_name->getErrors(), 'last_name' => $userForm->last_name->getErrors()]
          ),
          400,
          ['Content-Type' => 'application/json']
        );
      }

      $user->first_name = $userForm->first_name->getValue();
      $user->last_name = $userForm->last_name->getValue();
      $user->save();

      return Response::create($user, 200, ['Content-Type' => 'application/json']);
    }
    catch(DaoNotFoundException $e)
    {
      return Response::create(
        json_encode(['error' => 'Resource not found!']),
        404,
        ['Content-Type' => 'application/json']
      );
    }
  }

  /**
   * @param UserApiContext $context
   *
   * @return Response
   */
  public function patchDarkMode(UserApiContext $context): Response
  {
    try
    {
      $user = User::loadById($context->routeData()->getInt('id'));
      $user->dark_mode = !$user->dark_mode;
      $user->save();
      return Response::create($user, 200, ['Content-Type' => 'application/json']);
    }
    catch(DaoNotFoundException $e)
    {
      return Response::create(
        json_encode(['error' => 'Resource not found!']),
        404,
        ['Content-Type' => 'application/json']
      );
    }
  }

}