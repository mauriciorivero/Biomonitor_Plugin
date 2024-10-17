<?php

class BioMonitorAPI {
    public function register_routes() {
        register_rest_route('biomonitor/v1', '/vital-signs/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_vital_signs'),
            'permission_callback' => array($this, 'check_permission')
        ));

        register_rest_route('biomonitor/v1', '/diagnoses/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_diagnoses'),
            'permission_callback' => array($this, 'check_permission')
        ));

        register_rest_route('biomonitor/v1', '/diagnoses', array(
            'methods' => 'POST',
            'callback' => array($this, 'add_diagnosis'),
            'permission_callback' => array($this, 'check_doctor_permission')
        ));
    }

    public function check_permission($request) {
        return is_user_logged_in();
    }

    public function check_doctor_permission($request) {
        $user_id = get_current_user_id();
        return BioMonitorUser::is_doctor($user_id);
    }

    public function get_vital_signs($request) {
        $patient_id = $request['id'];
        $vital_signs = BioMonitorDB::get_patient_vital_signs($patient_id);
        return new WP_REST_Response($vital_signs, 200);
    }

    public function get_diagnoses($request) {
        $patient_id = $request['id'];
        $diagnoses = BioMonitorDB::get_patient_diagnoses($patient_id);
        return new WP_REST_Response($diagnoses, 200);
    }

    public function add_diagnosis($request) {
        $vital_sign_id = $request->get_param('vital_sign_id');
        $doctor_id = get_current_user_id();
        $diagnosis = $request->get_param('diagnosis');

        $result = BioMonitorDB::add_diagnosis($vital_sign_id, $doctor_id, $diagnosis);

        if ($result) {
            return new WP_REST_Response(array('message' => 'Diagnosis added successfully'), 201);
        } else {
            return new WP_Error('cant-add', 'Failed to add diagnosis', array('status' => 500));
        }
    }
}
