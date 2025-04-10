<?php

namespace App\Services;

use App\Models\TypeDiscipline;
use InvalidArgumentException;

class TypeDisciplineService
{
    // Объединенная карта для всех типов имен
    private const TYPE_MAP = [
        // Короткие названия
        'Л' => ['short' => 'Л', 'full' => 'Лекция'],
        'П' => ['short' => 'П', 'full' => 'Практика'],
        'ЛБ' => ['short' => 'ЛБ', 'full' => 'Лабораторная работа'],
        
        // Полные названия
        'ЛЕКЦИЯ' => ['short' => 'Л', 'full' => 'Лекция'],
        'ПРАКТИКА' => ['short' => 'П', 'full' => 'Практика'],
        'ЛАБОРАТОРНАЯ РАБОТА' => ['short' => 'ЛБ', 'full' => 'Лабораторная работа'],
        
        // Варианты множественного числа
        'ЛЕКЦИИ' => ['short' => 'Л', 'full' => 'Лекция'],
        'ПРАКТИКИ' => ['short' => 'П', 'full' => 'Практика'],
        'ЛАБОРАТОРНЫЕ РАБОТЫ' => ['short' => 'ЛБ', 'full' => 'Лабораторная работа'],
        
        // Старые варианты
        'ПРАКТИЧЕСКИЕ ЗАНЯТИЯ' => ['short' => 'П', 'full' => 'Практика'],
        'ЛАБОРАТОРНЫЕ ЗАНЯТИЯ' => ['short' => 'ЛБ', 'full' => 'Лабораторная работа'],
        
        // Варианты с пробелами
        'Л ' => ['short' => 'Л', 'full' => 'Лекция'],
        'П ' => ['short' => 'П', 'full' => 'Практика'],
        'ЛБ ' => ['short' => 'ЛБ', 'full' => 'Лабораторная работа'],
    ];

    /**
     * Нормализует входное значение для поиска в карте типов.
     *
     * @param string $name Входное имя типа дисциплины.
     * @return string Нормализованное имя.
     */
    private static function normalizeName(string $name): string
    {
        // Удаляем лишние пробелы и приводим к верхнему регистру
        return trim(mb_strtoupper($name));
    }

    /**
     * Находит или создает запись TypeDiscipline на основе предоставленного имени.
     *
     * @param string $name Входное имя типа дисциплины (может быть старым или новым форматом).
     * @return TypeDiscipline Найденная или созданная модель TypeDiscipline.
     * @throws InvalidArgumentException Если предоставленное имя не найдено в карте типов.
     */
    public static function addTypeDiscipline(string $name): TypeDiscipline
    {
        $normalizedName = static::normalizeName($name);
        $typeInfo = static::getTypeInfo($normalizedName);

        if ($typeInfo === null) {
            \Illuminate\Support\Facades\Log::warning('[TypeDisciplineService] Action: Unknown discipline type', ['name' => $normalizedName]);
            throw new InvalidArgumentException("Неизвестный тип дисциплины: '{$normalizedName}'");
        }

        $typeDiscipline = TypeDiscipline::firstOrCreate(
            ['shortName' => $typeInfo['short']],
            ['name' => $typeInfo['full']]
        );

        \Illuminate\Support\Facades\Log::info('[TypeDisciplineService] Action: Retrieved discipline type', [
            'type' => $name,
            'normalized' => $normalizedName,
            'short' => $typeInfo['short'],
            'full' => $typeInfo['full'],
            'type_id' => $typeDiscipline->id,
        ]);

        return $typeDiscipline;
    }

    /**
     * Получает стандартизированное полное имя типа дисциплины.
     *
     * @param string $name Входное имя типа дисциплины.
     * @return string|null Полное имя или null, если имя не найдено.
     */
    public static function getTypeDisciplineName(string $name): ?string
    {
        return static::TYPE_MAP[$name]['full'] ?? null;
    }

    /**
     * Получает стандартизированное короткое имя типа дисциплины.
     *
     * @param string $name Входное имя типа дисциплины.
     * @return string|null Короткое имя или null, если имя не найдено.
     */
    public static function getShortNameTypeDisc(string $name): ?string
    {
        return static::TYPE_MAP[$name]['short'] ?? null;
    }

    /**
     * Вспомогательный метод для получения информации о типе (короткое и полное имя).
     *
     * @param string $name Входное имя типа дисциплины.
     * @return array|null Массив с ключами 'short' и 'full' или null, если имя не найдено.
     */
    private static function getTypeInfo(string $name): ?array
    {
        return static::TYPE_MAP[$name] ?? null; // Используем ?? для краткости
    }
}