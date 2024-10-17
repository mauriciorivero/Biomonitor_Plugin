<?php
/**
 * Plugin Name: BioMonitor
 * Plugin URI: http://mrd2.co/biomonitor
 * Description: A plugin to monitor and manage patient biometric data
 * Version: 1.0
 * Author: Mauricio Rivero
 * Author URI: http://mrd2.co
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class BioMonitor {
    private static $instance = null;

    private function __construct() {
        $this->init();
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new BioMonitor();
        }
        return self::$instance;
    }

    private function init() {
        $this->load_dependencies(); // Move this line to the top
        add_action('init', array($this, 'register_shortcodes'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('rest_api_init', array($this, 'register_rest_routes'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('wp_ajax_get_patients', array($this, 'ajax_get_patients'));
        add_action('wp_ajax_get_vital_signs', array($this, 'ajax_get_vital_signs'));
        add_action('wp_ajax_get_diagnoses', array($this, 'ajax_get_diagnoses'));
        add_action('wp_ajax_add_diagnosis', array($this, 'ajax_add_diagnosis'));
        add_filter('wp_nav_menu_items', array($this, 'add_biomonitor_menu_item'), 10, 2);
    }

    public function load_dependencies() {
        require_once plugin_dir_path(__FILE__) . 'includes/class-biomonitor-db.php';
        require_once plugin_dir_path(__FILE__) . 'includes/class-biomonitor-api.php';
        require_once plugin_dir_path(__FILE__) . 'includes/class-biomonitor-frontend.php';
        require_once plugin_dir_path(__FILE__) . 'includes/class-biomonitor-user.php';
    }

    public function register_shortcodes() {
        add_shortcode('biomonitor_dashboard', array($this, 'render_dashboard'));
    }

    public function render_dashboard() {
        $user_id = get_current_user_id();
        $user_type = BioMonitorUser::get_user_type($user_id);

        if ($user_type === 'doctor') {
            return BioMonitorFrontend::render_doctor_dashboard();
        } elseif ($user_type === 'patient') {
            return BioMonitorFrontend::render_patient_dashboard($user_id);
        } else {
            return 'You do not have permission to view this dashboard. Please contact an administrator to set your user type.';
        }
    }

    public function enqueue_scripts() {
        wp_enqueue_script('biomonitor-js', plugin_dir_url(__FILE__) . 'assets/js/biomonitor.js', array('jquery'), '1.0', true);
        wp_localize_script('biomonitor-js', 'biomonitor_ajax', array('ajax_url' => admin_url('admin-ajax.php')));
        wp_enqueue_style('biomonitor-css', plugin_dir_url(__FILE__) . 'assets/css/biomonitor.css');
    }

    public function register_rest_routes() {
        $api = new BioMonitorAPI();
        $api->register_routes();
    }

    public function add_admin_menu() {
        add_menu_page(
            'BioMonitor Settings',
            'BioMonitor',
            'manage_options',
            'biomonitor-settings',
            array($this, 'render_settings_page'),
            'dashicons-heart',
            30
        );
    }

    public function render_settings_page() {
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id']) && isset($_POST['user_type'])) {
            $user_id = intval($_POST['user_id']);
            $user_type = sanitize_text_field($_POST['user_type']);
            BioMonitorUser::set_user_type($user_id, $user_type);
            echo '<div class="updated"><p>User type updated successfully.</p></div>';
        }

        // Render the form
        ?>
        <div class="wrap">
            <h1>BioMonitor Settings</h1>
            <form method="post">
                <table class="form-table">
                    <tr>
                        <th><label for="user_id">User</label></th>
                        <td>
                            <select name="user_id" id="user_id">
                                <?php
                                $users = get_users();
                                foreach ($users as $user) {
                                    echo '<option value="' . $user->ID . '">' . $user->user_login . '</option>';
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="user_type">User Type</label></th>
                        <td>
                            <select name="user_type" id="user_type">
                                <option value="doctor">Doctor</option>
                                <option value="patient">Patient</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <?php submit_button('Set User Type'); ?>
            </form>
        </div>
        <?php
    }

    public function ajax_get_patients() {
        $patients = BioMonitorUser::get_patients();
        wp_send_json($patients);
    }

    public function ajax_get_vital_signs() {
        $patient_id = intval($_GET['patient_id']);
        $vital_signs = BioMonitorDB::get_patient_vital_signs($patient_id);
        wp_send_json($vital_signs);
    }

    public function ajax_get_diagnoses() {
        $patient_id = intval($_GET['patient_id']);
        $diagnoses = BioMonitorDB::get_patient_diagnoses($patient_id);
        wp_send_json($diagnoses);
    }

    public function ajax_add_diagnosis() {
        // Remove the capability check, as doctors might not have 'edit_posts' capability
        // if (!current_user_can('edit_posts')) {
        //     wp_send_json_error('Permission denied');
        //     return;
        // }

        $vital_sign_id = intval($_POST['vital_sign_id']);
        $diagnosis = sanitize_textarea_field($_POST['diagnosis']);
        $doctor_id = get_current_user_id();

        // Check if the user is a doctor
        if (!BioMonitorUser::is_doctor($doctor_id)) {
            wp_send_json_error('Permission denied: User is not a doctor');
            return;
        }

        $result = BioMonitorDB::add_diagnosis($vital_sign_id, $doctor_id, $diagnosis);

        if ($result) {
            wp_send_json_success('Diagnosis added successfully');
        } else {
            wp_send_json_error('Error adding diagnosis');
        }
    }

    public function create_dashboard_page() {
        $page_title = 'BioMonitor Dashboard';
        $page_content = '[biomonitor_dashboard]';
        $page_check = get_page_by_title($page_title);

        if (!$page_check) {
            $page = array(
                'post_type' => 'page',
                'post_title' => $page_title,
                'post_content' => $page_content,
                'post_status' => 'publish',
                'post_author' => 1,
            );
            wp_insert_post($page);
        }
    }

    public static function plugin_activation() {
        $instance = self::getInstance();
        $instance->create_dashboard_page();
    }

    public function add_biomonitor_menu_item($items, $args) {
        if (is_user_logged_in()) {
            $page = get_page_by_title('BioMonitor Dashboard');
            if ($page) {
                $items .= $this->add_menu_item('BioMonitor', get_permalink($page->ID));
            }
        }
        return $items;
    }

    private function add_menu_item($title, $url, $restrict = false, $permission = '') {
        $menu_items = '';
        $menu_items .= '<li class="menu-item">';
        $menu_items .= '<a href="' . $url . '">' . $title . '</a>';
        $menu_items .= '</li>';
        return $menu_items;
    }
}

// Initialize the plugin
add_action('plugins_loaded', array('BioMonitor', 'getInstance'));
register_activation_hook(__FILE__, array('BioMonitor', 'plugin_activation'));
