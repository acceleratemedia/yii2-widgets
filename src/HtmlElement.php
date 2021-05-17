<?php 
namespace bvb\yiiwidget;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * HtmlElement sets up a framework to create widgets for rendering a single
 * HTML element. Classes that extend this can be configured with options
 * to render a default element via [[$defaultElementOptions]] with the flexibility
 * to override and customize via [[$elementOptions]]. 
 * An example use case would be an website that re-uses a modal with a some events
 * tied to it. In the design, this modal may be triggered by various elements like
 * a link, image, button, etc. A widget extending from this class could impelmemt
 * the javascript needed to open the modal and queue an event to render it. Then,
 * the widget could be reused on in an application to render any element while 
 * accomplishing the same purpose of opening the modal and triggering events
 */
class HtmlElement extends \yii\base\Widget
{
    /**
     * Options that are passed as the third argument to \yii\helpers\Html::tag()
     * There are two special keys to be configured:
     *     `tag` is removed and passed into to Html::tag() as the first argument
     *     `content` is removed and passed into to Html::tag() as the second argument
     * @var array
     */
    public $options = [];

    /**
     * Default configuration options for the rendering of the element. This is
     * a static property so that they can be accessed at a class level which may
     * be useful for descendents of this class to traverse through the parent
     * classes and aggregate and inherit their $defaultOptoins
     * @see $options
     */
    static $defaultOptions = [];

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $options = ArrayHelper::merge(
            static::$defaultOptions,
            $this->options
        );

        // --- Set up the tag as necessary
        $tag = ArrayHelper::remove($options, 'tag');
        $content = ArrayHelper::remove($options, 'content');

        return Html::tag(
            $tag,
            $content,
            $options
        );
    }
}