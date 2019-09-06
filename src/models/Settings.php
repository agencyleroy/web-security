<?php

namespace agencyleroy\websecurity\models;

use craft\base\Model;
use craft\helpers\StringHelper;
use agencyleroy\websecurity\Plugin;

/**
 *
 */
class Settings extends Model
{

    /**
     *
     */
    public $httpStrictTransportSecurity;

    /**
     *
     */
    public $httpStrictTransportSecurityMaxAge = 15768000;

    /**
     *
     */
    public $contentSecurityPolicy;

    /**
     *
     */
    public $contentSecurityPolicyValue = '';

    /**
     *
     */
    public $rows;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();

        $rules[] = [['httpStrictTransportSecurityMaxAge'], 'number', 'min' => 15768000];

        return $rules;
    }

    /**
     *
     */
    public function getCSPValue()
    {
        $nonce = Plugin::getInstance()->websecurity->nonce;
        return str_replace('{{ nonce }}', $nonce, implode($this->_CSPlines()));
    }

    /**
     *
     */
    public function getRows()
    {
        return count($this->_CSPlines());
    }

    /**
     *
     */
    private function _CSPlines()
    {
        return StringHelper::lines($this->contentSecurityPolicyValue);
    }
}
