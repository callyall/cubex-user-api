<?php

namespace UserApi\Api\Services;

use Packaged\Dal\Exceptions\DataStore\DaoNotFoundException;
use Packaged\Dal\Ql\QlDaoCollection;
use Packaged\QueryBuilder\Expression\Like\ContainsExpression;
use Packaged\QueryBuilder\Expression\ValueExpression;
use Packaged\QueryBuilder\Predicate\EqualPredicate;
use Packaged\QueryBuilder\Predicate\LikePredicate;
use Symfony\Component\HttpFoundation\ParameterBag;
use UserApi\Api\Forms\UserForm;
use UserApi\Models\User;

class UserService implements ServiceInterface
{
  /**
   * @param int $id
   *
   * @return User
   * @throws DaoNotFoundException
   */
  public function getUserById(int $id)
  {
    return User::loadById($id);
  }

  /**
   * @param int $id
   *
   * @return User
   * @throws DaoNotFoundException
   */
  public function deleteUserById(int $id)
  {
    $user = $this->getUserById($id);
    $user->delete();

    return $user;
  }

  /**
   * @param UserForm $form
   *
   * @return array
   */
  public function createUser(UserForm $form)
  {
    $errors = $this->_getErrors($form->validate());

    if(!empty($errors))
    {
      return [
        'errors' => $errors,
        'user'   => null,
      ];
    }

    $user = new User();
    $user->firstName = $form->firstName->getValue();
    $user->lastName = $form->lastName->getValue();
    $user->username = $form->username->getValue();
    $user->darkMode = $form->darkMode->getValue() === 'true';
    $user->dateCreated = date('Y-m-d H:i:s', time());
    $user->save();

    return [
      'errors' => $errors,
      'user'   => $user,
    ];
  }

  /**
   * @param int      $id
   * @param UserForm $form
   *
   * @return array
   * @throws DaoNotFoundException
   */
  public function updateUserName(int $id, UserForm $form)
  {
    $errors = $this->_getErrors($form->validate());

    if(!empty($errors))
    {
      return [
        'errors' => $errors,
        'user'   => null,
      ];
    }

    $user = $this->getUserById($id);

    $user->firstName = $form->firstName->getValue();
    $user->lastName = $form->lastName->getValue();
    $user->save();

    return [
      'errors' => $errors,
      'user'   => $user,
    ];
  }

  /**
   * @param int $id
   *
   * @return User
   * @throws DaoNotFoundException
   */
  public function toggleDarkMode(int $id)
  {
    $user = $this->getUserById($id);
    $user->darkMode = !$user->darkMode;
    $user->save();

    return $user;
  }

  /**
   * @param ParameterBag $query
   *
   * @return QlDaoCollection
   * @throws \Exception
   */
  public function search(ParameterBag $query)
  {
    $users = User::collection();
    $params = [];
    $orderBy = [];

    if($query->has('limit'))
    {
      $users->limit($query->get('limit'));
    }

    if($query->has('firstName'))
    {
      $params[] = (new LikePredicate())->setField('firstName')->setExpression(
        ContainsExpression::create($query->get('firstName'))
      );
    }

    if($query->has('lastName'))
    {
      $params[] = (new LikePredicate())->setField('lastName')->setExpression(
        ContainsExpression::create($query->get('lastName'))
      );
    }

    if($query->has('username'))
    {
      $params[] = (new LikePredicate())->setField('username')->setExpression(
        ContainsExpression::create($query->get('username'))
      );
    }

    if($query->has('darkMode'))
    {
      $darkMode = $query->get('darkMode') === 'true';
      $params[] = (new EqualPredicate())->setField('darkMode')->setExpression(
        ValueExpression::create($darkMode)
      );
    }

    if($query->has('asc'))
    {
      $fields = explode(',', $query->get('asc'));
      foreach($fields as $field)
      {
        $orderBy[$field] = 'asc';
      }
    }

    if($query->has('desc'))
    {
      $fields = explode(',', $query->get('desc'));
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
   * @param $e
   *
   * @return array
   */
  protected function _getErrors($e)
  {
    $errors = [];

    foreach($e as $name => $validationErrors)
    {
      foreach($validationErrors as $key => $error)
      {
        $errors[$name][$key] = $error->getMessage();
      }
    }

    return $errors;
  }
}
