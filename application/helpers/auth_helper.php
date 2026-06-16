<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('auth_user_id')) {
    function auth_user_id()
    {
        $CI =& get_instance();
        return $CI->session->userdata('id');
    }
}

if (!function_exists('is_logged_in')) {
    function is_logged_in()
    {
        $CI =& get_instance();
        return $CI->aauth->is_loggedin();
    }
}

if (!function_exists('has_group')) {
    function has_group($group_id, $user_id = false)
    {
        $CI =& get_instance();

        if (!$user_id) {
            $user_id = auth_user_id();
        }

        return $CI->aauth->is_member($group_id, $user_id);
    }
}

if (!function_exists('has_any_group')) {
    function has_any_group(array $group_ids, $user_id = false)
    {
        foreach ($group_ids as $group_id) {
            if (has_group($group_id, $user_id)) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('is_procurement')) {
    function is_procurement()
    {
        return has_group(GROUP_PROCUREMENT);
    }
}

if (!function_exists('is_finance')) {
    function is_finance()
    {
        return has_group(GROUP_FINANCE);
    }
}

if (!function_exists('is_hr')) {
    function is_hr()
    {
        return has_group(GROUP_HR);
    }
}

if (!function_exists('is_misd')) {
    function is_misd()
    {
        return has_group(GROUP_MISD);
    }
}

if (!function_exists('is_admin')) {
    function is_admin()
    {
        return has_group(GROUP_ADMIN);
    }
}

if (!function_exists('is_end_user')) {
    function is_end_user()
    {
        return has_group(GROUP_ENDUSER);
    }
}

if (!function_exists('is_reviewer')) {
    function is_reviewer()
    {
        return has_group(GROUP_REVIEWER);
    }
}

if (!function_exists('is_approver')) {
    function is_approver()
    {
        return has_group(GROUP_APPROVER);
    }
}