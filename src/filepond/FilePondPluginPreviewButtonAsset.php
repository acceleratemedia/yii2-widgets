<?php
namespace bvb\filepond;

/**
 * Asset for filepond to render a button that will trigger an event
 * when clicking on it
 */
class FilePondPluginPreviewButtonAsset extends \yii\web\AssetBundle
{
    /**
     * @var string Identifier for this plugin from the js library. Also used to determine
     * dependences in FilepondAsset
     */

    const PLUGIN_ID = 'FilePondPluginSelectButton';
    /**
     * {@inheritdoc}
     */
    public $sourcePath = __DIR__.'/js';

    /**
     * @inheritdoc}
     */
    public $js = [
        'filepond-plugin-select-button.js'
    ];
}