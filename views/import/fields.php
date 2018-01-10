<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model \app\modules\customers\forms\ImportFileForm */
/* @var $headers array */
/* @var $fields array */
/* @var $forms array */

$this->title = Yii::t('customers', 'Customers Import');
$this->params['breadcrumbs'][] = ['label' => Yii::t('customers', 'Customers'), 'url' => ['customer/index']];
$this->params['breadcrumbs'][] = $this->title;

$headersSelect = array_merge(['' => Yii::t('customers', '- Skip -')], $headers);
$headersArray = array_combine(array_keys($headersSelect), $headersSelect);
?>

<div class="import-fields">

    <?php $form = ActiveForm::begin(); ?>

    <div class="lead">
        <?= Yii::t('customers', 'Select which columns of the file correspond to the customer fields') ?>
    </div>

    <?php foreach ($forms as $index=>$item): ?>
        <?= $form->field($item, "[$index]value")->dropDownList($headersArray)->label($fields[$index]); ?>
    <?php endforeach; ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('customers', 'Import'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
