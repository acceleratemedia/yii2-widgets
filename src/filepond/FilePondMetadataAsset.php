<?php
namespace bvb\filepond;

/**
 * Asset for filepond metadata plugin
 * https://pqina.nl/filepond/docs/patterns/installation/
 */
class FilePondMetadataAsset extends \yii\web\AssetBundle
{
    /**
     * @var string Identifier for this plugin from the js library. Also used to determine
     * dependences in FilepondAsset
     */
    const PLUGIN_ID = 'FilePondPluginFileMetadata';

    /**
     * {@inheritdoc}
     */
    public $sourcePath = '@vendor/npm-asset/filepond-plugin-file-metadata/dist';

    /**
     * Load the development or production version based on the environment
     * {@inheritdoc}
     */
    public function init()
    {
        $this->js[] = (YII_ENV_PROD ? 'filepond-plugin-file-metadata.min.js' : 'filepond-plugin-file-metadata.js');
    }
}