<?php

if (!function_exists('ordinal_suffix')) {
    /**
     * Get ordinal suffix for a number (1st, 2nd, 3rd, 4th, etc.)
     *
     * @param int $number
     * @return string
     */
    function ordinal_suffix($number)
    {
        $ends = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
        
        if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
            return 'th';
        }
        
        return $ends[$number % 10];
    }
}

if (!function_exists('getCurrentSemester')) {
    /**
     * Get current semester based on current month
     *
     * @return string
     */
    function getCurrentSemester()
    {
        $month = now()->month;
        
        if ($month >= 6 && $month <= 10) {
            return '1st';
        }
        
        if ($month >= 11 || $month <= 3) {
            return '2nd';
        }
        
        return 'summer';
    }
}

