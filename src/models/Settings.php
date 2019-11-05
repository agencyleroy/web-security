<?php

namespace agencyleroy\websecurity\models;

use craft\base\Model;
use craft\helpers\UrlHelper;
use craft\helpers\StringHelper;
use agencyleroy\websecurity\Plugin;

/**
 * Settings model class.
 */
class Settings extends Model
{

    /**
     * @var bool|null Strict-Transport-Security enabled?
     */
    public $httpStrictTransportSecurity;

    /**
     * @var int|null Strict-Transport-Security max-age
     */
    public $httpStrictTransportSecurityMaxAge = 15768000;

    /**
     * @var bool|null Content-Security-Policy enabled?
     */
    public $contentSecurityPolicy;

    /**
     * @var string|null Content-Security-Policy value
     */
    public $contentSecurityPolicyValue = '';

    /**
     * @var string|null X-Frame-Options
     */
    public $xFrameOptions;

    /**
     * @var string|null X-XSS-Protection
     */
    public $xXssProtection;

    /**
     * @var string|null X-Content-Type-Options
     */
    public $xContentTypeOptions;

    /**
     *
     */
    // public $rows;

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
        $host = UrlHelper::host();

        $contentSecurityPolicyValue = implode($this->_CSPlines());
        $contentSecurityPolicyValue = preg_replace('/{{\s?nonce\s?}}/', $nonce, $contentSecurityPolicyValue);
        $contentSecurityPolicyValue = preg_replace('/{{\s?baseUrl\s?}}/', $host, $contentSecurityPolicyValue);

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
