<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model \app\modules\customers\forms\ImportFileForm */
/* @var $bases \app\modules\customers\entities\CustomerBase[] */

$this->title = Yii::t('customers', 'Customers Import');
$this->params['breadcrumbs'][] = ['label' => Yii::t('customers', 'Customers'), 'url' => ['customer/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="import-import">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'file')->fileInput() ?>

    <?php if (count($bases) > 1): ?>
        <?= $form->field($model, 'base_id')->dropDownList(ArrayHelper::map($bases, 'id', 'name')) ?>
    <?php else: ?>
        <?= $form->field($model, 'base_id')->hiddenInput(['value' => $bases[0]->id])->label(false) ?>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('customers', 'Next'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
