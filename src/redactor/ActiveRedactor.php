<?php
namespace bvb\yiiwidget\redactor;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * Exteneds [[yii\redactor\widgets\Redactor]] to render it inside of a container
 * so that it may grow and increase in size but not beyond a limit and stay in
 * that container while scrolling
 */
class ActiveRedactor extends \yii\redactor\widgets\Redactor
{
    /**
     * Render a container around the parent implementation
     * {@inheritdoc}
     */
    public function run()
    {
        self::registerInlineCss();
        $containerId = (isset($this->options['id']) && !empty($this->options['id'])) ? $this->options['id'] : $this->getId();
        $defaultOptions = [
            'toolbarFixedTarget' => '#'.$containerId.'-redactor-container',
        ];
        $this->options = ArrayHelper::merge($defaultOptions, $this->options);
        ob_start();
        parent::run();
        $content = ob_get_contents();
        ob_end_clean();
        return '<div id="'.$containerId.'-redactor-container" class="redactor-container">' . $content . '</div>';
    }

    /**
     * Registers the inline CSS to the page for the redactor
     * This function is static because other widgets might want to register this 
     * CSS to use the style
     * @return void
     */
    static function registerInlineCss()
    {
        $css = <<<CSS
.redactor-container {
    max-height: 800px;
    overflow: auto;
}
.redactor-field-container{
    margin-bottom: 30px;
}
.redactor-box ~ .invalid-feedback{
    display:block;
}
.is-invalid ~ .redactor-editor{
    display:none;
}
CSS;

        Yii::$app->getView()->registerCss( preg_replace('/\s+/', '', $css), [], 'active-redactor-css' );
    }
}