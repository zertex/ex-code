<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\widgets\FileInput;
use yii\widgets\MaskedInput;
USE kartik\widgets\DatePicker;
use bookin\aws\checkbox\AwesomeCheckbox;

/* @var $this yii\web\View */
/* @var $model \app\modules\customers\forms\CustomerAccountForm */

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
		<div class="col-md-6">
			<?= $form->field($model, 'name')->textInput(['maxLength' => true]) ?>
		</div>
		<div class="col-md-6">
			<?= $form->field($model, 'lastname')->textInput(['maxLength' => true]) ?>
		</div>
	</div>

	<div class="row">
        <div class="col-md-4">
			<?= $form->field($model, 'position')->textInput(['maxLength' => true]) ?>
        </div>
		<div class="col-md-4">
			<?= $form->field($model, 'email')->textInput(['maxLength' => true]) ?>
		</div>
		<div class="col-md-4">
			<?= $form->field($model, 'phone')->widget(MaskedInput::className(), [
				'mask' => '+9 (999) 999-9999',
			]) ?>
		</div>
	</div>

    <div class="row">
        <div class="col-md-6">
	        <?= $form->field($model, 'comment')->textarea([
		        'rows' => 10,
	        ]) ?>
        </div>
        <div class="col-md-6">
	        <?= $form->field($model, 'title', [
		        'hintOptions' => ['class' => 'hint-block', 'style' => 'display:none'],
	        ])->textarea([
		        'rows' => 5,
	        ]) ?>

	        <?= $form->field($model, 'sub_email')->widget(AwesomeCheckbox::classname(), ['options'=>[], 'style'=>[AwesomeCheckbox::STYLE_PRIMARY]])->label(false) ?>
	        <?= $form->field($model, 'sub_sms')->widget(AwesomeCheckbox::classname(), ['options'=>[], 'style'=>[AwesomeCheckbox::STYLE_PRIMARY]])->label(false) ?>

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
	        ]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'sex')->dropDownList(\app\modules\customers\helpers\CustomerHelper::sexList()) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'birth_date')->widget(DatePicker::classname(), [
	            'options' => [],
	            'pluginOptions' => [
		            'autoclose'=>true
	            ]
            ]); ?>
        </div>
    </div>




	<div class="form-group">
		<?= Html::submitButton(Yii::t('users', 'Save'), ['class' => 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
