<?php

/* @var $this yii\web\View */
/* @var $base \app\modules\customers\entities\CustomerBase */
/* @var $model \app\modules\customers\forms\CustomerBaseForm */

$this->title = Yii::t('customers', 'Update Customer Base: {base}', ['base' => $model->name]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('customers', 'Customer bases'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('users', 'Editing');
?>
<div class="customer-base-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>