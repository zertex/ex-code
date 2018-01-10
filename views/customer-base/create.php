<?php

/* @var $this yii\web\View */
/* @var $model \app\modules\customers\forms\CustomerBaseForm */

$this->title = Yii::t('customers', 'Create Customer Base');
$this->params['breadcrumbs'][] = ['label' => Yii::t('customers', 'Customer bases'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-base-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>