jQuery(document).ready(function($) {
    const doctorDashboard = $('#biomonitor-doctor-dashboard');
    const patientDashboard = $('#biomonitor-patient-dashboard');

    if (doctorDashboard.length) {
        loadPatientList();
    }

    if (patientDashboard.length) {
        const patientId = patientDashboard.data('patient-id');
        loadPatientVitalSigns(patientId);
        loadPatientDiagnoses(patientId);
    }

    function loadPatientList() {
        $.ajax({
            url: biomonitor_ajax.ajax_url,
            method: 'GET',
            data: {
                action: 'get_patients'
            },
            success: function(response) {
                const patientSelect = doctorDashboard.find('#patient-select');
                patientSelect.empty().append('<option value="">Select a patient</option>');
                response.forEach(function(patient) {
                    patientSelect.append(`<option value="${patient.ID}">${patient.user_login}</option>`);
                });
            }
        });
    }

    doctorDashboard.on('change', '#patient-select', function() {
        const patientId = $(this).val();
        if (patientId) {
            loadPatientVitalSigns(patientId);
            $('#patient-details').show();
        } else {
            $('#patient-details').hide();
        }
    });

    function loadPatientVitalSigns(patientId) {
        $.ajax({
            url: biomonitor_ajax.ajax_url,
            method: 'GET',
            data: {
                action: 'get_vital_signs',
                patient_id: patientId
            },
            success: function(response) {
                const vitalSigns = doctorDashboard.length ? doctorDashboard.find('#vital-signs') : patientDashboard.find('#vital-signs tbody');
                vitalSigns.empty();
                response.forEach(function(reading) {
                    if (doctorDashboard.length) {
                        vitalSigns.append(`
                            <div class="vital-sign" data-id="${reading.id}">
                                <p>Date: ${reading.reading_timestamp}</p>
                                <p>Oximetry: ${reading.oximetry}%</p>
                                <p>Heart Rate: ${reading.heart_rate} bpm</p>
                                <button class="select-vital-sign">Select for Diagnosis</button>
                            </div>
                        `);
                    } else {
                        vitalSigns.append(`
                            <tr>
                                <td>${reading.reading_timestamp}</td>
                                <td>${reading.oximetry}%</td>
                                <td>${reading.heart_rate} bpm</td>
                            </tr>
                        `);
                    }
                });
            }
        });
    }

    function loadPatientDiagnoses(patientId) {
        $.ajax({
            url: biomonitor_ajax.ajax_url,
            method: 'GET',
            data: {
                action: 'get_diagnoses',
                patient_id: patientId
            },
            success: function(response) {
                const diagnosesList = patientDashboard.find('#diagnoses ul');
                diagnosesList.empty();
                response.forEach(function(diagnosis) {
                    diagnosesList.append(`
                        <li>
                            <p><strong>Date:</strong> ${diagnosis.created_at}</p>
                            <p><strong>Diagnosis:</strong> ${diagnosis.diagnosis}</p>
                        </li>
                    `);
                });
            }
        });
    }

    doctorDashboard.on('click', '.vital-sign', function() {
        const vitalSignId = $(this).data('id');
        $('#vital-sign-id').val(vitalSignId);
    });

    doctorDashboard.on('click', '.select-vital-sign', function() {
        const vitalSignId = $(this).closest('.vital-sign').data('id');
        $('#vital-sign-id').val(vitalSignId);
        alert('Vital sign selected for diagnosis');
    });

    $('#add-diagnosis-form').on('submit', function(e) {
        e.preventDefault();
        const vitalSignId = $('#vital-sign-id').val();
        const diagnosis = $('#diagnosis').val();
        const patientId = $('#patient-select').val();

        if (!vitalSignId) {
            alert('Please select a vital sign reading first.');
            return;
        }

        $.ajax({
            url: biomonitor_ajax.ajax_url,
            method: 'POST',
            data: {
                action: 'add_diagnosis',
                vital_sign_id: vitalSignId,
                diagnosis: diagnosis,
                patient_id: patientId
            },
            success: function(response) {
                if (response.success) {
                    alert('Diagnosis added successfully');
                    $('#diagnosis').val('');
                    loadPatientVitalSigns(patientId);
                } else {
                    alert('Error adding diagnosis: ' + response.data);
                }
            },
            error: function() {
                alert('Error adding diagnosis');
            }
        });
    });
});
