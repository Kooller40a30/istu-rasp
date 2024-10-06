<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Error extends Model
{
    use HasFactory;
    protected $table = 'errors';

    protected $guarded = false;

    public static function getAttributeLabels() 
    {
        return [
            'id' => 'ID ошибки',
            'file' => 'Файл',
            'group' => 'Группа',
            'day' => 'День недели',
            'week' => 'Неделя над/под чертой',
            'class' => 'Пара',
            'value' => 'Ошибка',
            'teacher' => 'Преподаватель',
            'classroom' => 'Аудитория',
            'discipline' => 'Дисциплина',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
        ];
    } 
}
