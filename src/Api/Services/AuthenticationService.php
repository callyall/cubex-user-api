<?php

namespace UserApi\Api\Services;

use Firebase\JWT\JWT;
use Packaged\Config\ConfigProviderInterface;
use UserApi\Api\Forms\LoginForm;

class AuthenticationService implements ServiceInterface
{
  /** @var ConfigProviderInterface */
  private $_config;
  /** @var LoginForm */
  private $_form;

  public function __construct(ConfigProviderInterface $config, LoginForm $form)
  {
    $this->_config = $config;
    $this->_form = $form;
  }

  /**
   * @return array
   */
  public function getValidationErrors(): array
  {
    $errors = [];

    foreach($this->_form->validate() as $name => $validationErrors)
    {
      foreach($validationErrors as $key => $error)
      {
        $errors[$name][$key] = $error->getMessage();
      }
    }

    return $errors;
  }

  /**
   * @return string|null
   * @throws \Exception
   */
  public function authenticate(): ?string
  {
    $jwtConfig = $this->_config->getSection('jwt')->getItems();
    $username = ($this->_form->getDataHandlers()['username'])->getValue();
    $password = ($this->_form->getDataHandlers()['password'])->getValue();

    if($username !== $jwtConfig['username'] || $password !== $jwtConfig['password'])
    {
      return null;
    }

    return JWT::encode(
      [
        'iss' => $jwtConfig['host'],
        'aud' => $jwtConfig['host'],
        'iat' => time(),
        'exp' => time() + intval($jwtConfig['exp']),
      ],
      $jwtConfig['key']
    );
  }
}