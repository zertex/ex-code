<?php

/* @var $this yii\web\View */
/* @var $model \app\modules\customers\forms\CustomerAccountForm */

$this->title = Yii::t('customers', 'Create Contact');
$this->params['breadcrumbs'][] = ['label' => Yii::t('customers', 'Contacts'), 'url' => ['view', 'id' => $model->customer_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-create">

	<?= $this->render('_form_contact', [
		'model' => $model,
	]) ?>

</div>
