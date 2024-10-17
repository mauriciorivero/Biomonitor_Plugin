<?php

class BioMonitorDB {
    public static function get_user_type($user_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'user_types';
        $query = $wpdb->prepare("SELECT user_type FROM $table_name WHERE user_id = %d", $user_id);
        return $wpdb->get_var($query);
    }

    public static function set_user_type($user_id, $user_type) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'user_types';
        return $wpdb->replace(
            $table_name,
            array(
                'user_id' => $user_id,
                'user_type' => $user_type
            ),
            array('%d', '%s')
        );
    }

    public static function get_patient_vital_signs($patient_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'vital_signs';
        $query = $wpdb->prepare("SELECT * FROM $table_name WHERE patient_id = %d ORDER BY reading_timestamp DESC", $patient_id);
        return $wpdb->get_results($query);
    }

    public static function get_patient_diagnoses($patient_id) {
        global $wpdb;
        $vital_signs_table = $wpdb->prefix . 'vital_signs';
        $diagnoses_table = $wpdb->prefix . 'diagnoses';
        $query = $wpdb->prepare(
            "SELECT d.*, v.oximetry, v.heart_rate, v.reading_timestamp 
            FROM $diagnoses_table d 
            JOIN $vital_signs_table v ON d.vital_sign_id = v.id 
            WHERE v.patient_id = %d 
            ORDER BY d.created_at DESC",
            $patient_id
        );
        return $wpdb->get_results($query);
    }

    public static function add_diagnosis($vital_sign_id, $doctor_id, $diagnosis) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'diagnoses';
        
        $result = $wpdb->insert(
            $table_name,
            array(
                'vital_sign_id' => $vital_sign_id,
                'doctor_id' => $doctor_id,
                'diagnosis' => $diagnosis,
                'created_at' => current_time('mysql')
            ),
            array('%d', '%d', '%s', '%s')
        );

        return $result !== false;
    }
}
