<?php
/*by Pearllsss 31012025*/

defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        #load all that you need
        $this->load->library("Aauth");
        $this->load->model('AuthModel');
        $this->load->helper('captcha');
        $this->load->library('NotifEmail');
        $this->load->model('User_model');
    }

    public function csrf()
    {
        $name = $this->security->get_csrf_token_name();
        $hash = $this->security->get_csrf_hash();
        $html = '<input type="hidden" name="' . $name . '" value="' . $hash . '">';
        return $html;
    }

    public function csrf_ajax()
    {
        return array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );
    }

    private function enable_captcha()
    {
        return $this->config->item('ci_captcha');
    }

    //LOGIN MODULE
    // =========================================================================================================================================
    public function index()
    {
        if ($this->session->logged_in) {
            redirect('PurchaseRequest');
        }

        $aModel = new AuthModel();
        if ($this->enable_captcha()) {
            $path = './captcha-images/';

            if (!is_dir($path)) {
                mkdir($path);
            }

            $vals = array(
                'img_path'      => './captcha-images/',
                'img_url'       => base_url("/captcha-images/"),
                'font_path'     => '../system/fonts/Roboto-Black.ttf',
                'img_width'     => '300',
                'img_height'    => 40,
                'expiration'    => 30,
                'word_length'   => 6,
                'font_size'     => 20,
                'img_id'        => 'Imageid',
                'pool'          => '123456789ABCDEFGHIJKLMNPQRSTUVWXYZ',
                'colors'        => array(
                    'background' => array(255, 255, 255),
                    'border' => array(255, 255, 255),
                    'text' => array(0, 0, 0),
                    'grid' => array(134, 195, 235)
                )
            );

            $cap = create_captcha($vals);
            $captcha_image = $cap['image'];
            $captcha_word = $cap['word'];
            $data['captcha_image'] = $captcha_image;
        }

        $data['csrf'] = $this->csrf();
        $data['enable_captcha'] = $this->enable_captcha();

        $this->form_validation->set_rules('email', 'Email', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if (!$this->form_validation->run()) {
            $this->load->view('login', $data);
            if ($this->enable_captcha()) {
                $this->session->unset_userdata('captchaCode');
                $this->session->set_userdata('captchaCode', $captcha_word);
            }
        } else {
            $captcha = $this->input->post('captcha');
            $validCaptcha = 1;

            if ($this->enable_captcha()) {
                if ($captcha != $this->session->captchaCode) {
                    $this->session->set_flashdata('fail', 'Captcha does not match.');
                    redirect('User/index');
                }
            }

            $email = $this->input->post('email');
            $password = $this->input->post('password');

            // Fetch user first
            $userDetails = $aModel->get_active_user_by_email($email);

            if (!$userDetails) {
                $this->session->set_flashdata('fail', 'Invalid email or password.');
                redirect('User/index');
            }

            // Check if email is verified
            if ($userDetails->email_verify != 1) {
                $data['fullname'] = $userDetails->fullname; // optional
                $this->load->view('errors/restricted_access', $data);
                return; // stop further execution
            }

            // Now attempt login
            $login = $this->aauth->login($email, $password);

            if (!$login) {
                $this->session->set_flashdata('fail', 'Invalid email or password.');
                redirect('User/index');
            }

            // If login successful, proceed with session setup
            $userGroup = $aModel->getAllGroupsByUID($userDetails->id);

            // Set flags
            $is_procurement = 0;
            $is_finance = 0;
            $is_hr = 0;
            $is_misd = 0;
            $is_admin_office = 0;

            foreach ($userGroup as $u) {

                switch ($u->group_id) {

                    case 11:
                        $is_procurement = 1;
                        break;

                    case 12:
                        $is_finance = 1;
                        break;

                    case 13:
                        $is_hr = 1;
                        break;

                    case 14:
                        $is_misd = 1;
                        break;

                    case 15:
                        $is_admin_office = 1;
                        break;
                }
            }

            // set session
            $this->session->set_userdata([
                'userID'        => $userDetails->id,
                'fullname'      => $userDetails->fullname,
                'email'         => $userDetails->email,
                'office'        => $userDetails->office,
                'logged_in'     => TRUE,
                'user_group'    => $userGroup,
                'doctor_id'     => $userDetails->doctor_id,
                'ui'            => $userDetails->ui,

                'is_procurement' => $is_procurement,
                'is_finance'     => $is_finance,
                'is_hr'          => $is_hr,
                'is_misd'        => $is_misd,
                'is_admin_office' => $is_admin_office
            ]);

            // Redirect based on role
            if ($is_procurement || $is_finance || $is_hr) {
                redirect('PurchaseRequest');
            } elseif ($is_misd) {
                redirect('PurchaseRequest');
            } elseif ($is_admin_office) {
                redirect('PurchaseRequest');
            } else {
                redirect('User/index');
            }
        }
    }

    // REGISTRATION MODULE
    // =========================================================================================================================================
    public function register()
    {
        if ($this->enable_captcha()) {
            $path = './captcha-images/';
            if (!is_dir($path)) {
                mkdir($path);
            }

            $vals = array(
                'img_path'      => './captcha-images/',
                'img_url'       => base_url('captcha-images/'),
                'font_path'     => './system/fonts/Roboto-Black.ttf',
                'img_width'     => '300',
                'img_height'    => 40,
                'expiration'    => 30,
                'word_length'   => 6,
                'font_size'     => 20,
                'img_id'        => 'Imageid',
                'pool'          => '123456789ABCDEFGHIJKLMNPQRSTUVWXYZ',
                'colors'        => array(
                    'background' => array(255, 255, 255),
                    'border' => array(255, 255, 255),
                    'text' => array(0, 0, 0),
                    'grid' => array(134, 195, 235)
                )
            );

            $cap = create_captcha($vals);
            $captcha_image = $cap['image'];
            $captcha_word = $cap['word'];
            $this->session->set_userdata('captchaCode', $captcha_word);
            $data['captcha_image'] = $captcha_image;
        }

        $this->load->model('User_model');
        $data['offices'] = $this->User_model->get_offices(); // ✅ Load office list
        $data['wards'] = [];

        $data['csrf'] = $this->csrf();
        $data['csrf_ajax'] = $this->csrf_ajax();
        $data['enable_captcha'] = $this->enable_captcha();

        $this->load->view('register', $data);
    }


    // public function getWardsByOffice()
    // {
    //     $office_id = $this->input->post('office_id');

    //     if (!$office_id) {
    //         echo json_encode([]);
    //         return;
    //     }

    //     $this->load->model('User_model');
    //     $wards = $this->User_model->get_wards_by_office($office_id);

    //     echo json_encode($wards);
    // }

    // SAVE REGISTRATION
    // =========================================================================================================================================
    public function saveRegistration()
    {
        log_message('debug', '=== REGISTRATION START ===');
        log_message('debug', 'POST Data: ' . print_r($this->input->post(), true));

        // Check if form is actually being submitted
        if (!$this->input->post()) {
            log_message('debug', 'No POST data received');
            $this->session->set_flashdata('fail', 'No form data received.');
            redirect('User/register');
            return;
        }

        $this->load->library('form_validation');

        // Set validation rules
        $this->form_validation->set_rules('fullname', 'Full Name', 'required|trim');
        $this->form_validation->set_rules('phone_number', 'Phone Number', 'required|trim');
        $this->form_validation->set_rules('office', 'Office', 'required|integer');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('confirm_pass', 'Confirm Password', 'required|matches[password]');

        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            log_message('debug', 'Validation failed: ' . $errors);
            $this->session->set_flashdata('fail', $errors);
            $this->session->set_flashdata('old_input', $this->input->post());
            redirect('User/register');
            return;
        }

        log_message('debug', 'Form validation passed');

        // Check captcha
        $captcha = $this->input->post('captcha');
        log_message('debug', 'Captcha input: ' . $captcha);
        log_message('debug', 'Session captcha: ' . $this->session->captchaCode);

        if ($captcha !== $this->session->captchaCode) {
            log_message('debug', 'Captcha mismatch');
            $this->session->set_flashdata('fail', 'Captcha does not match.');
            $this->session->set_flashdata('old_input', $this->input->post());
            redirect('User/register');
            return;
        }

        log_message('debug', 'Captcha validation passed');

        // Prepare data
        $data = [
            'fullname'      => html_escape($this->input->post('fullname')),
            'phone_number'  => html_escape($this->input->post('phone_number')),
            'office'        => (int) $this->input->post('office'),
            // 'ward'          => $this->input->post('ward') ? (int) $this->input->post('ward') : null,
            'email'         => html_escape($this->input->post('email')),
            'password'      => $this->input->post('password')
        ];

        log_message('debug', 'Prepared data for user creation: ' . print_r($data, true));

        try {
            // Create user
            $user_data = $this->aauth->create_user(
                $data['email'],
                $data['password'],
                $data['fullname'],
                $data['phone_number'],
                $data['office'],
                // $data['ward'],
                null
            );

            if ($user_data) {
                log_message('debug', 'User created successfully: ' . print_r($user_data, true));

                $token = time();
                $aModel = new AuthModel();
                $aModel->insertTokenEmail($token, $data['email']);

                // Email notification code here...

                $this->session->set_flashdata('success', 'Registration successful! Please check your email for verification.');
                redirect('User/index');
            } else {
                $errors = $this->aauth->print_errors();
                log_message('debug', 'AAuth user creation failed: ' . $errors);
                $this->session->set_flashdata('fail', $errors);
                $this->session->set_flashdata('old_input', $data);
                redirect('User/register');
            }
        } catch (Exception $e) {
            log_message('error', 'Registration exception: ' . $e->getMessage());
            $this->session->set_flashdata('fail', 'System error: ' . $e->getMessage());
            $this->session->set_flashdata('old_input', $data);
            redirect('User/register');
        }
    }

    // USER PROFILE
    // =========================================================================================================================================
    public function profile()
    {
        if (!$this->session->logged_in) {
            redirect('User/index');
        }

        $user_id = $this->session->userID;
        $this->load->model('User_model');
        $data['user'] = $this->User_model->get_user($user_id);
        $data['csrf'] = $this->csrf();

        $this->load->view('profile', $data);
    }

    // UPDATE USER PROFILE
    // =========================================================================================================================================
    public function update_profile()
    {
        if (!$this->session->logged_in) {
            redirect('User/index');
        }

        $user_id = $this->session->userID;
        $data = array(
            'fullname' => $this->input->post('fullname'),
            'email' => $this->input->post('email'),
            'phone_number' => $this->input->post('phone_number'),
            'office' => $this->input->post('office')
        );

        if ($this->User_model->edit($user_id, $data)) {
            $this->session->set_flashdata('success', 'Profile updated successfully.');
        } else {
            $this->session->set_flashdata('fail', 'Failed to update profile.');
        }

        redirect('User/profile');
    }

    //LOGOUT
    // =========================================================================================================================================
    public function logout()
    {
        session_destroy();
        redirect('User/index');
    }

    // LOGOUT SESSION
    // =========================================================================================================================================
    public function logout_sess()
    {
        $this->session->set_flashdata('success_logout', 'Session expired!');
        $this->load->view('logout');
    }

    // VERIFY EMAIL
    // =========================================================================================================================================
    public function verify_email($token)
    {
        $aModel = new AuthModel();

        $count = $aModel->check_exist_token_email($token);
        $get_minutes = $aModel->check_date_start_token($token);
        $minutes_now = date("i");
        $expired = $minutes_now - date("i", strtotime($get_minutes->token_date_start)); // 5minutes

        if ($count <= 0) {
            $this->session->set_flashdata('fail', 'Email verification token is invalid. Please coordinate with MISD.');
        } else {
            if ($expired > 5) {
                $this->session->set_flashdata('fail', 'Email verification process expired. Please coordinate with MISD');
            } else {
                $updateStat = $aModel->update_email_verify($token);
                if ($updateStat > 0) {
                    $this->session->set_flashdata('success', 'Email verification successful. Please proceed with Login.');
                } else {
                    $this->session->set_flashdata('fail', 'Email verification failed. If problems persist contact the MISD');
                }
            }
        }
        redirect('User/index');
    }

    //RESTRICT ACCESS
    // =========================================================================================================================================
    public function restricted_access()
    {
        $this->load->view('errors/restricted_access');
    }

    // public function changeUIPreference()
    // {
    //     if (!$this->session->logged_in) {
    //         echo json_encode(['success' => false, 'message' => 'Not logged in']);
    //         return;
    //     }
    //     $u_model = new User_model();
    //     $newUI = $this->input->post('ui');
    //     $user_id = $this->session->userID;
    //     $updateData = array('ui' => $newUI);

    //     if ($u_model->edit($user_id, $updateData)) {
    //         // Update session data
    //         $this->session->set_userdata('ui', $newUI);
    //         echo json_encode(['success' => true]);
    //     } else {
    //         echo json_encode(['success' => false, 'message' => 'Failed to update UI preference']);
    //     }
    // }

    // VALIDATE USER FULL NAME
    // =========================================================================================================================================
    public function validateFullname()
    {
        $fullname = $this->input->post('fullname');

        // Check if fullname exists in database
        $this->db->where('fullname', $fullname);
        $this->db->where('DELETED', 0);
        $query = $this->db->get('aauth_users');

        $exists = $query->num_rows() > 0;

        // Return simple true/false
        echo $exists ? 'false' : 'true';
        exit;
    }

    //VALIDATE OFFICE
    // =========================================================================================================================================
    public function validateOffice()
    {
        $office_id = $this->input->post('office_id');

        // First, check if the office exists in lib_office
        $this->db->where('office_id', $office_id);
        $office_query = $this->db->get('lib_office');

        if ($office_query->num_rows() === 0) {
            echo 'false'; // Office doesn't exist in lib_office table
            exit;
        }

        // Office exists - allow multiple users from same office
        echo 'true';
        exit;
    }

    // VALIDATE PHONE NUMBER
    // =========================================================================================================================================
    public function validatePhone()
    {
        $phone_number = $this->input->post('phone_number');

        // Check if phone number exists in database
        $this->db->where('phone_number', $phone_number);
        $this->db->where('DELETED', 0);
        $query = $this->db->get('aauth_users');

        $exists = $query->num_rows() > 0;

        echo $exists ? 'false' : 'true';
        exit;
    }

    // VALIDATE EMAIL
    // =========================================================================================================================================
    public function validateEmail()
    {
        $email = $this->input->post('email');

        // Check if email exists in database
        $this->db->where('email', $email);
        $this->db->where('DELETED', 0);
        $query = $this->db->get('aauth_users');

        $exists = $query->num_rows() > 0;

        echo $exists ? 'false' : 'true';
        exit;
    }

    // public function getNursesJSON()
    // {
    //     $search = $this->input->get('search');

    //     $this->db->select('u.id, u.fullname');
    //     $this->db->from('aauth_users u');
    //     $this->db->join('aauth_user_to_group ug', 'ug.user_id = u.id', 'inner');
    //     $this->db->where('ug.group_id', 6); // nurses

    //     if ($search) {
    //         $this->db->like('u.fullname', $search);
    //     }

    //     $this->db->order_by('u.fullname', 'ASC');
    //     $query = $this->db->get();

    //     echo json_encode(['success' => true, 'data' => $query->result()]);
    // }
}
