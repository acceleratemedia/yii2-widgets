<?php
namespace bvb\choices;

use yii\helpers\ArrayHelper;
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
     * Options that can be passed in if this plugin is desired to be run by
     * setting choices from a remote data source using AJAX. To set this up
     * to use ajax, only the 'url' key is required. Configuration is as such:
     * `ajax`:
     *     A url that will be requested to set the choices. This automatically 
     *     configures a number of options and registers javascript to provide for
     *     an optimal AJAX experience. 
     *     If this value is provided, the following values will be set IF they have
     *     not already been:
     *     ```php
     *     'jsOptions' => [
     *         'noChoicesText' => 'No items matching search',
     *         'placeholder' => true,
     *         'searchChoices' => false,
     *         'searchPlaceholderValue' => 'Type to search',
     *         'classNames' => [
     *             'containerOuter' => 'choices choices-'.$this->getInstanceVarName()
     *         ]
     *     ],
     *     'options' => [
     *         'prompt' => 'Search...'
     *     ]
     *     ```
     *     This will also register javascript that uses fetch() to get the results,
     * `searchListenerJs`
     *     The javascript that is run to make an AJAX request. @see getSearchListenerJs()
     *     for the default implementation. Set to false to not run, and implement 
     *     your own solution
     * `delay`
     *     The number of milliseconds to delay before making the ajax request. Prevents
     *     too many requests and provides a better UI.
     * `minChars`
     *     The minimum number of characters in the search input before making the
     *     ajax call
     * `choiceMadeJs`
     *     The javascript to run when a choice has been made. @see getChoiceMadeJs()
     *     for the default implementation. Set to false to not run, and implement 
     *     your own solution
     * `dropdownOpenJs`
     *     The javascript to run when the dropdown is opened. @see getShowDropdownFocusJs()
     *     for the default implementation. Set to false to not run, and implement 
     *     your own solution
     * 
     * @var array
     */
    public $ajaxOptions = [];

    /**
     * Default options for $ajaxOptions
     * @see $ajaxOptions
     * @var array
     */
    static $defaultAjaxOptions = [
        'delay' => 500,
        'minChars' => 3
    ];

    /**
     * Set default options if $ajaxOptions['url'] has been provided and no values for
     * the needed options
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        if(!empty($this->ajaxOptions['url'])){
            $this->initializeForAjax();
        }
    }

    /**
     * Register the javascript and run the widget
     * {@inheritdoc}
     */
    public function run()
    {
        // --- If we are not applying any 'itemSelectText' then write some CSS to
        // --- make the dropdown item take the full width
        if(!isset($this->jsOptions['noResultsText'])){
            $this->jsOptions['noResultsText'] = 'No results...';
        }
            $css = <<<CSS
.choices__heading {
    border-bottom: 1px solid #CCC;
    color: #333;
    font-style: italic;
}
CSS;
        if(!isset($this->jsOptions['itemSelectText'])){
            $this->jsOptions['itemSelectText'] = '';
            if(!isset($this->jsOptions['classNames']['list'])){
                $this->jsOptions['classNames']['list'] = $this->options['id'];
            }
            $css .= <<<CSS
.{$this->jsOptions['classNames']['list']} .choices__item--selectable{padding-right:0;}
CSS;
        }

        $this->getView()->registerCss($css);

        // --- Register default javascript
    	$this->registerDefaultJavascript();

        // --- render the dropdown
        if($this->hasModel()){
    	   return Html::activeDropDownList($this->model, $this->attribute, $this->data, $this->options);
        }
        return Html::dropdownList($this->name, $this->value, $this->data, $this->options);
    }

    /**
     * Registers the assets and javascript needed to change the select element
     * into a Choices.js javascript widget.
     * @return void
     */
    protected function registerDefaultJavascript()
    {
    	ChoicesJsAsset::register($this->getView());
    	$elementId = $this->options['id'];
    	$js = <<<JAVASCRIPT
const {$this->getInstanceVarName()} = new Choices('#{$elementId}', {$this->getOptionsVarName()})
JAVASCRIPT;
		$this->getView()->registerJs($js, \yii\web\View::POS_END);
		$this->getView()->registerJsVar($this->getOptionsVarName(), (object)$this->jsOptions);
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
     * Set some default values that create a desired UI when populating choices
     * from a remote AJAX source
     * @return void
     */
    public function initializeForAjax()
    {
        $this->ajaxOptions = ArrayHelper::merge(self::$defaultAjaxOptions, $this->ajaxOptions);
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
            // --- Turn off regular searching since we are doing it via ajax
            $this->jsOptions['searchChoices'] = false;
        }
        if(!isset($this->jsOptions['classNames']['containerOuter'])){
            // --- Setting a custom container so we can apply custom css
            $this->jsOptions['classNames']['containerOuter'] = 'choices choices-'.$this->getInstanceVarName();
        }
        // --- Hide the loading states in the area for selecteed choices
        $this->getView()->registerCss('.choices-'.$this->getInstanceVarName().' .choices__inner .choices__item~.choices__placeholder{display:none}');

        $this->getView()->registerJs($this->getSearchListenerJs(), \yii\web\View::POS_END);
        $this->getView()->registerJs($this->getChoiceMadeJs(), \yii\web\View::POS_END);
        $this->getView()->registerJs($this->getDropdownOpenJs(), \yii\web\View::POS_END);
    }

    /**
     * Gets the javascript used to fetch choices from the $ajaxOptions['url']
     * Expects the endpoint to return data in an array format with properties
     * `label` and `id` defined to be used as the label and value.
     * UI ajax search improvements like
     * @return string
     */
    public function getSearchListenerJs()
    {
        if(!isset($this->ajaxOptions['searchListenerJs']) || empty($this->ajaxOptions['searchListenerJs'])){
            $this->ajaxOptions['searchListenerJs'] = <<<JAVASCRIPT
let delayCallId = false;
document.getElementById("{$this->options['id']}").addEventListener("search", function(e){
    if(e.detail.value.length >= {$this->ajaxOptions['minChars']}){
        clearTimeout(delayCallId);
        let self = this;
        delayCallId = setTimeout(function(){
            {$this->getInstanceVarName()}.setChoices(() => {
                return fetch("{$this->ajaxOptions['url']}?term="+e.detail.value, {
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
        }, {$this->ajaxOptions['delay']});
    }
})
JAVASCRIPT;
        }
        return $this->ajaxOptions['searchListenerJs'];
    }

    /**

     * Gets the javascript that will be used to clear the choices after a choice
     * is made. By default, it will set the choices back to the default prompt. 
     * If a change is detected and there are no options, it will add the default
     * prompt back in and set it as the selected choice.
     * @return string
     */
    public function getChoiceMadeJs()
    {
        if(!isset($this->ajaxOptions['choiceMadeJs']) || empty($this->ajaxOptions['choiceMadeJs'])){
            // --- Set the placeholder when we clear choices back to the prompt or empty
            $promptLabel = !empty($this->options['prompt']) ? $this->options['prompt'] : '';
            $this->ajaxOptions['choiceMadeJs'] = <<<JAVASCRIPT
document.getElementById("{$this->options['id']}").addEventListener("choice", function(e){
    // --- Triggered when a user selects a choice
    {$this->getInstanceVarName()}.clearChoices(); // --- Clear choices (from search result)

    // --- Remove the 'no choices' element after a selection b/c it's bad UI to
    // --- show after a successful search and choice. This is one of the most 
    // --- hacky parts of all of this and depends on the user no re-opening the 
    // --- search and seeing that element but for now it accomplishes the desire
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
        return $this->ajaxOptions['choiceMadeJs'];
    }


    /**
     * Gets the javascript that will be used to focus on the search input after
     * the dropdown has been shown
     * @return string
     */
    public function getDropdownOpenJs()
    {
        if(!isset($this->ajaxOptions['dropdownOpenJs']) || empty($this->ajaxOptions['dropdownOpenJs'])){
            $this->ajaxOptions['dropdownOpenJs'] = <<<JAVASCRIPT
document.getElementById("{$this->options['id']}").addEventListener("showDropdown", function(e){
    this.parentNode.parentNode.querySelector(".choices__input.choices__input--cloned").focus();
})
JAVASCRIPT;
        }
        return $this->ajaxOptions['dropdownOpenJs'];
    }
}
?>