<?php
namespace bvb\filepond;

/**
 * Asset for filepond the javascript calendar plugin
 * https://pqina.nl/filepond/docs/patterns/installation/
 */
class FilePondAsset extends \yii\web\AssetBundle
{
    /**
     * {@inheritdoc}
     */
    public $sourcePath = '@vendor/npm-asset/filepond/dist';

    /**
     * @var array Plugins to register, which will set additional dependencies
     */
    public $plugins = [];

    /**
     * Load the development or production version based on the environment
     * {@inheritdoc}
     */
    public function init()
    {
        $this->js[] = (YII_ENV_PROD ? 'filepond.min.js' : 'filepond.js');
        $this->css[] = (YII_ENV_PROD ? 'filepond.min.css' : 'filepond.css');
        parent::init();
    }
}