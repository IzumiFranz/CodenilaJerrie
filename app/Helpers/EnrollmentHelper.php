<?php

namespace App\Helpers;

class EnrollmentHelper
{
    /**
     * Get the current academic year in format "YYYY-YYYY+1"
     * Example: "2024-2025"
     */
    public static function getCurrentAcademicYear(): string
    {
        return now()->format('Y') . '-' . (now()->year + 1);
    }

    /**
     * Get the current semester based on current month
     * 
     * @return string '1st', '2nd', or 'summer'
     */
    public static function getCurrentSemester(): string
    {
        $month = now()->month;
        
        // 1st Semester: June (6) to October (10)
        if ($month >= 6 && $month <= 10) {
            return '1st';
        }
        
        // 2nd Semester: November (11) to March (3)
        if ($month >= 11 || $month <= 3) {
            return '2nd';
        }
        
        // Summer: April (4) to May (5)
        return 'summer';
    }

    /**
     * Get academic years list (current -1 to current +2)
     * 
     * @return array
     */
    public static function getAcademicYears(): array
    {
        $years = [];
        $currentYear = now()->year;
        
        for ($i = -1; $i <= 2; $i++) {
            $year = $currentYear + $i;
            $years[] = $year . '-' . ($year + 1);
        }
        
        return $years;
    }
}

