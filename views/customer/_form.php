<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\widgets\FileInput;
use yii\widgets\MaskedInput;
use bookin\aws\checkbox\AwesomeCheckbox;
use kartik\widgets\DatePicker;

/* @var $this yii\web\View */
/* @var $model \app\modules\customers\forms\CustomerForm */
/* @var $customerBases \app\modules\customers\entities\CustomerBase[] */

$js2 = '
$(".hint-block").each(function () {
    var $hint = $(this);
    var label = $hint.parent().find("label");
    label.html(label.html() + \' <i style="color:#3c8dbc" class="fa fa-question-circle" aria-hidden="true"></i>\');
    label.addClass("help").popover({
        html: true,
        trigger: "hover",
        placement: "right",
        content: $hint.html()
    });
    $(this).hide();
});
';
$this->registerJs($js2);
?>
<div class="customer-form">

	<?php $form = ActiveForm::begin([
		'enableClientValidation' => false,
	]); ?>

    <div class="row">
        <div class="col-md-8">

    <?php if (count($customerBases) > 1): ?>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'name', [
	                'hintOptions' => ['class' => 'hint-block', 'style' => 'display:none'],
                ])->textInput(['maxLength' => true]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'base_id')->dropDownList(\yii\helpers\ArrayHelper::map($customerBases, 'id', 'name')) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'status')->dropDownList(\app\modules\customers\helpers\CustomerHelper::statusList()) ?>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-9">
                <?= $form->field($model, 'name')->textInput(['maxLength' => true]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'status')->dropDownList(\app\modules\customers\helpers\CustomerHelper::statusList()) ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'company')->textInput(['maxLength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'website')->textInput(['maxLength' => true]) ?>
        </div>
    </div>

    <?= $form->field($model, 'comment')->textarea(['rows' => 8]) ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'country')->textInput(['maxLength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'city')->textInput(['maxLength' => true]) ?>
        </div>
    </div>


    <div class="row">
        <div class="col-md-4">

            <?php if ($model->photo): ?>
                <div class="thumbnail">
                    <?= Html::img(Yii::getAlias('@static/photos/'.$model->photo), [
                        'style' => 'height:96px',
                    ]) ?>
                </div>
            <?php endif; ?>

            <?=  $form->field($model, 'photo')->widget(FileInput::classname(), [
                'pluginOptions' => [
                    'showUpload' => false,
                ],
            ])->label(Yii::t('customers', 'Company Logo / Photo')) ?>
        </div>
        <div class="col-md-8">

            <?php if (!empty($model->files)): ?>
                <div class="row">
                    <?php $files = $model->files ?>
                    <?php foreach ($files as $file): ?>
                    <div id="div<?= md5($file) ?>" class="col-md-2 col-xs-3" style="text-align: center">

                        <div class="thumbnail">

                            <?php
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

                            <div class="file-icon" title="<?= $file ?>">
                                <?php $ext = array_pop(explode('.', $file)); ?>
                                <?php $icon = '<i class="fa fa-file text-muted"></i>'; ?>
                                <?php if (in_array($ext, ['doc', 'docx'])) {$icon = $icons['doc'];} ?>
                                <?php if (in_array($ext, ['xls', 'xlsx', 'csv', 'xlsm'])) {$icon = $icons['xls'];} ?>
                                <?php if (in_array($ext, ['ppt', 'pptx'])) {$icon = $icons['ppt'];} ?>
                                <?php if (in_array($ext, ['pdf'])) {$icon = $icons['pdf'];} ?>
                                <?php if (in_array($ext, ['zip', 'arj', 'rar', '7z', 'tar', 'gz'])) {$icon = $icons['zip'];} ?>
                                <?php if (in_array($ext, ['htm', 'html', 'js', 'css', 'scss', 'sass', 'less', 'xml', 'yml', 'sql'])) {$icon = $icons['htm'];} ?>
                                <?php if (in_array($ext, ['txt', 'ini'])) {$icon = $icons['txt'];} ?>
                                <?php if (in_array($ext, ['mov', 'avi', 'mp4', 'wmv', 'mpg'])) {$icon = $icons['mov'];} ?>
                                <?php if (in_array($ext, ['mp3', 'wav'])) {$icon = $icons['mp3'];} ?>
                                <?php if (in_array($ext, ['jpg', 'png', 'gif', 'jpeg', 'bmp', 'tif', 'tiff'])) {$icon = $icons['jpg'];} ?>

                                <?= $icon ?>
                            </div>
                            <div class="file-data">
                                <?= \app\helpers\StringHelper::shortFilename($file, 19) ?>
                            </div>

                            <div class="btn-group" style="margin-top: 10px">

                                <?= Html::a('<i class="fa fa-download" aria-hidden="true"></i>', Yii::getAlias('@static/files/' . $file), [
                                'class' => 'btn btn-sm btn-default',
                                'target' => '_blank',
                                ]) ?>

                                <?= Html::a('<i class="fa fa-times" aria-hidden="true"></i>', "#", [
                                'class' => 'btn btn-sm btn-danger delete-file-button',
                                'data-cid' => $model->id,
                                'data-file' => $file,
                                ]) ?>

                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?=  $form->field($model, 'files[]')->widget(FileInput::classname(), [
                'options' => [
                    'multiple' => true,
                ],
                'pluginOptions' => [
                    'showUpload' => false,
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
        </div>
    </div>


        </div>
        <div class="col-md-4" style="border-left: solid 1px #555">
    <?php if (!$model->_customer): ?>

    <div class="lead"><?= Yii::t('customers', 'Contact') ?></div>

    <div class="row">
        <div class="col-md-6">
	        <?= $form->field($model->account, 'name')->textInput() ?>
        </div>
        <div class="col-md-6">
	        <?= $form->field($model->account, 'lastname')->textInput() ?>
        </div>
    </div>

	<?= $form->field($model->account, 'position')->textInput() ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model->account, 'phone')->widget(MaskedInput::className(), [
                'mask' => '+9 (999) 999-9999',
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model->account, 'email')->textInput() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
		    <?= $form->field($model->account, 'sex')->dropDownList(\app\modules\customers\helpers\CustomerHelper::sexList()) ?>
        </div>
        <div class="col-md-6">
		    <?= $form->field($model->account, 'birth_date')->widget(DatePicker::classname(), [
			    'options' => [],
			    'pluginOptions' => [
				    'autoclose'=>true
			    ]
		    ]); ?>
        </div>
    </div>

    <?php

        $model->account->sub_email = 1;
	    $model->account->sub_sms = 1;
	?>

    <div class="row">
        <div class="col-md-6">
	        <?= $form->field($model->account, 'sub_email')->widget(AwesomeCheckbox::classname(), ['options'=>[], 'style'=>[AwesomeCheckbox::STYLE_PRIMARY]])->label(false) ?>
        </div>
        <div class="col-md-6">
	        <?= $form->field($model->account, 'sub_sms')->widget(AwesomeCheckbox::classname(), ['options'=>[], 'style'=>[AwesomeCheckbox::STYLE_PRIMARY]])->label(false) ?>
        </div>
    </div>


    <?php endif; ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('users', 'Save'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>