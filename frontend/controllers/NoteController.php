<?php

namespace frontend\controllers;

use common\models\Note;
use frontend\models\NoteSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class NoteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['GET', 'POST'],
                    'update' => ['GET', 'POST'],
                    'delete' => ['POST'],
                    'toggle-pin' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new NoteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Yii::$app->user->id);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new Note();
        $model->user_id = Yii::$app->user->id;
        $model->color = '#6366f1';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Заметка создана.');
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findOwnModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Заметка обновлена.');
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findOwnModel($id);
        $model->delete();

        Yii::$app->session->setFlash('success', 'Заметка удалена.');
        return $this->redirect(['index']);
    }

    public function actionTogglePin($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = $this->findOwnModel($id);
        $model->is_pinned = $model->is_pinned ? 0 : 1;

        if (!$model->save(false)) {
            return ['success' => false];
        }

        return [
            'success' => true,
            'is_pinned' => (int) $model->is_pinned,
        ];
    }

    protected function findOwnModel($id)
    {
        $model = Note::findOne((int) $id);

        if ($model === null) {
            throw new NotFoundHttpException('Заметка не найдена.');
        }

        if ($model->user_id !== Yii::$app->user->id) {
            throw new ForbiddenHttpException('Нет доступа к этой заметке.');
        }

        return $model;
    }
}
