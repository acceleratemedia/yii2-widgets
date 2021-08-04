<?php 
namespace bvb\yiiwidget;

/**
 * This class contains some helpful functions and configuraitons that would be
 * useful in any application using widgets from this repository
 */
class Helper
{
    /**
     * @var array Useful container definitions for whne using some of the 
     * widgets from this module
     */
    static $containerDefinitions = [
        \yii\widgets\ActiveForm::class => [
            'validationStateOn' => \yii\widgets\ActiveForm::VALIDATION_STATE_ON_INPUT,
            'errorCssClass' => 'is-invalid'
        ],
        \yii\widgets\ActiveField::class => \bvb\yiiwidget\form\ActiveField::class,
    ];
}