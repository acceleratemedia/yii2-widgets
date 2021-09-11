<?php
namespace bvb\yiiwidget\form;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class ActiveField extends \yii\widgets\ActiveField
{
    /**
     * @var string Constant that identifies we should automatically determine
     * the template based on some conditions
     */
    const TEMPLATE_AUTO = 'auto';

    /**
     * @var string Default template from yii\widgets\ActiveField
     */
    const TEMPLATE_DEFAULT = "{label}\n{input}\n{hint}\n{error}";

    /**
     * @var string Default template to use when hints are displayed as tooltips
     */
    const TEMPLATE_TOOLTIP_HINT_DEFAULT = "{label}\n{hint}\n{input}\n{error}";

    /**
     * @var string Default template to use when hints are displayed as tooltips
     * and we are rendering a checkbox input
     */
    const TEMPLATE_TOOLTIP_HINT_CHECKBOX = "<div class=\"form-check form-switch\">{input}\n{label}\n{hint}</div>\n{error}";

    /**
     * Set the template to auto determine by default
     * {@inheritdoc}
     */
    public $template = self::TEMPLATE_AUTO;

    /**
     * Set up for Bootstrap 5
     * {@inheritdoc}
     */
    public $options = ['class' => ['bs5' => 'form-group mb-3']];

    /**
     * Set up for Bootstrap 5
     * {@inheritdoc}
     */
    public $labelOptions = ['class' => ['bs5' => 'form-label']];

    /**
     * Set up for Bootstrap 5
     * {@inheritdoc}
     */
    public $errorOptions = ['class' => 'invalid-feedback'];

    /**
     * By default, set a key 'asTooltip' to true, which will render the hint
     * as a tooltip that uses Bootstrap 5 tooltips and a font awesome icon
     * {@inheritdoc}
     */
    public $hintOptions = [
        'asTooltip' => true
    ];

    /**
     * Renders the hint as a tooltip if $hintOptions['tooltip'] is set to true
     * otherwise defaults to the parent implementation
     * {@inheritdoc}
     */
    public function hint($content, $options = [])
    {
        if(!isset($this->hintOptions['asTooltip']) || !$this->hintOptions['asTooltip']){
            if($this->template == self::TEMPLATE_AUTO){
                $this->template = self::TEMPLATE_DEFAULT;
            }
            return parent::hint($content, $options);
        }

        if($this->template == self::TEMPLATE_AUTO){
            $this->template = self::TEMPLATE_TOOLTIP_HINT_DEFAULT;
        }

        // --- mimics \yii\helpers\BaseHtml::activeHint()
        $attribute = \yii\helpers\Html::getAttributeName($this->attribute);
        $hint = isset($options['hint']) ? $options['hint'] : $this->model->getAttributeHint($attribute);
        if (!empty($hint)) {
            $icon = Html::tag('i', '', [ 'class' => 'fas fa-question-circle'] );
            $this->parts['{hint}'] = Html::tag(
                'span',
                $icon,
                [
                    'id' => $this->getInputId().'-hint',
                    'class' => ['activeField' => 'mt-1 mx-1 hint-wrapper'],
                    'data-bs-toggle' => 'tooltip',
                    'title' => $hint]
            );
            $js = <<<JAVASCRIPT
var tooltipTriggerList = [].slice.call(document.querySelectorAll('#{$this->form->options['id']} [data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
})
JAVASCRIPT;
            $this->form->getView()->registerJs($js, \yii\web\View::POS_END, 'activefield-tooltip-trigger-js-'.$this->form->options['id']);
        } else {
            $this->parts['{hint}'] = '';
        }


        return $this;
    }

    /**
     * If the tooltip template is set to auto and we are rendering hints as
     * tooltips then use the option
     * {@inheritdoc}
     */
    public function checkbox($options = [], $enclosedByLabel = false)
    {
        if(
            isset($this->hintOptions['asTooltip']) &&
            $this->hintOptions['asTooltip'] &&
            $this->template == self::TEMPLATE_AUTO
        ){
            $this->template = self::TEMPLATE_TOOLTIP_HINT_CHECKBOX;
        }

        $defaultOptions = [
            'class' => 'form-check-input',
            'labelOptions' => [
                'class' => 'form-check-label',
            ],
        ];

        $options = ArrayHelper::merge($defaultOptions, $options);
        return parent::checkbox($options, $enclosedByLabel);
    }


    /**
     * If our default classes are still set, update to use BS5's form-select class
     * {inheritdoc}
     */
    public function dropDownList($items, $options = [])
    {
        if(is_string($this->inputOptions['class'])){
            $this->inputOptions['class'] = 'form-select';
        } elseif(
            is_array($this->inputOptions['class']) && 
            !isset($this->inputOptions['class']['bs5'])
        ){
            $this->inputOptions['class']['bs5'] = 'form-select';
        }
        parent::dropdownList($items, $options);

        return $this;
    }
}
