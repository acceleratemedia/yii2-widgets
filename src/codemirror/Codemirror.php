<?php
namespace bvb\codemirror;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;

/**
 * Codemirror implements an input widget that uses Codemirror javascript library
 */
class Codemirror extends \yii\widgets\InputWidget
{
    /**
     * @var array Array of options that will be made into a json object and used
     * to initialize the Codemirror object
     */
    public $jsOptions = [];

    /**
     * @var array
     */
    static $defaultJsOptions = [
        'lineNumbers' => true,
        'matchBrackets' => true,
        'mode' => 'application/x-httpd-php',
        'indentUnit' => 4,
        'indentWithTabs' => true
    ];

    /**
     * @var array Relative paths to additional js files to register from the
     * CodemirrorAsset basepath
     */
    public $jsFiles = [];

    /**
     * @var array Default set of files to run in PHP editor mode
     */
    static $defaultJsFiles = [
        'addon/edit/matchbrackets.js',
        'mode/clike/clike.js',
        'mode/css/css.js',
        'mode/htmlmixed/htmlmixed.js',
        'mode/javascript/javascript.js',
        'mode/xml/xml.js',
        'mode/php/php.js',
    ];

    /**
     * Render a container around the parent implementation
     * {@inheritdoc}
     */
    public function run()
    {
        $this->registerDefaultJavascript();
        if ($this->hasModel()) {
            return Html::activeTextarea($this->model, $this->attribute, $this->options);
        }
        return Html::activeTextarea($this->name, $this->value, $this->options);
    }

    /**
     * Registers the assets and javascript needed to change the textarea element
     * into a Codemirror editor
     * @return void
     */
    protected function registerDefaultJavascript()
    {
        $codemirrorAsset = new CodemirrorAsset;
        $codemirrorAsset->register($this->getView());
        $elementId = $this->options['id'];
        $js = <<<JAVASCRIPT
let {$this->getInstanceVarName()} = CodeMirror.fromTextArea(document.getElementById('{$this->options['id']}'), {$this->getOptionsVarName()});
JAVASCRIPT;
        $this->getView()->registerJs($js, \yii\web\View::POS_END);
        $this->getView()->registerJsVar($this->getOptionsVarName(), (object)ArrayHelper::merge(self::$defaultJsOptions, $this->jsOptions));
        $jsFiles = !empty($this->jsFiles) ? $this->jsFiles : self::$defaultJsFiles;
        list($basePath, $baseUrl) = $this->getView()->getAssetManager()->publish($codemirrorAsset->sourcePath, $codemirrorAsset->publishOptions);
        foreach($jsFiles as $jsFile){
            $this->getView()->registerJsFile($baseUrl.'/'.$jsFile, ['depends' => CodemirrorAsset::class]);
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
            $this->_optionsVarName = 'codemirror_'.Inflector::variablize($this->options['id']).'Options';
        }
        return $this->_optionsVarName;
    }
}
?>