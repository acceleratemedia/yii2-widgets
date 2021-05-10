<?php
namespace bvb\choices;

use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Json;

/**
 * ChoicesJs is an InputWidget for Choices.js
 * https://joshuajohnson.co.uk/Choices/
 */
class ChoicesJs extends \yii\widgets\InputWidget
{
    /**
     * @var array $data The options for the select input. The array keys are 
     * option values, and the array values are the corresponding option labels
     */
    public $data = [];

    /**
     * @var array Array of options that will be made into a json object and used
     * to initialize the ChoicesJs object
     */
    public $jsOptions = [];

    /**
     * Register the javascript and run the widget
     * {@inheritdoc}
     */
    public function run()
    {
    	$this->registerJavascript();
    	return Html::activeDropDownList($this->model, $this->attribute, $this->data, $this->options);
    }

    /**
     * Registers the assets and javascript needed to change the select element
     * into a Choices.js javascript widget
     * @return void
     */
    protected function registerJavascript()
    {
    	ChoicesJsAsset::register($this->getView());
    	$elementId = $this->options['id'];
    	$js = <<<JAVASCRIPT
const {$this->getInstanceVarName()} = new Choices('#{$elementId}', {$this->getOptionsVarName()})
JAVASCRIPT;
		$this->getView()->registerJs($js, \yii\web\View::POS_END);
		$this->getView()->registerJsVar($this->getOptionsVarName(), $this->jsOptions);
    }

    /**
     * @return string The name of the variable that the Choices javascript object
     * instance will he held in
     */
    private $_instanceVarName;
    public function getInstanceVarName()
    {
    	if(empty($this->_instanceVarName)){
    		$this->_instanceVarName = Inflector::variablize($this->options['id']);
    	}
    	return $this->_instanceVarName;
    }


    /**
     * @return string The name of the variable that holds the options that will
     * be used to intialize the Choices instance
     */
    private $_optionsVarName;
    public function getOptionsVarName()
    {
    	if(empty($this->_optionsVarName)){
    		$this->_optionsVarName = 'choices_'.Inflector::variablize($this->options['id']).'Options';
    	}
    	return $this->_optionsVarName;
    }
}
?>