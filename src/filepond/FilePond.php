<?php
namespace bvb\filepond;

use yii\helpers\Inflector;

/**
 * Filepond is an InputWidget for Filepond javascript media widget
 * https://pqina.nl/filepond/docs/patterns/installation/
 */
class FilePond extends \yii\widgets\InputWidget
{
    /**
     * @var array Array of options that will be made into a json object and used
     * to initialize the filepond object
     */
    public $jsOptions = [];

    /**
     * Array of class names of additional AssetBundles that are for
     * filepong plugins to be registered
     * @see \bvb\filepond\FilepondAsset::$plugins
     * @var array 
     */
    public $plugins = [];

    /**
     * The event to listen to where intiliazation of the javascript widget will
     * take place. Set to false to not run and write a custom initialization
     * @var string
     */
    public $initializeEvent = 'FilePond:loaded';

    /**
     * Register the javascript and run the widget
     * {@inheritdoc}
     */
    public function run()
    {
        // --- Register default javascript
    	$this->registerDefaultJavascript();

        // --- render the dropdown
    	return $this->renderInputHtml('file');
    }

    /**
     * Registers the assets and javascript needed to change the select element
     * into a Choices.js javascript widget.
     * @return void
     */
    protected function registerDefaultJavascript()
    {
        // --- Load the main library
    	FilePondAsset::register($this->getView());
        if(is_string($this->initializeEvent)){        
            $js = <<<JAVASCRIPT
let {$this->getInstanceVarName()};
document.addEventListener('{$this->initializeEvent}', e => {
    {$this->getPluginJs()}
    {$this->getInstanceVarName()} = FilePond.create(document.getElementById('{$this->options['id']}'), {$this->getOptionsVarName()});
});
JAVASCRIPT;

            $this->getView()->registerJs($js, \yii\web\View::POS_END);
        }
        $this->getView()->registerJsVar($this->getOptionsVarName(), (object)$this->jsOptions);
    }

    /**
     * Gets the javascript to register the specified plugins
     * @return string
     */
    public function getPluginJs()
    {
        // --- FilePond requires plugins load before the main library
        $pluginIds = [];
        foreach($this->plugins as $pluginClass){
            $pluginClass::register($this->getView());
            $pluginIds[] = $pluginClass::PLUGIN_ID;
        }

        return (empty($pluginIds)) ? '' : 'FilePond.registerPlugin('.implode(',', $pluginIds).')';
    }

    /**
     * @return string The name of the variable that holds the options that will
     * be used to intialize the Choices instance
     */
    private $_optionsVarName;
    public function getOptionsVarName()
    {
        if(empty($this->_optionsVarName)){
            $this->_optionsVarName = 'filepond_'.Inflector::variablize($this->options['id']).'Options';
        }
        return $this->_optionsVarName;
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
}
