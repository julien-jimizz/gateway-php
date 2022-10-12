<?php

namespace Jimizz\Gateway;

use Elliptic\EC;
use Exception;

class Transaction
{
  private array $_fields;
  private string $_signature;

  public function __construct(array $fields)
  {
    $this->_fields = $fields;
  }

  public function sign(string $private_key)
  {
    $flat = $this->_flatPayload();
    $hash = hash('sha256', $flat);

    $ec = new EC('secp256k1');
    $ecPrivateKey = $ec->keyFromPrivate($private_key, 'hex');
    $signature = $ecPrivateKey->sign($hash, ['canonical' => true]);
    $this->_signature = $signature->toDER('hex');
  }

  /**
   * @throws Exception
   */
  public function render($form_id = 'jimizz-form')
  {
    if (empty($this->_signature)) {
      throw new Exception('You must sign first');
    }

    ?>
    <form action="<?= Gateway::API_BASE ?>" method="post" id="<?= $form_id; ?>">
      <?php foreach ($this->_fields as $key => $value): ?>
        <input type="hidden" name="<?= htmlspecialchars($key); ?>" value="<?= htmlspecialchars($value); ?>">
      <?php endforeach; ?>
      <input type="hidden" name="signature" value="<?= htmlspecialchars($this->_signature); ?>">
      <button type="submit">Send</button>
    </form>
    <?php
  }

  private function _flatPayload(): string
  {
    ksort($this->_fields);
    return array_reduce(
      array_keys($this->_fields),
      function ($acc, $key) {
        return $acc
          . $key
          . '+'
          . (
          is_object($this->_fields[$key])
            ? preg_replace('~\+$~', '', $this->_flatPayload($this->_fields[$key]))
            : $this->_fields[$key])
          . '+';
      }, '');
  }
}