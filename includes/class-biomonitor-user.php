<?php

class BioMonitorUser {
    public static function set_user_type($user_id, $user_type) {
        return BioMonitorDB::set_user_type($user_id, $user_type);
    }

    public static function get_user_type($user_id) {
        return BioMonitorDB::get_user_type($user_id);
    }

    public static function is_doctor($user_id) {
        $user_type = self::get_user_type($user_id);
        return $user_type === 'doctor';
    }

    public static function is_patient($user_id) {
        return self::get_user_type($user_id) === 'patient';
    }

    public static function get_patients() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'user_types';
        $query = "SELECT u.ID, u.user_login FROM {$wpdb->users} u 
                  JOIN {$table_name} ut ON u.ID = ut.user_id 
                  WHERE ut.user_type = 'patient'";
        return $wpdb->get_results($query);
    }
}
