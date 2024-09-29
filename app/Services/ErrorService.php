<?php

namespace App\Services;

use App\Models\Error;
use Illuminate\Support\Facades\Log;
use Exception;

class ErrorService
{
    /**
     * Метод для добавления ошибки в таблицу.
     *
     * @param array $data Данные для записи
     * @return Error|null
     */
    public static function logError(array $data, bool $logToSystem = true): ?Error
    {
        try {
            // Подготовка данных
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
            foreach (['file', 'day', 'week', 'value'] as $field) {
                if (empty($errorData[$field])) {
                    throw new Exception("Поле '{$field}' обязательно для заполнения.");
                }
            }
    
            return Error::create($errorData);
            
        } catch (Exception $e) {
            if ($logToSystem) {
                // Логирование только сообщения исключения и первого предыдущего, если есть
                $logMessage = 'Не удалось записать ошибку: ' . $e->getMessage();
                if ($e->getPrevious()) {
                    $logMessage .= ' | Предыдущее исключение: ' . $e->getPrevious()->getMessage();
                }
    
                Log::error($logMessage, [
                    'data' => $data
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
        $context['value'] = "Invalid time format: {$invalidTime}";
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
        $context['value'] = "Invalid audience: {$audience}";
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
        $context['value'] = 'Invalid class data format';
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
            'day' => $context['day'] ?? 'unknown_day',
            'week' => $context['week'] ?? 'unknown_week',
            'class' => $context['class'] ?? null,
            'value' => $context['value'] ?? "unexpected_error",
            'teacher' => $context['teacher'] ?? null,
            'classroom' => $context['classroom'] ?? null,
            'discipline' => $context['discipline'] ?? null,
            //ДА еБАННЫЙ РОТ ЭТОЙ ПИЗДЫ БЛЯТЬ
        ];
    }
}
