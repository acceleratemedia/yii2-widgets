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
     * A url that will be requested to set the choices. This automatically 
     * configures a number of options and registers javascript to provide for
     * an optimal AJAX experience. 
     * If this value is provided, the following values will be set IF they have
     * not already been:
     * ```php
     * 'jsOptions' => [
     *     'noChoicesText' => 'Type to search',
     *     'placeholder' => true,
     *     'searchChoices' => false
     * ],
     * 'options' => [
     *     'prompt' => 'Type to search'
     * ]
     * ```
     * This will also register javascript that uses fetch() to get the results,
     * clears the choices after a selection is made, and focuses on the search
     * input after opening the dropdown
     * @see $searchListenerJs
     * @see getSearchListenerJs()
     * @see $clearChoicesJs
     * @see getClearChoicesJs()
     * @see $showDropdownFocusJs
     * @see getShowDropdownFocusJs()
     * @var string
     */
    public $ajaxUrl;

    /**
     * @see getSearchListenerJs()
     * @var string Javascript that will be registered if $ajaxUrl has a value.
     * Intended to be used to fetch choices from the $ajaxUrl by listening to
     * the 'search' event
     */
    public $searchListenerJs;

    /**
     * @see getClearChoicesJs()
     * @var string Javascript that will be registered if $ajaxUrl has a value.
     * Intended to clear choices after a selection has been made
     */
    public $clearChoicesJs;

    /**
     * @see getShowDropdownFocusJs()
     * @var string Javascript that will be registered if $ajaxUrl has a value.
     * Intended to focus on the search input when the dropdown opens
     */
    public $showDropdownFocusJs;

    /**
     * Set default options if $ajaxUrl has been provided and no values for
     * the needed options
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        if(!empty($this->ajaxUrl)){
            if(!isset($this->jsOptions['noChoicesText'])){
                $this->jsOptions['noChoicesText'] = 'No items matching search';
            }
            if(!isset($this->options['prompt'])){
                $this->options['prompt'] = 'Search...';
            }
            if(!isset($this->jsOptions['searchPlaceholderValue'])){
                $this->jsOptions['searchPlaceholderValue'] = 'Type to search';
            }
            if(!isset($this->jsOptions['searchChoices'])){
                $this->jsOptions['searchChoices'] = false;
            }
            if(!isset($this->jsOptions['classNames']['containerOuter'])){
                $this->jsOptions['classNames']['containerOuter'] = 'choices choices-'.$this->getInstanceVarName();
            }
            // --- Hide the loading states
            $this->getView()->registerCss('.choices-'.$this->getInstanceVarName().' .choices__inner .choices__item~.choices__placeholder{display:none}');
        }
    }

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
     * into a Choices.js javascript widget. If an $ajaxUrl was supplied, it will
     * also register the default javascript
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

        if(!empty($this->ajaxUrl)){
            $this->getView()->registerJs($this->getSearchListenerJs(), \yii\web\View::POS_END);
            $this->getView()->registerJs($this->getClearChoicesJs(), \yii\web\View::POS_END);
            $this->getView()->registerJs($this->getShowDropdownFocusJs(), \yii\web\View::POS_END);
        }
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

    /**
     * Gets the javascript that will be used to fetch choices from the $ajaxUrl.
     * Expects the endpoint to return data in an array format with properties
     * `label` and `id` defined to be used as the label and value.
     * @return string
     */
    public function getSearchListenerJs()
    {
        if($this->searchListenerJs === null){
            $this->searchListenerJs = <<<JAVASCRIPT
let delayCallId = false;
document.getElementById("{$this->options['id']}").addEventListener("search", function(e){
    if(e.detail.value.length >= 3){
        clearTimeout(delayCallId);
        let self = this;
        delayCallId = setTimeout(function(){
            {$this->getInstanceVarName()}.setChoices(() => {
                return fetch("{$this->ajaxUrl}?term="+e.detail.value, {
                    method: "GET"
                }).then(function(response){ return response.json() })
                .then(function(json){
                    return json.map(function(result) {
                      return { label: result.label, value: result.id };
                    });
                }).catch((err) => {
                    alert(err);
                }).finally(function(){
                    clearTimeout(delayCallId);
                });
            }, 'value', 'label', true)
            .then((test) => {
                // --- Re-focus on the input after setting choices since it loses focus
                self.parentNode.parentNode.querySelector(".choices__input.choices__input--cloned").focus();

                // --- If there is a selected item and a placeholder, remove the placeholder and keep
                // --- only the selected item
                let selectedItem = {$this->getInstanceVarName()}.itemList.getChild("div:not(.choices__placeholder)");
                if(selectedItem && {$this->getInstanceVarName()}.itemList.getChild(".choices__placeholder")){
                    {$this->getInstanceVarName()}.itemList.getChild(".choices__placeholder").remove();
                }
            });
        }, 500);
    }
})
JAVASCRIPT;
        }
        return $this->searchListenerJs;
    }

    /**

     * Gets the javascript that will be used to clear the choices after a choice
     * is made. By default, it will set the choices back to the default prompt. 
     * If a change is detected and there are no options, it will add the default
     * prompt back in and set it as the selected choice.
     * @return string
     */
    public function getClearChoicesJs()
    {
        if($this->clearChoicesJs === null){
            // --- Set the placeholder when we clear choices back to the prompt or empty
            $promptLabel = !empty($this->options['prompt']) ? $this->options['prompt'] : '';
            $this->clearChoicesJs = <<<JAVASCRIPT
document.getElementById("{$this->options['id']}").addEventListener("choice", function(e){
    // --- Triggered when a user selects a choice
    {$this->getInstanceVarName()}.clearChoices(); // --- Clear choices (from search result)

    // --- Set up for the removal of the 'no choices' element for the UI to be for a fresh search
    let self = this;
    setTimeout(function(){ 
        let noChoicesElement = self.parentNode.parentNode.querySelector('.has-no-choices')
        if(noChoicesElement){
            noChoicesElement.remove() 
        }
    }, 1000);
})
document.getElementById('{$this->options['id']}').addEventListener("change", function(e){
    // ---- This is triggered when someone clears the choice using the 'x' button
    if(!e.target.options[0]){
        // --- If we have no options left, re-apply the placeholder
        let placeholderItem = {$this->getInstanceVarName()}._getTemplate( 'placeholder', '{$promptLabel}' ); 
        {$this->getInstanceVarName()}.itemList.append(placeholderItem);

        // --- If there is a no choices element, remove it
        let noChoicesElement = this.parentNode.parentNode.querySelector('.has-no-choices');
        if(noChoicesElement){
            noChoicesElement.remove();  
        }
    }
})
JAVASCRIPT;
        }
        return $this->clearChoicesJs;
    }


    /**
     * Gets the javascript that will be used to focus on the search input after
     * the dropdown has been shown
     * @return string
     */
    public function getShowDropdownFocusJs()
    {
        if($this->showDropdownFocusJs === null){
            $this->showDropdownFocusJs = <<<JAVASCRIPT
document.getElementById("{$this->options['id']}").addEventListener("showDropdown", function(e){
    this.parentNode.parentNode.querySelector(".choices__input.choices__input--cloned").focus();
})
JAVASCRIPT;
        }
        return $this->showDropdownFocusJs;
    }
}
?>