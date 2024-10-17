# BioMonitor WordPress Plugin

## Overview
BioMonitor is a WordPress plugin designed to help doctors monitor and manage patient biometric data, specifically oximetry and heart rate. It provides separate dashboards for doctors and patients, allowing doctors to view patient data and add diagnoses, while patients can view their own vital signs and diagnoses.

## File Structure
- `biomonitor.php`: Main plugin file, initializes the plugin and sets up WordPress hooks
- `includes/`
  - `class-biomonitor-db.php`: Handles database operations
  - `class-biomonitor-api.php`: Manages REST API functionality
  - `class-biomonitor-frontend.php`: Renders frontend views
  - `class-biomonitor-user.php`: Manages user roles and permissions
- `assets/`
  - `js/biomonitor.js`: Frontend JavaScript for dashboard functionality
  - `css/biomonitor.css`: Styles for the plugin

## Key Components

### BioMonitor Class (`biomonitor.php`)
- `getInstance()`: Implements Singleton pattern
- `init()`: Sets up WordPress hooks
- `load_dependencies()`: Loads required PHP files
- `register_shortcodes()`: Registers the `[biomonitor_dashboard]` shortcode
- `render_dashboard()`: Renders the appropriate dashboard based on user role
- `enqueue_scripts()`: Enqueues necessary JavaScript and CSS files
- `add_admin_menu()`: Adds the BioMonitor settings page to the WordPress admin menu
- `ajax_*` methods: Handle AJAX requests for various dashboard functions

### BioMonitorDB Class (`class-biomonitor-db.php`)
- `get_user_type()`: Retrieves the user type (doctor/patient)
- `set_user_type()`: Sets the user type for a given user
- `get_patient_vital_signs()`: Retrieves vital signs for a patient
- `get_patient_diagnoses()`: Retrieves diagnoses for a patient
- `add_diagnosis()`: Adds a new diagnosis for a patient

### BioMonitorAPI Class (`class-biomonitor-api.php`)
- `register_routes()`: Registers REST API routes
- `get_vital_signs()`: API endpoint for retrieving vital signs
- `get_diagnoses()`: API endpoint for retrieving diagnoses
- `add_diagnosis()`: API endpoint for adding a new diagnosis

### BioMonitorFrontend Class (`class-biomonitor-frontend.php`)
- `render_doctor_dashboard()`: Renders the doctor's dashboard HTML
- `render_patient_dashboard()`: Renders the patient's dashboard HTML

### BioMonitorUser Class (`class-biomonitor-user.php`)
- `get_user_type()`: Retrieves the user type
- `set_user_type()`: Sets the user type
- `is_doctor()`: Checks if a user is a doctor
- `is_patient()`: Checks if a user is a patient
- `get_patients()`: Retrieves a list of all patients

### Frontend JavaScript (`biomonitor.js`)
- Handles dynamic loading of patient lists, vital signs, and diagnoses
- Manages form submission for adding new diagnoses
- Updates the dashboard in real-time based on user interactions

## Database Tables
The plugin uses custom database tables:
- `bio_user_types`: Stores user types (doctor/patient)
- `bio_vital_signs`: Stores patient vital sign readings
- `bio_diagnoses`: Stores diagnoses made by doctors

## Usage
1. Install and activate the plugin
2. Use the WordPress admin panel to set user types (doctor/patient)
3. Add the `[biomonitor_dashboard]` shortcode to a page to display the dashboard
4. Doctors can view patient data and add diagnoses
5. Patients can view their own vital signs and diagnoses

