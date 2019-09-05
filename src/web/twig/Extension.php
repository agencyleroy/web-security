<?php

namespace agencyleroy\websecurity\web\twig;

use Twig_Extension;
use Twig_Function;
use agencyleroy\websecurity\Plugin;

/**
 *
 */
class Extension extends Twig_Extension
{

    /**
    * @inheritdoc
    */
    public function getFunctions()
    {
        return [
            new Twig_Function('nonce', [$this, 'nonce']),
        ];
    }

    /**
     *
     */
    public function nonce()
    {
        return Plugin::getInstance()->websecurity->nonce;
    }
}
