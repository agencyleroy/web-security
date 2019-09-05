<?php

namespace agencyleroy\websecurity\services;

use yii\base\Component;

/**
 *
 */
class WebSecurity extends Component
{

    private $_nonce;

    /**
     * Generates a random nonce parameter.
     *
     * @return string
     */
    public function getNonce()
    {
        if (!$this->_nonce) {
          $this->_nonce = base64_encode(random_bytes(20));
        }

        return $this->_nonce;
    }
}
