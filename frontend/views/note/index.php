<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

/** @var yii\web\View $this */
/** @var frontend\models\NoteSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Заметки';
?>
<h1><?= Html::encode($this->title) ?></h1>

<?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success">
        <?= Html::encode(Yii::$app->session->getFlash('success')) ?>
    </div>
<?php endif; ?>

<div class="notes-toolbar">
    <form method="get" action="<?= Url::to(['/notes']) ?>" class="d-flex" style="gap: 8px;">
        <input
            type="text"
            name="NoteSearch[q]"
            value="<?= Html::encode($searchModel->q ?? '') ?>"
            placeholder="Поиск по заголовку"
            class="form-control"
            style="max-width: 280px;"
        >
        <button type="submit" class="btn btn-outline-secondary">Найти</button>
        <?php if (!empty($searchModel->q)): ?>
            <a href="<?= Url::to(['/notes']) ?>" class="btn btn-link">Сбросить</a>
        <?php endif; ?>
    </form>

    <a href="<?= Url::to(['/notes/create']) ?>" class="btn btn-primary">+ Новая заметка</a>
</div>

<?php $notes = $dataProvider->getModels(); ?>

<?php if (empty($notes)): ?>
    <div class="notes-empty">
        Заметок пока нет. Создайте первую.
    </div>
<?php else: ?>
    <div class="notes-grid">
        <?php foreach ($notes as $note): ?>
            <div class="note-card">
                <div class="note-card__stripe" style="background: <?= Html::encode($note->color) ?>;"></div>
                <div class="note-card__body">
                    <div class="note-card__head">
                        <h3 class="note-card__title"><?= Html::encode($note->title) ?></h3>
                        <button
                            type="button"
                            x-data="{ pinned: <?= $note->is_pinned ? 'true' : 'false' ?>, loading: false }"
                            :class="{ 'is-pinned': pinned }"
                            :title="pinned ? 'Открепить' : 'Закрепить'"
                            :disabled="loading"
                            class="note-card__pin"
                            @click="
                                loading = true;
                                fetch('<?= Url::to(['/notes/' . $note->id . '/toggle-pin']) ?>', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-Token': document.querySelector('meta[name=csrf-token]').content,
                                        'X-Requested-With': 'XMLHttpRequest',
                                    },
                                })
                                .then(r => r.json())
                                .then(data => { if (data.success) { pinned = data.is_pinned === 1; } })
                                .finally(() => { loading = false; });
                            "
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                <path d="M9.828.722a.5.5 0 0 1 .354.146l4.95 4.95a.5.5 0 0 1 0 .707c-.48.48-1.072.588-1.503.588-.177 0-.335-.018-.46-.039l-3.134 3.134a6 6 0 0 1 .16 1.013c.046.702-.032 1.687-.72 2.375a.5.5 0 0 1-.707 0l-2.829-2.828-3.182 3.182c-.195.195-1.219.902-1.414.707-.195-.195.512-1.22.707-1.414l3.182-3.182-2.828-2.829a.5.5 0 0 1 0-.707c.688-.688 1.673-.767 2.375-.72a6 6 0 0 1 1.013.16l3.134-3.133a3 3 0 0 1-.04-.461c0-.43.108-1.022.589-1.503a.5.5 0 0 1 .353-.146"/>
                            </svg>
                        </button>
                    </div>

                    <?php if (!empty($note->content)): ?>
                        <div class="note-card__content">
                            <?= nl2br(Html::encode(mb_substr($note->content, 0, 200))) ?>
                            <?= mb_strlen($note->content) > 200 ? '…' : '' ?>
                        </div>
                    <?php endif; ?>

                    <div class="note-card__actions">
                        <a href="<?= Url::to(['/notes/' . $note->id . '/edit']) ?>" class="btn btn-sm btn-outline-secondary">
                            Редактировать
                        </a>
                        <?= Html::beginForm(['/notes/' . $note->id . '/delete'], 'post', ['style' => 'display:inline']) ?>
                        <?= Html::submitButton('Удалить', [
                            'class' => 'btn btn-sm btn-outline-danger',
                            'data-confirm' => 'Удалить заметку?',
                        ]) ?>
                        <?= Html::endForm() ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?= LinkPager::widget([
        'pagination' => $dataProvider->pagination,
    ]) ?>
<?php endif; ?>
