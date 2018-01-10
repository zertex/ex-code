<?php

/* @var $this yii\web\View */
/* @var $model \app\modules\customers\forms\CustomerForm */
/* @var $customerBases \app\modules\customers\entities\CustomerBase[] */


$this->title = Yii::t('customers', 'Update Customer: {customer}', ['customer' => $model->name]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('customers', 'Customers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('users', 'Editing');

$deleteUrl = \yii\helpers\Url::to(['delete-file']);
$js = <<<JS
    function deleteFile(id, file, div) {
        $.post( "{$deleteUrl}", { id: id, file: file })
        .done(function(data) {
            if (data && data.result == 'success')
            {
                div.remove();
            }
            else {
                alert('Deleting error!');
            }
        });
    }
JS;
$this->registerJs($js, $this::POS_HEAD);

$deleteFileConfirmMessage = Yii::t('customers', 'Are you sure you want to delete this file?');
$js2 = <<<JSS
    $(".delete-file-button").on('click', function() {
        var id = $(this).data('cid');
        var file = $(this).data('file');
        if (confirm('{$deleteFileConfirmMessage}')) {
            (deleteFile(id, file, $(this).parent().parent().parent()));
        }
    });
JSS;
$this->registerJs($js2, $this::POS_READY);
?>
<div class="customer-update">

    <?= $this->render('_form', [
        'model' => $model,
        'customerBases' => $customerBases,
    ]) ?>

<!--
    < ?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-4">
            < ?= $form->field($model, 'name')->textInput(['maxLength' => true]) ?>
        </div>
        <div class="col-md-4">
            < ?php
                $sources = array_combine(CustomerHelper::getSources(), CustomerHelper::getSources());
                $sources[$model->source] = $model->source;
            ?>
            < ?= $form->field($model, 'source')->widget(Select2::classname(), [
                'data' => $sources,
                'pluginOptions' => [
                    'allowClear' => true,
                    'tags' => true,
                ],
            ]); ?>
        </div>
        <div class="col-md-4">
            < ?= $form->field($model, 'city')->textInput(['maxLength' => true]) ?>
        </div>
    </div>


    <div class="row">
        <div class="col-md-4">
            <div class="panel">
                <div class="panel-body">
                    <p>Главный ПАКС</p>
                    <div class="row">
                        <div class="col-md-6">
                            < ?= $form->field($model, 'phone1')->widget(MaskedInput::className(), [
                                'mask' => '+9 (999) 999-9999',
                            ]) ?>
                        </div>
                        <div class="col-md-6">
                            < ?= $form->field($model, 'email1')->textInput(['maxLength' => true]) ?>
                        </div>
                    </div>
                    < ?= $form->field($model, 'etext1')->textarea() ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel">
                <div class="panel-body">
                    <p>Ассистент</p>
                    <div class="row">
                        <div class="col-md-6">
                            < ?= $form->field($model, 'phone2')->widget(MaskedInput::className(), [
                                'mask' => '+9 (999) 999-9999',
                            ]) ?>
                        </div>
                        <div class="col-md-6">
                            < ?= $form->field($model, 'email2')->textInput(['maxLength' => true]) ?>
                        </div>
                    </div>
                    < ?= $form->field($model, 'etext2')->textarea() ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel">
                <div class="panel-body">
                    <p>Дополнительные контакты</p>
                    <div class="row">
                        <div class="col-md-6">
                            < ?= $form->field($model, 'phone3')->textInput(['maxLength' => true]) ?>
                        </div>
                        <div class="col-md-6">
                            < ?= $form->field($model, 'email3')->textInput(['maxLength' => true]) ?>
                        </div>
                    </div>
                    < ?= $form->field($model, 'etext3')->textarea() ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            < ?= $form->field($model, 'description')->textarea() ?>
        </div>
        <div class="col-md-4">
            < ?= $form->field($model, 'additional')->textarea() ?>
        </div>
        <div class="col-md-4">
            < ?= $form->field($model, 'comment')->textarea() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            < ?= $form->field($model, 'requested_dmy')->widget(DatePicker::classname(), [
                'pluginOptions' => [
                    'autoclose'=>true
                ]
            ]) ?>
        </div>
        <div class="col-md-4">
            < ?= $form->field($model, 'manager')->textInput(['maxLength' => true]) ?>
        </div>
        <div class="col-md-4">

            <div class="row">
                <div class="col-md-8">
                    < ?= $form->field($model, 'flag')->radioList(ArrayHelper::map(
                        array_map(function($num, $flag){
                            return [
                                'id' => $num,
                                'name' => '<i style="color: ' . $flag . '" class="fa fa-flag" aria-hidden="true"></i>',
                            ];
                        }, array_keys(CustomerHelper::getFlagsList()), CustomerHelper::getFlagsList()),
                        'id', 'name'), [
                        'item' => function ($index, $label, $name, $checked, $value) {
                            $check = $checked ? ' checked="checked"' : '';
                            return "<label><input type=\"radio\" name=\"$name\" value=\"$value\"$check> <i></i> $label</label>&nbsp;";
                        }
                    ]) ?>
                </div>
                <div class="col-md-4">
                    < ?= $form->field($model, 'status')->dropDownList([
                        '1' => 'Активен',
                        '0' => 'Черновик',
                    ]) ?>
                </div>
            </div>


        </div>
    </div>

    <div class="row">
        < ?php $files = $model->file1 ?>
        < ?php foreach ($files as $file): ?>
            <div id="div< ?= md5($file) ?>" class="col-md-2 col-xs-3" style="text-align: center">

                <div class="thumbnail">

                    < ?php
                        $icons = [
                            'doc' => '<i class="fa fa-file-word-o text-primary"></i>',
                            'xls' => '<i class="fa fa-file-excel-o text-success"></i>',
                            'ppt' => '<i class="fa fa-file-powerpoint-o text-danger"></i>',
                            'pdf' => '<i class="fa fa-file-pdf-o text-danger"></i>',
                            'zip' => '<i class="fa fa-file-archive-o text-muted"></i>',
                            'htm' => '<i class="fa fa-file-code-o text-info"></i>',
                            'txt' => '<i class="fa fa-file-text-o text-info"></i>',
                            'mov' => '<i class="fa fa-file-movie-o text-warning"></i>',
                            'mp3' => '<i class="fa fa-file-audio-o text-warning"></i>',
                            'jpg' => '<i class="fa fa-file-image-o text-success"></i>',
                        ];
                    ?>

                    <div class="file-icon">
                        < ?php $ext = array_pop(explode('.', $file)); ?>
                        < ?php $icon = '<i class="fa fa-file text-muted"></i>'; ?>
                        < ?php if (in_array($ext, ['doc', 'docx'])) {$icon = $icons['doc'];} ?>
                        < ?php if (in_array($ext, ['xls', 'xlsx', 'csv', 'xlsm'])) {$icon = $icons['xls'];} ?>
                        < ?php if (in_array($ext, ['ppt', 'pptx'])) {$icon = $icons['ppt'];} ?>
                        < ?php if (in_array($ext, ['pdf'])) {$icon = $icons['pdf'];} ?>
                        < ?php if (in_array($ext, ['zip', 'arj', 'rar', '7z', 'tar', 'gz'])) {$icon = $icons['zip'];} ?>
                        < ?php if (in_array($ext, ['htm', 'html', 'js', 'css', 'scss', 'sass', 'less', 'xml', 'yml', 'sql'])) {$icon = $icons['html'];} ?>
                        < ?php if (in_array($ext, ['txt', 'ini'])) {$icon = $icons['txt'];} ?>
                        < ?php if (in_array($ext, ['mov', 'avi', 'mp4', 'wmv', 'mpg'])) {$icon = $icons['mov'];} ?>
                        < ?php if (in_array($ext, ['mp3', 'wav'])) {$icon = $icons['mp3'];} ?>
                        < ?php if (in_array($ext, ['jpg', 'png', 'gif', 'jpeg', 'bmp', 'tif', 'tiff'])) {$icon = $icons['jpg'];} ?>

                        < ?= $icon ?>
                    </div>
                    <div class="file-data">
                        < ?= $file ?>
                    </div>

                    <div class="btn-group" style="margin-top: 10px">

                        < ?= Html::a('<i class="fa fa-download" aria-hidden="true"></i>', Yii::getAlias('@static/files/' . $file), [
                            'class' => 'btn btn-sm btn-default',
                            'target' => '_blank',
                        ]) ?>

                        < ?= Html::a('<i class="fa fa-times" aria-hidden="true"></i>', "#", [
                            'class' => 'btn btn-sm btn-danger delete-file-button',
                            'data-cid' => $model->id,
                            'data-file' => $file,
                            //'onclick' => new \yii\web\JsExpression('if (confirm("' . Yii::t('customers', 'Delete this file?') . '")) { deleteFile("'.$model->id.'","'.$file.'")}'),
                            //'data-method' => 'post',
                            //'data-confirm' => Yii::t('customers', 'Delete this file?'),
                        ]) ?>

                    </div>
                </div>
            </div>
        < ?php endforeach; ?>
    </div>

    < ?=  $form->field($model, 'file1[]')->widget(FileInput::classname(), [
        'options' => [
            'multiple' => true,
        ],
        'pluginOptions' => [
            'showUpload' => false,
            /*'initialPreview' => array_map(function($file){
                return Yii::getAlias('@static') . '/files/' . $file;
            }, $model->file1),*/
                /*[
                "http://upload.wikimedia.org/wikipedia/commons/thumb/e/e1/FullMoon2010.jpg/631px-FullMoon2010.jpg",
                "http://upload.wikimedia.org/wikipedia/commons/thumb/6/6f/Earth_Eastern_Hemisphere.jpg/600px-Earth_Eastern_Hemisphere.jpg"
            ],*/
            /*'initialPreviewAsData' => true,
            'initialPreviewConfig' => array_map(function($file){
                $size = filesize(Yii::getAlias('@staticRoot') . '/files/' . $file);
                return [
                    'caption' => $file,
                    'size' => $size,
                    'url' => \yii\helpers\Url::to(['delete-image']),
                    'key' => $file,
                ];
            }, $model->file1),*/
            //'initialCaption'=>"The Moon and the Earth",
            //'initialPreviewConfig' => [
                //['caption' => 'Moon.jpg', 'size' => '873727'],
                //['caption' => 'Earth.jpg', 'size' => '1287883'],
            //],
            //'overwriteInitial'=>false,
            'preferIconicPreview' => true,
            'previewFileIcon' => '<i class="fa fa-file"></i>',
            'previewFileIconSettings' => [
                'doc' => '<i class="fa fa-file-word-o text-primary"></i>',
                'xls' => '<i class="fa fa-file-excel-o text-success"></i>',
                'ppt' => '<i class="fa fa-file-powerpoint-o text-danger"></i>',
                'pdf' => '<i class="fa fa-file-pdf-o text-danger"></i>',
                'zip' => '<i class="fa fa-file-archive-o text-muted"></i>',
                'htm' => '<i class="fa fa-file-code-o text-info"></i>',
                'txt' => '<i class="fa fa-file-text-o text-info"></i>',
                'mov' => '<i class="fa fa-file-movie-o text-warning"></i>',
                'mp3' => '<i class="fa fa-file-audio-o text-warning"></i>',
            ],

            'previewFileExtSettings' => [
                'doc' => new \yii\web\JsExpression('function(ext) {
                    return ext.match(/(doc|docx)$/i);
                }'),
                'xls' => new \yii\web\JsExpression('function(ext) {
                    return ext.match(/(xls|xlsx|xlsm|csv)$/i);
                }'),
                'ppt' => new \yii\web\JsExpression('function(ext) {
                    return ext.match(/(ppt|pptx)$/i);
                }'),
                'pdf' => new \yii\web\JsExpression('function(ext) {
                    return ext.match(/(pdf)$/i);
                }'),
                'zip' => new \yii\web\JsExpression('function(ext) {
                    return ext.match(/(zip|rar|tar|gzip|gz|7z)$/i);
                }'),
                'htm' => new \yii\web\JsExpression('function(ext) {
                    return ext.match(/(php|pl|js|css|scss|sass|less|htm|html|yml|xml|sql)$/i);
                }'),
                'txt' => new \yii\web\JsExpression('function(ext) {
                    return ext.match(/(txt|ini|md)$/i);
                }'),
                'mov' => new \yii\web\JsExpression('function(ext) {
                    return ext.match(/(avi|mpg|mkv|mov|mp4|3gp|webm|wmv)$/i);
                }'),
                'mp3' => new \yii\web\JsExpression('function(ext) {
                    return ext.match(/(mp3|wav)$/i);
                }'),
            ],
        ],
    ]) ?>

    <div class="form-group">
        < ?= Html::submitButton(Yii::t('users', 'Save'), ['class' => 'btn btn-primary']) ?>
    </div>

    < ?php ActiveForm::end(); ?>
-->
</div>