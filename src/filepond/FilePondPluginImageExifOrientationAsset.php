<?php
namespace bvb\filepond;

/**
 * Asset for filepond image exif orneitationp plugin
 * https://pqina.nl/filepond/docs/patterns/installation/
 */
class FilePondPluginImageExifOrientationAsset extends \yii\web\AssetBundle
{
    /**
     * @var string Identifier for this plugin from the js library. Also used to determine
     * dependences in FilepondAsset
     */
    const PLUGIN_ID = 'FilePondPluginImageExifOrientation';

    /**
     * {@inheritdoc}
     */
    public $sourcePath = '@vendor/npm-asset/filepond-plugin-image-exif-orientation/dist';

    /**
     * Load the development or production version based on the environment
     * {@inheritdoc}
     */
    public function init()
    {
        $this->js[] = (YII_ENV_PROD ? 'filepond-plugin-image-exif-orientation.min.js' : 'filepond-plugin-image-exif-orientation.js');
    }
}