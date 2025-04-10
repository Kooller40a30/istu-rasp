<?php

namespace App\Services;

use App\Models\Group;
use App\Models\GroupSchedule;
use App\Models\Schedule;
use Illuminate\Support\Facades\Log;

class GroupScheduleService 
{
    public static function addGroupSchedule(Group $group, Schedule $schedule): GroupSchedule
    {
        Log::info('[GroupScheduleService] Action: Adding group schedule', ['group_id' => $group['id'], 'schedule_id' => $schedule['id']]);
        $groupSchedule = GroupSchedule::firstOrCreate([
            'group_id' => $group['id'],
            'schedule_id' => $schedule['id'],
        ]);
        Log::info('[GroupScheduleService] Action: Group schedule added', ['group_schedule_id' => $groupSchedule->id]);
        return $groupSchedule;
    }
}