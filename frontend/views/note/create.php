<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Note $model */

$this->title = 'Новая заметка';
?>
<h1><?= Html::encode($this->title) ?></h1>

<?= $this->render('_form', ['model' => $model]) ?>
