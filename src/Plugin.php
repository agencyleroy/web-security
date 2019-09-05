<?php

namespace agencyleroy\websecurity;

use Craft;

use yii\base\Event;
use craft\web\View;
use craft\web\Response;
use craft\web\Application;
use craft\helpers\Html;

use agencyleroy\websecurity\models\Settings;
use agencyleroy\websecurity\services\WebSecurity;
use agencyleroy\websecurity\web\twig\Extension;

/**
 *
 */
class Plugin extends \craft\base\Plugin
{

    /**
     * @inheritdoc
     */
    public $hasCpSettings = true;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->setComponents([
            'websecurity' => WebSecurity::class,
        ]);

        if (Craft::$app->request->isSiteRequest) {
            $this->_registerTwigExtensions();
            $this->_registerHeaders();
            $this->_hijackInlines();
        }
    }

    /**
     * Register twig extensions.
     */
    private function _registerTwigExtensions()
    {
        $webSecurityExtension = new Extension();
        Craft::$app->view->registerTwigExtension($webSecurityExtension);
    }

    /**
     *  Set response headers based on settings.
     */
    private function _registerHeaders()
    {
        Event::on(Response::class, Response::EVENT_AFTER_PREPARE, function(Event $event) {
            $response = $event->sender;

            $settings = Plugin::getInstance()->getSettings();
            $cspValue = str_replace('{{ nonce }}', $this->websecurity->nonce, $settings->contentSecurityPolicyValue);

            $headers = $response->getHeaders();

            if ($settings->httpStrictTransportSecurity) {
                $headers->set('Strict-Transport-Security', "max-age={$settings->httpStrictTransportSecurityMaxAge}");
            }

            if ($settings->contentSecurityPolicy) {
                $headers->set('Content-Security-Policy', $cspValue);
            }
        });
    }

    /**
     * Hijack inline styles and scripts and outout them with a nonce attribute.
     */
    private function _hijackInlines()
    {
        Event::on(View::class, View::EVENT_END_PAGE, function($event) {

            foreach ($event->sender->js as $scripts) {
                foreach ($scripts as $script) {
                    echo Html::script($script, ['nonce' => $this->websecurity->nonce]);
                }
            }

            foreach ($event->sender->css as $styles) {
                foreach ($styles as $style) {
                    echo Html::style($style, ['nonce' => $this->websecurity->nonce]);
                }
            }

            $event->sender->js = [];
            $event->sender->css = [];
        });
    }

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml()
    {
        return Craft::$app->getView()->renderTemplate('web-security/settings', [
            'settings' => $this->getSettings()
        ]);
    }
}
