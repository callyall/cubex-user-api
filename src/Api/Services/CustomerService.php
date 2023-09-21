<?php

namespace UserApi\Api\Services;

use Backend\Api\Customer\CustomerResponse;
use Backend\Api\Customer\IDRequest;
use Nikolaybrankov\BackendPhpClient\BackendProvider;
use Grpc;
use UserApi\Api\Entities\CustomerEntity;

class CustomerService implements ServiceInterface
{
  /** @var BackendProvider */
  protected $provider;

  public function __construct(BackendProvider $provider)
  {
    $this->provider = $provider;
  }

  public function getById(int $id): CustomerEntity
  {
    [$response, $status] = $this
      ->provider
      ->getCustomer()
      ->GetById(
        (new IDRequest())
          ->setId($id)
      )
      ->wait();

    if ($status->code !== Grpc\STATUS_OK)
    {
      throw new \Exception("ERROR: " . $status->code . ", " . $status->details);
    }

    return $this->responseToEntity($response);
  }

  protected function responseToEntity(CustomerResponse $response): CustomerEntity
  {
    return (new CustomerEntity())
      ->setId($response->getId())
      ->setName($response->getName());
  }
}