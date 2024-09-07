<?php

namespace App\Services;

use App\Models\TypeDiscipline;

class TypeDisciplineService
{
    const SHORT_NAMES = [
        // из новой таблицы
        'Л' => 'Л',
        'П' => 'П',
        'ЛБ' => 'ЛБ',
        // из старой таблицы
        'Практические занятия' => 'П',
        'Лабораторные занятия' => 'ЛБ',
        'Лекции' => 'Л',
    ];

    const NAMES = [
        // из новой таблицы
        'Л' => 'Лекция',
        'П' => 'Практика',
        'ЛБ' => 'Лабораторная работа',
        // из старой таблицы
        'Практические занятия' => 'Практика',
        'Лабораторные занятия' => 'Лабораторная работа',
        'Лекции' => 'Лекция',
    ];

    public static function addTypeDiscipline(string $name)
    {        
        $shortName = static::getShortNameTypeDisc($name);
        // if (!$shortName) {
        //     dd($name, $shortName);
        // }
        TypeDiscipline::firstOrCreate(
            ['shortName' => $shortName],
            ['name' => static::getTypeDisciplineName($name)]);
        return TypeDiscipline::where('shortName', $shortName);
    }

    public static function getTypeDisciplineName($name)
    {
        return key_exists($name, static::NAMES) ? static::NAMES[$name] : null;
    }

    public static function getShortNameTypeDisc($name)
    {
        return key_exists($name, static::SHORT_NAMES) ? static::SHORT_NAMES[$name] : null;
    }
}