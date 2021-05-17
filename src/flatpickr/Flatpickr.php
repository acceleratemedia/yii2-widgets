<?php
namespace bvb\flatpickr;

use yii\helpers\Inflector;

/**
 * Flatpickr is an InputWidget for Flatpickr javascript calendar widget
 * https://flatpickr.js.org/getting-started/
 */
class FlatPickr extends \yii\widgets\InputWidget
{
    /**
     * @var array Array of options that will be made into a json object and used
     * to initialize the flatpickr object
     */
    public $jsOptions = [];

    /**
     * Register the javascript and run the widget
     * {@inheritdoc}
     */
    public function run()
    {
        // --- Register default javascript
    	$this->registerDefaultJavascript();

        // --- render the dropdown
    	return $this->renderInputHtml('text');
    }

    /**
     * Registers the assets and javascript needed to change the select element
     * into a Choices.js javascript widget.
     * @return void
     */
    protected function registerDefaultJavascript()
    {
    	FlatpickrAsset::register($this->getView());
        $js = <<<JAVASCRIPT
flatpickr('#{$this->options['id']}', {$this->getOptionsVarName()});
JAVASCRIPT;
        $this->getView()->registerJs($js, \yii\web\View::POS_END);
        $this->getView()->registerJsVar($this->getOptionsVarName(), (object)$this->jsOptions);
    }


    /**
     * @return string The name of the variable that holds the options that will
     * be used to intialize the Choices instance
     */
    private $_optionsVarName;
    public function getOptionsVarName()
    {
        if(empty($this->_optionsVarName)){
            $this->_optionsVarName = 'flatpickr_'.Inflector::variablize($this->options['id']).'Options';
        }
        return $this->_optionsVarName;
    }
}
