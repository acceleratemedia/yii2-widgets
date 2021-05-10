<?php
namespace bvb\choices;

/**
 * Asset for choices.js
 * https://github.com/jshjohnson/Choices
 */
class ChoicesJsAsset extends \yii\web\AssetBundle
{
    /**
     * {@inheritdoc}
     */
    public $sourcePath = '@vendor/npm-asset/choices.js/public/assets';

    /**
     * Load the development or production version based on the environment
     * {@inheritdoc}
     */
    public function init()
    {
        $this->js[] = (YII_ENV_PROD ? 'scripts/choices.min.js' : 'scripts/choices.js');
        $this->css[] = (YII_ENV_PROD ? 'styles/choices.min.css' : 'styles/choices.css');
    }
}