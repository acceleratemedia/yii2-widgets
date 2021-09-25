<?php

namespace bvb\bs5;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Dropdown will render a dropdown menu with html markup for Bootstrap 5
 * it requires that all of the css and js for BS5 be loaded
 */
class Dropdown extends \yii\widgets\Menu
{
    /**
     * @var array Options to be used to render the menu link
     */
    public $menuLinkOptions = [];

    /**
     * @var array Default options to be used to render the menu link
     */
    static $defaultMenuLinkOptions = [
        'tag' => 'button',
        'content' => 'Dropdown',
        'class' => ['default' => 'btn btn-primary dropdown-toggle'],
        'href' => '#',
        'role' => 'button',
        'data-bs-toggle' => 'dropdown',
        'aria-expanded' => 'false'
    ];

    /**
     * @var array Options to be used to render the menu link
     */
    public $containerOptions = [];

    /**
     * @var array Options to be used to render the menu link
     */
    static $defaultContainerOptions = [
        'tag' => 'div',
        'class' => ['default' => 'dropdown d-inline-block']
    ];

    /**
     * {@inheritdoc}
     */
    public $options = [
        'class' => 'dropdown-menu dropdown-menu-right',
    ];

    /**
     * {@inheritdoc}
     */
    public $linkTemplate = '<a class="dropdown-item" href="{url}">{label}</a>';

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $containerOptions = ArrayHelper::merge(static::$defaultContainerOptions, $this->containerOptions);
        $containerTag = ArrayHelper::remove($containerOptions, 'tag');

        $menuLinkId = (isset($this->options['id']) ? $this->options['id'] : $this->getId()).'-menu-link';
        $menuLinkOptions = ArrayHelper::merge(
            static::$defaultMenuLinkOptions,
            [
                'id' => $menuLinkId
            ],
            $this->menuLinkOptions
        );
        $menuLinkTag = ArrayHelper::remove($menuLinkOptions, 'tag');
        $menuLinkContent = ArrayHelper::remove($menuLinkOptions, 'content');
        $menuLink = Html::Tag($menuLinkTag, $menuLinkContent, $menuLinkOptions);

        if(!isset($this->options['aria-labelledby'])){
            $this->options['aria-labelledby'] = $menuLinkId;
        }

        // --- Menu normally echos so we need to output buffer to get parent
        ob_start();
        parent::run();
        $menu = ob_get_contents();
        ob_end_clean();
        return Html::tag($containerTag, $menuLink . $menu, $containerOptions);
    }
}
