<?php

namespace bvb\yiiwidget\file;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * ActiveField displays the appropriate form input based on the `widget_config`
 * property of an [[bvb\filldoc\common\models\FdInput]]. The ideal case is that
 * this class is used to render [[bvb\filldoc\common\models\FdInputUser]] models
 * and it gets the widget config from the related FdInput model
 */
class CustomFileInput extends \yii\widgets\InputWidget
{
    /**
     * Template to be used on an ActiveField instance
     * @var string
     */
    public $fieldTemplate;

    /**
     * Whether to render using Bootstrap 5 styles
     * @var boolean
     */
    public $bs5 = false;

    /**
     * Renders a file input with bootstrap 4 custom styles
     * @return string
     */
    public function run(){
        if(!$this->bs5){
            $this->registerCustomFileInputJavascript();
            $this->registerCustomFileInputStyles();
            $this->field->labelOptions['data-content'] = 'Browse';
        }
        $this->field->labelOptions['class'] = 'custom-file-label';
        $this->field->options['class']['customFileInput'] = 'custom-file restore-normal-hint';
        $this->field->template = $this->getFieldTemplate();

        if(empty($this->field->options['style'])){
            $this->field->options['style'] = 'margin-bottom:1rem;';
        }

        // --- {@see \yii\widgets\ActiveField::fileInput()}
        if (!isset($this->field->form->options['enctype'])) {
            $this->field->form->options['enctype'] = 'multipart/form-data';
        }
        Html::addCssClass($this->options, 'custom-file-input');
        return Html::activeFileInput($this->model, $this->attribute, $this->options);
    }

    /**
     * Register javascript to update the label with the name of the uploaded file
     * @return void
     */
    protected function registerCustomFileInputJavascript()
    {
        $inputId = Html::getInputId($this->model, $this->attribute);
        $readyJs = <<<JAVASCRIPT
document.getElementById("#{$inputId}", function(e){
    $(".custom-file-label").attr("data-content", e.currentTarget.files[0].name);
});
JAVASCRIPT;
        Yii::$app->view->registerJs($readyJs);
    }

    /**
     * Register javascript to update the label with the name of the uploaded file
     * @return null
     */
    protected function registerCustomFileInputStyles()
    {
        $css = <<<CSS
.custom-file-label::after{
    content: attr(data-content) !important;
}
.restore-normal-hint .kv-hint-block{
    font-size: 0.75rem;
    margin-top: 0.375rem;
    color: #999;
}
.restore-normal-hint .fa-question-circle{
    display:none;
}
.custom-file-input:disabled{
    opacity:0 !important;
}
CSS;
        Yii::$app->view->registerCss($css, [], 'custom-file-css');
    }

    /**
     * Get the field template with default values for BS5 or otherwise
     * @return string
     */
    public function getFieldTemplate()
    {
        if(empty($this->fieldTemplate)){
            if($this->bs5){
                $this->fieldTemplate = "{label}\n{hint}\n{input}\n{error}";
            } else {
                $this->fieldTemplate = "{input}\n{label}\n{error}\n{hint}";
            }
        }
        return $this->fieldTemplate;
    }
}