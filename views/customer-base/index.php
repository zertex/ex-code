<?php

use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\customers\entities\CustomerBase;

/* @var $this yii\web\View */
/* @var $searchModel \app\modules\customers\forms\search\CustomerBaseSearch */
/* @var $dataProvider yii\data\ArrayDataProvider */

$this->title = Yii::t('customers', 'Customer bases');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-base-index">

    <p>
        <?= Html::a(Yii::t('customers', 'Create Customer Base'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'filterSelector' => 'select[name="per-page"]',
                'columns' => [
                    [
                        'attribute' => 'name',
                        'format' => 'raw',
                        'value' => function(CustomerBase $model) {
                            return Html::a($model->name, ['view', 'id' => $model->id]);
                        },
                    ],
                    [
                        'class' => ActionColumn::class,
                        'options' => ['style' => 'width: 100px;'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                ],
            ]); ?>

	<?php echo \zertex\gridpagesize\GridPageSize::widget([
		'label' => Yii::t('customers', 'Items per page'),
		'defaultPageSize' => \app\helpers\UserHelper::getSetting('perPage', 50),
		'sizes' => [5,10,20,50,100,200,500],
		'template' => '
                    <div class="row">
                        <div class="col-md-11" style="text-align: right; padding-top: 5px;">
                            {label}
                        </div>
                        <div class="col-md-1">
                            {list}
                        </div>
                    </div>
                ',
		'options' => [
			'class' => 'form-control',
		],
		'callback' => function($newPerPage) {
			\app\helpers\UserHelper::setSetting('perPage', $newPerPage);
		}
	]); ?>

</div>
