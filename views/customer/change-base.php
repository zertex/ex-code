<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use kartik\widgets\Select2;

/**
 * @var $this \yii\web\View
 * @var $model \app\modules\customers\forms\ChangeGroupForm
 * @var $bases array()
 * @var $count integer
 */

$this->title = Yii::t('customers', 'Change Customers Base');
$this->params['breadcrumbs'][] = ['label' => Yii::t('customers', 'Customers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="customer-change_base">

    <?php $form = ActiveForm::begin(); ?>

    <div style="margin: 5px 0 15px">
        <?= Yii::t('customers', 'Selected customers: {count}', ['count' => $count]) ?>
    </div>

    <?= $form->field($model, 'base_id')->widget(
        Select2::classname(), [
        'name' => 'childrenRoles',
        'data' => $bases,
        'options' => ['placeholder' => Yii::t('customers', '- Select base -'), 'multiple' => false],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    ?>

    <?= $form->field($model, 'ids')->hiddenInput()->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('users', 'Save'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
