<?php

namespace bvb\yiiwidget\slug;

use Yii;
use yii\web\View;

/**
 * Slugify widget generates a slug for one input after blurring another input.
 * Optional prefix can be put onto slug
 */
class Slugify extends \yii\base\Widget
{
    /**
     * The prefix applied to the slug if desred
     * @var string
     */
    public $prefix;

    /**
     * The ID attribute of the input that will be used to generate the slug
     * @var string
     */
    public $generatingInputId;

    /**
     * The ID attribute of the input the slug will be put into
     * @var string
     */
    public $receivingElementId = '';

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $js = <<<JAVASCRIPT
function slugify(text)
{
  return text.toString().toLowerCase()
    .replace(/\s+/g, '-')           // Replace spaces with -
    .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
    .replace(/\-\-+/g, '-')         // Replace multiple - with single -
    .replace(/^-+/, '')             // Trim - from start of text
    .replace(/-+$/, '');            // Trim - from end of text
}
JAVASCRIPT;
        $this->getView()->registerJs($js, View::POS_END, 'slugify-function');

        $js = <<<JAVASCRIPT
document.getElementById('{$this->generatingInputId}').addEventListener("blur", function(e){
    let el = document.getElementById('{$this->receivingElementId}');
    el.value = "{$this->prefix}"+slugify( this.value )
    if ("createEvent" in document) {
        var evt = document.createEvent("HTMLEvents");
        evt.initEvent("change", false, true);
        el.dispatchEvent(evt);
    } else {
        el.fireEvent("onchange");
    }
})
JAVASCRIPT;
        $this->getView()->registerJs($js, \yii\web\View::POS_END);
    }
}
