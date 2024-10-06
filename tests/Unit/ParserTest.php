<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_validatePregMatchDayAndWeek()
    {
        $weekExamples = ['пн (над чертой)', 'пн
        над
        чертой',
            'пт над чертой',
            'сб (над чертой) ',
        ];
        $matches = [];
        $days = ['пн', 'вт', 'ср', 'чт', 'пт', 'сб', 'вс'];
        $weekTypes = ['над', 'под'];
        foreach ($weekExamples as $weekText) {
            preg_match('/(\w+)\s+\(?(\w+)\s*\)?/ui', $weekText, $matches);
            list($str, $day, $week) = $matches;
            print_r([$day, $week]);            
            $this->assertTrue(in_array($day, $days));
            $this->assertTrue(in_array($week, $weekTypes));
        }
    }
}
