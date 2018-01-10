<?php

use mihaildev\ckeditor\CKEditor;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $result array */

$this->title = Yii::t('customers', 'Customers Import');
$this->params['breadcrumbs'][] = ['label' => Yii::t('customers', 'Customers'), 'url' => ['customer/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="import-result">

    <p class="text-success"><?= Yii::t('customers', 'Success') ?>: <?= $result[0] ?></p>

    <p class="text-danger"><?= Yii::t('customers', 'Failure') ?>: <?= $result[1] ?></p>

    <?php if ($result[2]): ?>
        <p><?= Yii::t('customers', 'Errors found') ?></p>
        <?= Html::textarea('errors', implode("", \yii\helpers\ArrayHelper::getColumn($result[2], 'message')), [
            'id' => 'errors',
            'readonly' => '',
            'class' => 'form-control',
            'rows' => 10,
            'style' => 'background-color: #444',
        ]) ?>
    <?php endif; ?>

</div>
