<?php
/**
 * Created by Error202
 * Date: 14.12.2017
 */

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var $this \yii\web\View
 * @var $account \app\modules\customers\entities\CustomerAccount
 */
$photo = $account->photo ? Yii::getAlias('@static') . '/photos/' . $account->photo : ($account->sex ? Yii::getAlias('@web/images/' . $account->sex . '.png') : Yii::getAlias('@web/images/m.png'));
?>

<div class="contact-card">

    <div class="panel">
        <div class="panel-body">

            <div class="row">
                <div class="col-md-12">
                    <div class="name"><?= $account->lastname ?> <?= $account->name ?></div>
                    <div class="job"><?= $account->position ?></div>
                    <br>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="cc_image" style="background-image: url(<?= $photo ?>)"></div>
                </div>

            <div class="col-md-9">
	            <?= DetailView::widget([
		            'id' => 'sub_data',
		            'model' => $account,
		            'attributes' => array_filter([
			            $account->customer->company ? [
				            'label' =>  Yii::t('customers', 'Company'),
				            'value' => $account->customer->company,
			            ] : false,

			            $account->phone ? [
				            'format' => 'raw',
				            'label' =>  Yii::t('customers', 'Phone'),
				            'value' => Html::a($account->phone, 'tel://' . $account->phone),
			            ] : false,

			            $account->email ? [
				            'format' => 'raw',
				            'label' =>  Yii::t('customers', 'E-mail'),
				            'value' => Html::a($account->email, 'mailto://' . $account->email),
			            ] : false,

			            $account->birth_date ? [
				            'format' => 'raw',
				            'label' =>  Yii::t('customers', 'Birth Date'),
				            'value' => date('d.m.Y', $account->birth_date),
			            ] : false,

			            $account->sex ? [
				            'format' => 'raw',
				            'label' =>  Yii::t('customers', 'Sex'),
				            'value' => \app\modules\customers\helpers\CustomerHelper::sexName($account->sex),
			            ] : false,

			            [
				            'format' => 'boolean',
				            'label' =>  Yii::t('customers', 'E-mail sending'),
				            'value' => $account->sub_email,
			            ],
			            [
				            'format' => 'boolean',
				            'label' =>  Yii::t('customers', 'SMS sending'),
				            'value' => $account->sub_sms,
			            ],

		            ]),
	            ]) ?>
            </div>
        </div>


            <?php if ($account->comment): ?>
            <div class="row">
                <div class="col-md-12">
                    <strong><?= Yii::t('customers', 'Comment') ?></strong>
                    <br>
	                <?= str_replace("\n", '<br>', $account->comment) ?>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>

</div>
