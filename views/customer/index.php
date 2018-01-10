<?php

use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\customers\entities\Customer;
use app\modules\customers\helpers\CustomerHelper;
use yii\helpers\Url;
use app\widgets\grid\CheckBoxColumn;

/* @var $this yii\web\View */
/* @var $searchModel \app\modules\customers\forms\search\CustomerSearch */
/* @var $dataProvider yii\data\ArrayDataProvider */
/* @var $base_id integer */

$this->title = Yii::t('customers', 'Customers');
if ($base_id)
{
    $base = \app\modules\customers\entities\CustomerBase::findOne($base_id);
    if ($base)
    {
        $this->title = $base->name;
    }
}

$this->params['breadcrumbs'][] = $this->title;

$statusUrl = \yii\helpers\Url::to(['toggle-status']);
$js = <<<JS
    $('.select-on-check-all').addClass('grid_checkbox');

    var body = $('body');
    var customerId = 0;
    body.on('click', '.customer-status', function(e) {
        e.preventDefault();
        customerId = $(this).data('cid');
        $.get( "{$statusUrl}", { id: customerId } )
            .done(function( data ) {
            $("#c" + customerId).html(data);
        });
    });
    
    body.on('change', '.grid_checkbox', function(e) {
        var button = $('#groupActions');
        if (!multiSelected()) {
            button.attr('disabled', 'disabled');
            button.addClass('disabled');
        }
        else {
            button.removeAttr('disabled');
            button.removeClass('disabled');
        }
    });
JS;
$this->registerJs($js, $this::POS_READY);

$deleteAllUrl = Url::to(['delete-selected']);
$deleteConfirmMessage = Yii::t('customers', 'Are you sure you want to delete selected customers?');
$actionChangeBase = Url::to(['change-base']);
$actionChangeManager = Url::to(['change-manager']);
$js2 = <<<JS2
    function getRows()
    {
        var ids = new Array();
        $('input[name="gridSelection[]"]:checked').each(function() {
            ids.push(this.value);
        });
        return ids;
    }
    
    function multiSelected() {
        var result = false;
        $('input[name="gridSelection[]"]:checked').each(function() {
            result = true;
        });
        return result;
    }

    
    function deleteSelected() {
        if (confirm('{$deleteConfirmMessage}')) {
            var ids = JSON.stringify(getRows());
            $.post( "{$deleteAllUrl}", { ids: ids, base_id: '{$base_id}' })
            .done(function( data ) {
                document.location.reload();
            });
        }
    }
    
    function changeGroup() {
        $('#mainIndex').attr('action', '{$actionChangeBase}')
        $('#mainIndex').submit();
    }
    
    function changeManager() {
        $('#mainIndex').attr('action', '{$actionChangeManager}')
        $('#mainIndex').submit();
    }
JS2;
$this->registerJs($js2, $this::POS_HEAD);
?>
<div class="customer-index">

    <div style="margin-bottom: 10px;">
        <?= Html::a(Yii::t('customers', 'Create Customer'), ['create', 'base_id' => $base_id], ['class' => 'btn btn-success']) ?>

        <?php if (Yii::$app->user->can('ImportManagement') || Yii::$app->user->can('admin')): ?>
            <?= Html::a(Yii::t('customers', 'Import'), ['import/import'], ['class' => 'btn btn-default']) ?>
        <?php endif; ?>

        <?php if (Yii::$app->user->can('ExportManagement') || Yii::$app->user->can('admin')): ?>
            <?= Html::a(Yii::t('customers', 'Export'), ['export/export', 'base_id' => $base_id], ['class' => 'btn btn-default']) ?>
        <?php endif; ?>

        <div class="btn-group">
            <?= Html::a(Yii::t('customers', 'Group actions {caret}', ['caret' => '<span class="caret">']), '#', [
                'class' => 'btn btn-default dropdown-toggle disabled',
                'id' => 'groupActions',
                'disabled' => 'disabled',
                'data-toggle' => 'dropdown',
                'aria-haspopup' => 'true',
                'aria-expanded' => 'false',
            ]) ?>

            <ul class="dropdown-menu">
                <li><?= Html::a(Yii::t('customers', 'Change base...'), '#', [
                        'onclick' => new \yii\web\JsExpression('changeGroup()'),
                    ]) ?></li>
                <li><?= Html::a(Yii::t('customers', 'Delete selected'), '#', [
                        'onclick' => new \yii\web\JsExpression('deleteSelected()'),
                    ]) ?></li>
                <li role="separator" class="divider"></li>
                <li><?= Html::a(Yii::t('customers', 'Change manager...'), '#', [
                        'onClick' => new \yii\web\JsExpression('changeManager()'),
                    ]) ?></li>
            </ul>
        </div>

    </div>

    <?=Html::beginForm(['change-base'],'post', ['id' => 'mainIndex']);?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'filterSelector' => 'select[name="per-page"]',
                'columns' => [
                    [
                        'class' => CheckBoxColumn::class,
                    ],
                    [
                        'attribute' => 'id',
                        'label' => '№',
                        'options' => ['style' => 'width: 40px;'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'attribute' => 'name',
                        'format' => 'raw',
                        'value' => function(Customer $model) use ($base_id) {
                            return Html::a($model->name, $base_id ? ['view', 'id' => $model->id, 'base_id' => $base_id] :['view', 'id' => $model->id]);
                        },
                    ],
                    [
                        'attribute' => 'user_id',
                        'value' => function(Customer $model) {
                            return \app\entities\User::findOne($model->user_id)->username;
                        },
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'filter' => CustomerHelper::statusList(),
                        'value' => function (Customer $model) {
                            //return CustomerHelper::statusLabel($model->status);
                            return Html::a(CustomerHelper::statusLabel($model->status), '#',[
                                'class' => 'customer-status',
                                'data-cid' => $model->id,
                                'id' => 'c' . $model->id,
                            ]);
                        },
                        'options' => ['style' => 'width: 130px;'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'attribute' => 'created_at',
                        'format' => 'date',
                        'options' => ['style' => 'width: 130px;'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'class' => ActionColumn::class,
                        'options' => ['style' => 'width: 100px;'],
                        'contentOptions' => ['class' => 'text-center'],
                        'urlCreator' => function ($action, $model, $key, $index) use ($base_id) {
                            return $base_id ? Url::to([$action, 'id' => $model['id'], 'base_id' => $base_id]) : Url::to([$action, 'id' => $model['id']]);
                        }
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

        <?= Html::hiddenInput('base_id', $base_id) ?>
    <?= Html::endForm();?>

</div>
