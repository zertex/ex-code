<?php
;
use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\customers\helpers\CustomerHelper;
use app\modules\customers\entities\Customer;
use kartik\dialog\Dialog;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $customer \app\modules\customers\entities\Customer */
/* @var $accounts \app\modules\customers\entities\CustomerAccount[] */
/* @var $searchModel \app\modules\deals\forms\search\DealSearch */
/* @var $dataProvider yii\data\ArrayDataProvider */

$this->title = $customer->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('customers', 'Customers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$basesCount = \app\modules\customers\entities\CustomerBase::find()->count();

$css = <<<CSS
#main_data tr th {
    width: 300px;
}

#info-button-hidden {
    display: none;
}
CSS;
$this->registerCss($css);

//$this->registerCssFile(Yii::getAlias('@web/css/accounts.css'));

$accountCreateUrl = Url::to(['create-contact', 'cid' => $customer->id]);
$getInfoUrl = Url::to(['contact-info']);
$js = <<<JS
    $("#newAccountButton").on('click', function() {
        document.location.href = '{$accountCreateUrl}';
    })
    
    $('.info-view-button').on('click', function() {
        var id = $(this).data('id');
        
        $.get( "{$getInfoUrl}", { id: id })
        .done(function( data ) {
            accountInfo.dialog(
                $(data),
                function (result) {
                    alert(result);
                }
            );
        });
    });
JS;
$this->registerJs($js, $this::POS_READY);
?>
<div class="customers-view">

    <?= Dialog::widget([
	    'libName' => 'accountInfo',
	    'options' => [
            'size' => Dialog::SIZE_WIDE,
		    'type' => Dialog::TYPE_PRIMARY,
		    'title' => Yii::t('customers', 'Contact'),
		    'message' => 'Message',
		    'buttons' => [
			    [
				    'id' => 'info-button-hidden',
				    'label' => 'Hidden',
			    ],
			    [
				    'id' => 'info-button-cancel',
				    'label' => Yii::t('customers', 'Close'),
				    'icon' => '',
				    'cssClass' => 'btn-primary',
				    'action' => new JsExpression("function(dialog) {
				        dialog.close();
				    }")
			    ],
            ],
        ],
    ]); ?>

    <p>
		<?= Html::a(Yii::t('customers', 'Customers'), $basesCount > 1 ? ['index', 'base_id' => $customer->base_id] : ['index'], ['class' => 'btn btn-default']) ?>
		<?= Html::a(Yii::t('users', 'Edit'), ['update', 'id' => $customer->id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('users', 'Delete'), ['delete', 'id' => $customer->id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => Yii::t('customers', 'Are you sure you want to delete this customer?'),
				'method' => 'post',
			],
		]) ?>
    </p>

    <div class="row">

        <div class="col-md-9">

            <div class="row">
                <div class="col-md-4">
                    <div class="panel">
                        <div class="panel-body">
						    <?php if ($customer->photo): ?>
                                <div class="thumbnail">
								    <?= Html::img(Yii::getAlias('@static/photos/'.$customer->photo), [
									    'style' => 'max-width:200px',
								    ]) ?>
                                </div>
						    <?php else: ?>
                                <div style="padding:30px; border:solid 1px #555; text-align: center;">
                                    Нет фото
                                </div>
						    <?php endif; ?>
                            <br>
						    <?= DetailView::widget([
							    'id' => 'sub_data',
							    'model' => $customer,
							    'attributes' => [
								    [
									    'attribute' => 'status',
									    'format' => 'raw',
									    'value' => function (Customer $model) {
										    return CustomerHelper::statusLabel($model->status);
									    },
								    ],
								    [
									    'attribute' => 'created_at',
									    'format' => 'date',
								    ],
								    [
									    'attribute' => 'updated_at',
									    'format' => 'date',
								    ],
							    ],
						    ]);?>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
				    <?= DetailView::widget([
					    'id' => 'main_data',
					    'model' => $customer,
					    'attributes' => [
						    'name',
						    [
							    'attribute' => 'user.username',
							    'label' => Yii::t('customers', 'Manager'),
						    ],
						    [
							    'attribute' => 'website',
							    'label' => Yii::t('customers', 'Web site'),
							    'format' => 'raw',
							    'value' => function (Customer $customer) {
								    return Html::a($customer->website, $customer->website, [
									    'target' => '_blank',
								    ]);
							    },
						    ],
						    [
							    'attribute' => 'files',
							    'label' => Yii::t('customers', 'Files'),
							    'format' => 'raw',
							    'value' => function (Customer $customer) {
								    $files = \yii\helpers\Json::decode($customer->files, true);
								    $links = [];
								    foreach ($files as $file) {
									    array_push($links, Html::a($file, $file));
								    }
								    return implode(', ', $links);
							    },
						    ],
						    'comment:ntext',
					    ],
				    ]) ?>
                </div>
            </div>

	        <?php if (Yii::$app->user->can('DealManagement') || Yii::$app->user->can('admin')): ?>
                <div class="lead"><?= Yii::t('deals', 'Deals') ?></div>

		        <?= $this->render('../../../deals/views/deal/index', [
			        'searchModel' => $searchModel,
			        'dataProvider' => $dataProvider,
			        'base_id' => $basesCount > 1 ? ['index', 'base_id' => $customer->base_id] : ['index'],
			        'customer_id' => $customer->id,
		        ]); ?>
	        <?php endif; ?>

        </div>

        <div class="col-md-3">
	        <?php foreach ($accounts as $account): ?>

		        <?= $this->render('_view_account', [
			        'account' => $account,
		        ]) ?>

	        <?php endforeach; ?>

            <div class="business-card-empty" id="newAccountButton">
                <i class="fa fa-plus-circle" aria-hidden="true"></i><br><?= Yii::t('customers', 'Add contact') ?>
            </div>
        </div>

    </div>


</div>