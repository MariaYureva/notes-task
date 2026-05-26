<?php

use common\models\Note;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Note $model */

$colors = Note::PRESET_COLORS;

$action = $model->isNewRecord
    ? Url::to(['/notes'])
    : Url::to(['/notes/' . $model->id]);

$initialColor = $model->color ?: $colors[0];
?>

<div class="note-form" x-data="{ color: '<?= Html::encode($initialColor) ?>' }">
    <?php $form = ActiveForm::begin(['action' => $action]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true, 'autofocus' => true]) ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 8]) ?>

    <div class="mb-3">
        <label class="form-label">Цвет</label>
        <div class="color-picker">
            <?php foreach ($colors as $c): ?>
                <button
                    type="button"
                    class="color-picker__dot"
                    style="background: <?= Html::encode($c) ?>;"
                    :class="{ 'is-active': color === '<?= Html::encode($c) ?>' }"
                    @click="color = '<?= Html::encode($c) ?>'"
                    aria-label="<?= Html::encode($c) ?>"
                ></button>
            <?php endforeach; ?>
        </div>
        <input type="hidden" name="Note[color]" :value="color">
        <?= Html::error($model, 'color', ['class' => 'invalid-feedback d-block']) ?>
    </div>

    <?= $form->field($model, 'is_pinned')->checkbox() ?>

    <div class="d-flex" style="gap: 8px;">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        <a href="<?= Url::to(['/notes']) ?>" class="btn btn-link">Отмена</a>
    </div>

    <?php ActiveForm::end(); ?>
</div>
