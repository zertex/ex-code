<?php

/* @var $this yii\web\View */
/* @var $model \app\modules\customers\forms\CustomerForm */
/* @var $customerBases \app\modules\customers\entities\CustomerBase[] */

$this->title = Yii::t('customers', 'Create Customer');
$this->params['breadcrumbs'][] = ['label' => Yii::t('customers', 'Customers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-create">

    <?= $this->render('_form', [
        'model' => $model,
        'customerBases' => $customerBases,
    ]) ?>

</div>
