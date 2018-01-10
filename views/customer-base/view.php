<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\customers\entities\CustomerBase;

/* @var $this yii\web\View */
/* @var $customerBase \app\modules\customers\entities\CustomerBase */

$this->title = $customerBase->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('customers', 'Customer bases'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="customer-base-view">

    <p>
        <?= Html::a(Yii::t('customers', 'Customer bases'), ['index'], ['class' => 'btn btn-default']) ?>
        <?= Html::a(Yii::t('users', 'Edit'), ['update', 'id' => $customerBase->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('users', 'Delete'), ['delete', 'id' => $customerBase->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('subs', 'Are you sure you want to delete this customer base?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>


    <?= DetailView::widget([
        'model' => $customerBase,
        'attributes' => [
            'name',
            [
                'label' => Yii::t('customers', 'Customers'),
                'value' => function(CustomerBase $customerBase) {
                    return \app\modules\customers\entities\Customer::find()->where(['base_id' => $customerBase->id])->count();
                },
            ],
        ],
    ]) ?>

</div>
