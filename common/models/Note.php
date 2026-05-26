<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "notes".
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string|null $content
 * @property string $color
 * @property int $is_pinned
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 */
class Note extends ActiveRecord
{
    const PRESET_COLORS = [
        '#6366f1',
        '#ef4444',
        '#f59e0b',
        '#10b981',
        '#3b82f6',
        '#a855f7',
    ];

    public static function tableName()
    {
        return '{{%notes}}';
    }

    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title'], 'string', 'max' => 255],
            [['content'], 'string'],
            [['color'], 'match', 'pattern' => '/^#[0-9a-fA-F]{6}$/', 'message' => 'Введите цвет в формате #RRGGBB'],
            [['color'], 'default', 'value' => '#6366f1'],
            [['is_pinned'], 'boolean'],
            [['user_id'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => 'Заголовок',
            'content' => 'Содержимое',
            'color' => 'Цвет',
            'is_pinned' => 'Закреплено',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
