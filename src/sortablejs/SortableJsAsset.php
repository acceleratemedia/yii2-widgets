<?php
namespace bvb\sortablejs;

/**
 * Asset for Sortable
 * https://github.com/SortableJS/Sortable
 */
class SortableJsAsset extends \yii\web\AssetBundle
{
    /**
     * {@inheritdoc}
     */
    public $sourcePath = '@vendor/npm-asset/sortablejs';

    /**
     * Load the development or production version based on the environment
     * {@inheritdoc}
     */
    public function init()
    {
        $this->js[] = (YII_ENV_PROD ? 'Sortable.min.js' : 'Sortable.js');
    }
}