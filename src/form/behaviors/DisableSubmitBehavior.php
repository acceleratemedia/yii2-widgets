<?php

namespace bvb\yiiwidget\form\behaviors;

use yii\base\Behavior;
use yii\base\Widget;
use yii\web\View;

/**
 * DisableSubmitBehavior is meant to be attached to ActiveForm widgets and
 * registers javascript that will enable and display submit buttons and change
 * the button text as well
 */
class DisableSubmitBehavior extends Behavior
{
    /**
     * @var string Name of a data attribute "data-{$formDataAttribute}" that will 
     * be used identify the element whose child susmit buttons will be disabled
     * or enabled
     */
    public $formDataAttribute = "disable-on-submit";

    /**
     * @var string The jQuery selector used to identify the elements to be enabled
     * and disabled
     */
    public $disableElementSelector = ":input[type='submit']";

    /**
     * {@inheritdoc}
     */
    public function events()
    {
        return [
            Widget::EVENT_INIT => 'formInit',
        ];
    }

    /**
     * Register javascript that registers functions that enables and disables submit
     * buttons and also javscript that uses those during the Active Form js events
     * @return void
     */
    public function formInit()
    {
        // --- Set a data attribute on the owner to be used as the selector for
        // --- elements to disable submit buttons withing
        $this->owner->options['data'][$this->formDataAttribute] = true;

        // --- Register javascript that contains the functions which will enable
        // --- and disable the elements that is completely generalized
        $readyJs = <<<JAVASCRIPT
$.fn.extend({
    disableSubmitButtons: function(childSelector){
        var self = $(this);
        self.find(childSelector).attr("disabled", "disabled");
        self.find(childSelector).each(function() {
            var enabledText = getElementText($(this));
            $(this).attr("data-enabled-text", enabledText);
            if( $(this).attr("data-disabled-text") ){
                updateElementText($(this), $(this).data("disabled-text"))
            }
        });
    },

    enableSubmitButtons: function(childSelector){
        var self = $(this);
        self.find(childSelector).removeAttr("disabled");
        self.find(childSelector + "[data-enabled-text]").each(function () {
            updateElementText($(this), $(this).data("enabled-text"))
        });
    }
});
JAVASCRIPT;
        $this->owner->getView()->registerJs($readyJs, View::POS_READY, 'disable-enable-submit-js');

        // --- Register javascript that may be specific to each instance of where this is
        // --- used depending on the form selector and disable element selector attributes
        // --- This uses the Yii Active Form events to actually do the disabling and enabling
        $readyJs = <<<JAVASCRIPT
$("[data-{$this->formDataAttribute}]").on("beforeValidate", function (event, messages, deferred) {
    $(this).disableSubmitButtons("{$this->disableElementSelector}");
}).on("afterValidate", function (event, messages, errorAttributes) {
    console.log("event");
    console.log(event);
    console.log("errorAttributes.length");
    console.log(errorAttributes.length);
    if (errorAttributes.length == 0) {
        return;
    }
    $(this).enableSubmitButtons("{$this->disableElementSelector}");
}).on("ajaxComplete", function () {
    $(this).enableSubmitButtons("{$this->disableElementSelector}");
});
JAVASCRIPT;
        $this->owner->getView()->registerJs($readyJs);

        // --- Register a general javascript function that will update the text of an input
        // --- element properly or the html
        $js = <<<JAVASCRIPT
function updateElementText(jQEl, text){
    if (jQEl.prop("tagName").toLowerCase() === "input") {
        jQEl.val(text);
    } else {
        jQEl.html(text);
    }   
}

function getElementText(jQEl){
    if (jQEl.prop("tagName").toLowerCase() === "input") {
        return jQEl.val();
    } else {
        return jQEl.html();
    }   
}
JAVASCRIPT;
        $this->owner->getView()->registerJs($js, View::POS_END, 'disable-element-text');
    }
}