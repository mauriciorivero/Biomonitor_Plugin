<?php

class BioMonitorFrontend {
    public static function render_doctor_dashboard() {
        ob_start();
        ?>
        <div id="biomonitor-doctor-dashboard">
            <h2>Doctor Dashboard</h2>
            <div id="patient-list">
                <h3>Patients</h3>
                <select id="patient-select">
                    <option value="">Select a patient</option>
                </select>
            </div>
            <div id="patient-details" style="display: none;">
                <h3>Patient Details</h3>
                <div id="vital-signs"></div>
                <div id="diagnosis-form">
                    <h4>Add Diagnosis</h4>
                    <form id="add-diagnosis-form">
                        <input type="hidden" id="vital-sign-id" name="vital_sign_id">
                        <textarea id="diagnosis" name="diagnosis" required></textarea>
                        <button type="submit">Submit Diagnosis</button>
                    </form>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public static function render_patient_dashboard($patient_id) {
        ob_start();
        ?>
        <div id="biomonitor-patient-dashboard" data-patient-id="<?php echo esc_attr($patient_id); ?>">
            <h2>Patient Dashboard</h2>
            <div id="vital-signs">
                <h3>Your Vital Signs</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Oximetry</th>
                            <th>Heart Rate</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div id="diagnoses">
                <h3>Your Diagnoses</h3>
                <ul></ul>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
