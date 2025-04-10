<?php

namespace App\Services;

use App\Models\Error;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class ErrorService
{
    /**
     * Метод для добавления ошибки в таблицу.
     *
     * @param array $data Данные для записи
     * @param bool $logToSystem Логировать ли ошибку записи в системный лог
     * @return Error|null
     * @throws InvalidArgumentException Если отсутствуют обязательные поля в $data
     */
    public static function logError(array $data, bool $logToSystem = true): ?Error
    {
        try {
            // Подготовка данных (можно совместить с валидацией)
            $errorData = [
                'file' => $data['file'] ?? null,
                'group' => $data['group'] ?? null,
                'day' => $data['day'] ?? null,
                'week' => $data['week'] ?? null,
                'class' => $data['class'] ?? null,
                'value' => $data['value'] ?? null,
                'teacher' => $data['teacher'] ?? null,
                'classroom' => $data['classroom'] ?? null,
                'discipline' => $data['discipline'] ?? null,
            ];
    
            // Валидация обязательных полей
            $requiredFields = ['file', 'day', 'week', 'value'];
            foreach ($requiredFields as $field) {
                // Проверяем именно наличие и непустое значение после подготовки
                if (empty($errorData[$field])) { 
                    throw new InvalidArgumentException("Поле '{$field}' обязательно для логгирования ошибки.");
                }
            }
            
            return Error::create($errorData);
            
        } catch (InvalidArgumentException $e) {
             // Перебрасываем ошибку валидации, так как это проблема вызывающего кода
             throw $e;
        } catch (\Throwable $e) { // Ловим Throwable для большей надежности
            if ($logToSystem) {
                Log::error('Не удалось записать ошибку в БД: ' . $e->getMessage(), [
                    'exception' => $e, // Логируем полное исключение со стектрейсом
                    'original_data' => $data // Логируем исходные данные
                ]);
            }
            return null;
        }
    }
    

    /**
     * Ошибка преобразования времени.
     *
     * @param string $invalidTime Неверное значение времени
     * @param array $context Контекст ошибки
     * @return Error|null
     */
    public static function invalidTimeFormat(string $invalidTime, array $context = []): ?Error
    {
        $context = self::mergeContext($context);
        $context['value'] = "Неверный формат времени: {$invalidTime}";
        return self::logError($context);
    }

    /**
     * Ошибка в данных группы.
     *
     * @param string $group Название группы
     * @param array $context Контекст ошибки
     * @return Error|null
     */
    public static function groupDataError(string $group, array $context = []): ?Error
    {
        $context = self::mergeContext($context);
        $context['group'] = $group;
        $context['value'] = $context['value'] ?? "Ошибка данных группы: {$group}";
        return self::logError($context);
    }

    /**
     * Ошибка в данных преподавателя.
     *
     * @param string $teacher Имя преподавателя
     * @param array $context Контекст ошибки
     * @return Error|null
     */
    public static function teacherDataError(string $teacher, array $context = []): ?Error
    {
        $context = self::mergeContext($context);
        $context['teacher'] = $teacher;
        $context['value'] = $context['value'] ?? "Ошибка данных преподавателя: {$teacher}";
        return self::logError($context);
    }

    /**
     * Ошибка привязки аудитории.
     *
     * @param string $audience Номер аудитории
     * @param array $context Контекст ошибки
     * @return Error|null
     */
    public static function audienceBindingError(string $audience, array $context = []): ?Error
    {
        $context = self::mergeContext($context);
        $context['classroom'] = $audience;
        $context['value'] = $context['value'] ?? "Ошибка привязки аудитории: {$audience}";
        return self::logError($context);
    }

    /**
     * Ошибка в данных дисциплины.
     *
     * @param string $discipline Название дисциплины
     * @param array $context Контекст ошибки
     * @return Error|null
     */
    public static function disciplineDataError(string $discipline, array $context = []): ?Error
    {
        $context = self::mergeContext($context);
        $context['discipline'] = $discipline;
        $context['value'] = $context['value'] ?? "Ошибка данных дисциплины: {$discipline}";
        return self::logError($context);
    }

    /**
     * Ошибка в данных класса.
     *
     * @param string $class Номер или название класса
     * @param array $context Контекст ошибки
     * @return Error|null
     */
    public static function classDataError(string $class, array $context = []): ?Error
    {
        $context = self::mergeContext($context);
        $context['class'] = $class;
        $context['value'] = $context['value'] ?? "Ошибка данных класса: {$class}";
        return self::logError($context);
    }

    /**
     * Объединяет контекст с обязательными полями.
     *
     * @param array $context Дополнительные данные
     * @return array
     */
    private static function mergeContext(array $context): array
    {
        return [
            'file' => $context['file'] ?? 'unknown_file',
            'group' => $context['group'] ?? null,
            'day' => $context['day'] ?? 0,
            'week' => $context['week'] ?? 0,
            'class' => $context['class'] ?? null,
            'value' => $context['value'] ?? null,
            'teacher' => $context['teacher'] ?? null,
            'classroom' => $context['classroom'] ?? null,
            'discipline' => $context['discipline'] ?? null,
        ];
    }
}
