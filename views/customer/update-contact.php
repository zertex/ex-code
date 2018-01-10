<?php

/* @var $this yii\web\View */
/* @var $model \app\modules\customers\forms\CustomerAccountForm */


$this->title = Yii::t('customers', 'Update Contact: {contact}', ['contact' => $model->name]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('customers', 'Contacts'), 'url' => ['view', 'id' => $model->customer_id]];
$this->params['breadcrumbs'][] = Yii::t('users', 'Editing');
?>
<div class="customer-update">

    <?= $this->render('_form_contact', [
        'model' => $model,
    ]) ?>
</div>