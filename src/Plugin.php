<?php

namespace agencyleroy\websecurity;

use Craft;

use yii\base\Event;
use craft\web\View;
use craft\web\Response;
use craft\web\Application;
use craft\helpers\Html;
use craft\helpers\StringHelper;

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
            $headers = $response->getHeaders();

            if ($settings->httpStrictTransportSecurity) {
                $headers->set('Strict-Transport-Security', "max-age={$settings->httpStrictTransportSecurity}");
            }

            if ($settings->contentSecurityPolicy) {
                $headers->set('Content-Security-Policy', $settings->CSPValue);
            }
        });
    }

    /**
     * Hijack inline styles and scripts and outout them with a nonce attribute.
     */
    private function _hijackInlines()
    {
        Event::on(View::class, View::EVENT_END_PAGE, function($event) {

            // Set inline scripts as js files instead so that we can add a nonce attribute.
            foreach ($event->sender->js as $position => $scripts) {
                foreach ($scripts as $key => $script) {
                    $event->sender->jsFiles[$position][$key] = Html::script($script, ['nonce' => $this->websecurity->nonce]);
                }
            }
            // Clear inline scripts.
            $event->sender->js = [];

            // Add nonce attribute to inline styles.
            foreach ($event->sender->css as $key => $style) {
                $event->sender->css[$key] = Html::style($style, ['nonce' => $this->websecurity->nonce]);
            }
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
