<?php

namespace Jimizz\Gateway;

use Exception;

class Gateway
{
  public const API_BASE = 'https://gateway.jimizz.com/api';

  private string $_merchantId;

  public function __construct(
    string $merchantId
  )
  {
    $this->_merchantId = $merchantId;
  }

  /**
   * @throws Exception
   */
  public function transaction(
    string $transaction_type,
    array  $fields
  ): Transaction
  {
    return new Transaction([
      'merchantId' => $this->_merchantId,
      'type' => $transaction_type,
      ...$fields
    ]);
  }
}