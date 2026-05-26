<?php

namespace frontend\models;

use common\models\Note;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class NoteSearch extends Model
{
    public $q;

    public function rules()
    {
        return [
            [['q'], 'string', 'max' => 255],
            [['q'], 'trim'],
        ];
    }

    public function search($params, $userId)
    {
        $query = Note::find()->where(['user_id' => $userId]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'is_pinned' => SORT_DESC,
                    'updated_at' => SORT_DESC,
                ],
            ],
        ]);

        $this->load($params);

        if (!empty($this->q)) {
            $query->andFilterWhere(['like', 'title', $this->q]);
        }

        return $dataProvider;
    }
}
