<?php

namespace agencyleroy\websecurity\models;

use Craft;
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
        $baseUrl = Craft::$app->sites->currentSite->baseUrl;

        $contentSecurityPolicyValue = implode($this->_CSPlines());
        $contentSecurityPolicyValue = preg_replace('/{{\s?nonce\s?}}/', $nonce, $contentSecurityPolicyValue);
        $contentSecurityPolicyValue = preg_replace('/{{\s?baseUrl\s?}}/', $baseUrl, $contentSecurityPolicyValue);

        return $contentSecurityPolicyValue;
    }

    /**
     *
     */
    public function getRows()
    {
        $rows = count($this->_CSPlines());
        return $rows >= 3 ? $rows : 3;
    }

    /**
     *
     */
    private function _CSPlines()
    {
        return StringHelper::lines($this->contentSecurityPolicyValue);
    }
}
