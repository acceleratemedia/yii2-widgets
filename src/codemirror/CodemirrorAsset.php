<?php
namespace bvb\codemirror;

/**
 * Asset for Codemirror
 * https://codemirror.net/
 */
class CodemirrorAsset extends \yii\web\AssetBundle
{
    /**
     * {@inheritdoc}
     */
    public $sourcePath = '@vendor/npm-asset/codemirror';

    /**
     * {@inheritdoc}
     */
    public $js = ['lib/codemirror.js'];

    /**
     * {@inheritdoc}
     */
    public $css = ['lib/codemirror.css'];
}