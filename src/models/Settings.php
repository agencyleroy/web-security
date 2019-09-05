<?php

namespace agencyleroy\websecurity\models;

use craft\base\Model;

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
    public $contentSecurityPolicyValue;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();

        $rules[] = [['httpStrictTransportSecurityMaxAge'], 'number', 'min' => 15768000];

        return $rules;
    }
}
