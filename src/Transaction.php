<?php

namespace Jimizz\Gateway;

use Exception;

class Transaction
{
  private Payload $_payload;
  private string $_signature;

  public function __construct($fields)
  {
    $this->_payload = new Payload($fields);
  }

  public function sign(string $private_key)
  {
    $this->_signature = $this->_payload->sign($private_key);
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
    <form action="<?= Gateway::API_BASE ?>/" method="post" id="<?= $form_id; ?>">
      <?php foreach ($this->_payload->getRawFields() as $key => $value): ?>
        <input type="hidden" name="<?= htmlspecialchars($key); ?>" value="<?= htmlspecialchars($value); ?>">
      <?php endforeach; ?>
      <input type="hidden" name="signature" value="<?= htmlspecialchars($this->_signature); ?>">
      <button type="submit">Send</button>
    </form>
    <?php
  }
}