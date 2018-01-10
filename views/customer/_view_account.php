<?php

use yii\helpers\Url;
use yii\helpers\Html;

/**
 * @var $account \app\modules\customers\entities\CustomerAccount
 */
$photo = $account->photo ? Yii::getAlias('@static') . '/photos/' . $account->photo : ($account->sex ? Yii::getAlias('@web/images/' . $account->sex . '.png') : Yii::getAlias('@web/images/m.png'));
?>

<div class="business-card">
	<div class="media">
		<div class="media-left">
            <div class="business-card-avatar" style="background-image: url(<?= $photo ?>)"></div>
		</div>
		<div class="media-body">
			<div class="name"><?= $account->lastname ?> <?= $account->name ?></div>
			<div class="job"><?= $account->position ?></div>

			<div class="buttons">

                <a href="#" class="info-view-button" data-id="<?= $account->id ?>">
                                <span class="fa-stack fa-lg">
                                  <i class="fa fa-circle fa-stack-2x"></i>
                                  <i class="fa fa-eye fa-stack-1x fa-inverse"></i>
                                </span>
                </a>

				<a href="<?= Url::to(['update-contact', 'id' => $account->id]) ?>" title="<?= Yii::t('customers', 'Edit') ?>">
                                <span class="fa-stack fa-lg">
                                  <i class="fa fa-circle fa-stack-2x"></i>
                                  <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                                </span>
				</a>

                <?php
                    $deleteLable = '<span class="fa-stack fa-lg">
                                  <i class="fa fa-circle fa-stack-2x"></i>
                                  <i class="fa fa-times fa-stack-1x fa-inverse"></i>
                                </span>';

                    $deleteLink = Html::a($deleteLable, ['delete-contact', 'id' => $account->id], [
	                    'title' => Yii::t('customers', 'Delete'),
	                    'data' => [
		                    'confirm' => Yii::t('customers', 'Are you sure you want to delete this contact?'),
		                    'method' => 'post',
	                    ],
                    ]);

                ?>

                <?= $deleteLink ?>

			</div>

		</div>
	</div>
	<div class="clearfix"></div>
</div>
