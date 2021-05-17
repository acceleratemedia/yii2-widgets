<?php
/* @var $form \yii\widgets\ActiveForm */
/* @var $linkTextPrefix string */
/* @var $model \yii\base\Model */

use bvb\yiiwidget\slug\Slugify;
use yii\helpers\Html;
?>

<div class="name-slug-container">
    <div class="name-container">
        <?= $form->field($model, 'name', ['options' => ['class' => 'form-group'], 'template' => '{input}{error}'])
            ->textInput(['maxlength' => true, 'placeholder' => 'Enter Name Here']) ?>
    </div>

    <div class="slug-container">
        <?php
        $slugHint = (isset($model->attributeHints()['slug']) ? $model->attributeHints()['slug'] : 'Unique string to identify this object usually for SEO friendly URLs');
        echo $form->field($model, 'slug', [
                'options' => [
                    'class' => 'slug-field-container'
                ],
                'template' => '<span class="form-text">Link: '.$linkTextPrefix.'/<span id="dynamic-slug-text">'.$model->slug.'</span><i class="fas fa-edit"></i></span>'."\n{input}\n{hint}\n{error}"
            ])->hint($slugHint)
            ->textInput(['maxlength' => true]) ?>
    </div>
</div>

<?php
$nameInputId = Html::getInputId($model, 'name');
$slugInputId = Html::getInputId($model, 'slug');
// --- Slugify the name
Slugify::widget([
    'generatingInputId' => $nameInputId,
    'receivingElementId' => $slugInputId,
]);

$js = <<<JAVASCRIPT
document.querySelector('.slug-field-container .fa-edit').addEventListener('click', function(e){
    document.querySelectorAll("#dynamic-slug-text, .slug-field-container .fa-edit").forEach(function(item){
        item.style = 'display:none';
    })
    document.querySelector('.slug-field-container input').style = 'display:block';
});

document.getElementById('{$slugInputId}').addEventListener('change', function(e){
    document.getElementById("dynamic-slug-text").innerHTML = this.value;
});
JAVASCRIPT;

$this->registerJs($js, \yii\web\View::POS_END);

$css = <<<CSS
.slug-container{
    padding-top:3px;
}
.slug-container input.form-control{
    border-radius:3px !important;
    display:none;
    flex: .5 1 auto !important;
    font-size:.8rem;
    height: calc(1.125rem + 2px);
    padding:3px;
    margin-top:3px;
    width:30%;
}
.name-slug-container{
    margin-bottom:10px;
}
.slug-field-container{
    display: flex;
}
.slug-field-container .invalid-feedback{
    width:auto;
}
.slug-container a.btn{
    background-color:#e9ecef;
    border: 1px solid #ced4da;
}
#dynamic-slug-text{padding-right:10px;}
CSS;
$this->registerCss($css);
