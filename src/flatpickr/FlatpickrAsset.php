<?php
namespace bvb\flatpickr;

/**
 * Asset for Flatpickr the javascript calendar plugin
 * https://flatpickr.js.org/getting-started/
 */
class FlatpickrAsset extends \yii\web\AssetBundle
{
    /**
     * {@inheritdoc}
     */
    public $sourcePath = '@vendor/npm-asset/flatpickr/dist';

    /**
     * Load the development or production version based on the environment
     * {@inheritdoc}
     */
    public function init()
    {
        $this->js[] = (YII_ENV_PROD ? 'flatpickr.min.js' : 'flatpickr.js');
        $this->css[] = (YII_ENV_PROD ? 'flatpickr.min.css' : 'flatpickr.css');
    }
}