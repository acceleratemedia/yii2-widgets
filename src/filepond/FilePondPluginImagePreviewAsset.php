<?php
namespace bvb\filepond;

/**
 * Asset for filepond image preview plugin
 * https://pqina.nl/filepond/docs/patterns/installation/
 */
class FilePondPluginImagePreviewAsset extends \yii\web\AssetBundle
{
    /**
     * @var string Identifier for this plugin from the js library. Also used to determine
     * dependences in FilepondAsset
     */
    const PLUGIN_ID = 'FilePondPluginImagePreview';

    /**
     * {@inheritdoc}
     */
    public $sourcePath = '@vendor/npm-asset/filepond-plugin-image-preview/dist';

    /**
     * Load the development or production version based on the environment
     * {@inheritdoc}
     */
    public function init()
    {
        $this->css[] = (YII_ENV_PROD ? 'filepond-plugin-image-preview.min.css' : 'filepond-plugin-image-preview.css');
        $this->js[] = (YII_ENV_PROD ? 'filepond-plugin-image-preview.min.js' : 'filepond-plugin-image-preview.js');
    }
}