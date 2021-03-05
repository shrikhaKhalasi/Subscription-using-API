<?php


use Carbon\Carbon;

return [

    'system_user_id' => 1,
    'system_role_id' => 1,

    'token_expiry' => env('TOKEN_EXPIRY', (60 * 60 * 24)),// Default 24 hours
    'cron_hours_unverified' => '24',// Default 24 hours
    'project_name' => 'Make My Health Career',
    'project_url' => 'https://admin.makemyhealthcareer.com',
    'project_email' => 'contact@jobportal.com',
    'project_admin_email' => 'admin@jobportal.com',

    'calender' => [
        'date' => Carbon::now()->toDateString(),
        'date_format' => Carbon::now()->format('Y-m-d'),
        'time' => Carbon::now()->toTimeString(),
        'date_time' => Carbon::now()->toDateTimeString(),
        'export' => Carbon::now()->format('d-m-Y_H:i:s'),
        'date_timestamp' => strtotime(Carbon::now()->toDateTimeString()),
    ],

    'file' => [
        'name' => Carbon::now('Asia/Kolkata')->format('d_m_Y') . '_' . Carbon::now('Asia/Kolkata')->format('g_i_a'),
    ],

    'validation' => [
        'double' => 'regex:/^\d+(\.\d{1,2})?$/',        // Double validation with 2 floating points
        'password_regex' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,20}$/',  // at least 1 lowercase AND 1 uppercase 1 number and 6-20 character
        'password_regex_message' => 'The :attribute must be 6–20 characters, and include a number, a lower and a upper case letter',
    ],

    'messages' => [

        'success' => 'Success',
        'registration_success' => 'Please check your inbox to activate your account',
        'forgotpassword_success' => 'Password reset instructions has been sent to your email. Please check your inbox/spam',
        'forgotpassword_error' => 'Invalid Email',
        'something_wrong' => 'Something went wrong.',
        'login' => [
            'success' => 'Login is successful.',
            'unverified_account' => 'Your email address has not been verified yet.',
            'wrong_credentials' => 'Invalid email or password.',
            'login_token_failed' => 'Could not create login token.',
            'inactive_account' => 'Your account is not deactivate by admin.',
        ],
        'password_changed' => "Password has been changed.",
        'invalid_old_password' => "Invalid old password.",
        'similar_password' => "Please enter a password which is not similar then current password.",
        'not_match_confirm_password' => "New password is not match to confirm password.",
        'apply_permissions' => 'Role permissions applied successfully.',
        'file_csv_error' => 'please upload csv file',
        'no_data_found' => 'No data found.',
        'no_responses_found' => 'Sorry, no responses found.',
        'token_amount_exceed' => 'Assign token total must be less or equal to ',
        'token_expire' => 'Invalid token id or token expired.',
        'delete_multiple_error' => 'Please select records',
        'logout' => 'You have been Successfully logged out!',
        'admin_user_delete_error' => 'Super-admin user can\'t be delete.',
        'admin_role_delete_error' => 'Administrator role can\'t be delete.',
    ],

    'subscription_message' => [
        'plan_delete' => 'This plan can\'t be deleted because it has been used in a subscription transaction. If you’re no longer using this plan, you can always deactivate it.',
    ],

    'validation_codes' => [
        'unauthorized' => 401,
        'forbidden' => 403,
        'not_found' => 404,
        'unprocessable_entity' => 422,
        'internal_server' => 500,
        'ok' => 200
    ],

    'user' => [
        'role' => [
            '1' => 'Admin'
        ],
        'role_code' => [
            'admin' => '1'
        ],

        'user_type' => [
            '1' => 'Admin',
            '2' => 'Employer',
            '3' => 'Job Seeker',
        ],
        'user_type_code' => [
            'admin' => '1',
            'employer' => '2',
            'jobseeker' => '3',
        ],
        'status' => [
            '0' => 'Inactive',
            '1' => 'Active',
            '2' => 'NewRegister',
            '3' => 'Pending Request',
            // '4' => 'VerifySms',
        ],
        'status_code' => [
            'inactive' => '0',
            'active' => '1',
            'new_register' => '2',
            'verify_email' => '3',
            //'Verify_sms' => '4',
        ],
        'is_mobile_verify' => [
            '0' => 'No',
            '1' => 'Yes',
        ],
        'is_mobile_verify_code' => [
            'no' => '0',
            'yes' => '1'
        ],
        'gender' => [
            '0' => 'Female',
            '1' => 'Male',
            '2' => 'Any'
        ],
        'gender_code' => [
            'female' => '0',
            'male' => '1',
            'any' => '2'
        ],
        'availability_setting' => [
            '0' => 'Actively Looking for a job',
            '1' => 'Open to job opportunities',
            '2' => 'Not looking for a job'
        ],
        'notification' => [
            '0' => 'No',
            '1' => 'Yes',
        ],
        'notification_code' => [
            'no' => '0',
            'yes' => '1'
        ],
    ],

    'webadmin' => [
        'status' => [
            '0' => 'Inactive',
            '1' => 'Active'
        ],
        'status_code' => [
            'inactive' => '0',
            'active' => '1'
        ],

        'contact_status' => [
            '0' => 'Pending',
            '1' => 'Under Process',
            '2' => 'Closed'
        ],
        'contact_status_code' => [
            'pending' => '0',
            'under_process' => '1',
            'closed' => '2'
        ],
    ],

    'job' => [
        'compensation_range' => [
            '1' => 'Monthly',
            '2' => 'Yearly'
        ],
        'compensation_range_code' => [
            'monthly' => '1',
            'yearly' => '2'
        ],
        'gender' => [
            '0' => 'Female',
            '1' => 'Male',
            '2' => 'Any'
        ],
        'gender_code' => [
            'female' => '0',
            'male' => '1',
            'any' => '2'
        ],
        'show_salary_compensation' => [
            '0' => 'No',
            '1' => 'Yes',
        ],
        'show_salary_compensation_code' => [
            'no' => '0',
            'yes' => '1',
        ],
        'travelling_required' => [
            '0' => 'No',
            '1' => 'Yes',
        ],
        'travelling_required_code' => [
            'no' => '0',
            'yes' => '1',
        ],
        'show_company_details' => [
            '0' => 'No',
            '1' => 'Yes',
        ],
        'show_company_details_code' => [
            'no' => '0',
            'yes' => '1',
        ],
        'status' => [
            '0' => 'Inactive',
            '1' => 'Active'
        ],
        'status_code' => [
            'inactive' => '0',
            'active' => '1',
        ],

        'max_question' => 5,
    ],

    'plans' => [
        'interval' => [
            '0' => 'Day',
            '1' => 'Week',
            '2' => 'Month',
            '3' => 'Year',
        ],
        'interval_code' => [
            'day' => '0',
            'week' => '1',
            'month' => '2',
            'year' => '3',
        ],

        'is_active' => [
            '0' => 'No',
            '1' => 'Yes',
        ],
        'is_active_code' => [
            'no' => '0',
            'yes' => '1',
        ],

        'is_trial' => [
            '0' => 'No',
            '1' => 'Yes',
        ],
        'is_trial_code' => [
            'no' => '0',
            'yes' => '1',
        ],
    ],

    'user_plan_usages' => [
        'usage_type' => [
            '0' => 'Email',
            '1' => 'SMS',
            '2' => 'Download Resume',
        ],
        'usage_type_code' => [
            'email' => '0',
            'sms' => '1',
            'download_resume' => '2',
        ],

        'is_disable' => [
            '0' => 'No',
            '1' => 'Yes',
        ],
        'is_disable_code' => [
            'no' => '0',
            'yes' => '1'
        ],
    ],

    'job_application' => [
        'is_deleted_by_employer' => [
            '0' => 'No',
            '1' => 'Yes',
        ],
        'is_deleted_by_employer_code' => [
            'no' => '0',
            'yes' => '1'
        ],
        'status' => [
            '0' => 'Awaiting Review',
            '1' => 'Reviewed',
            '2' => 'Contacting',
            '3' => 'Offered',
            '4' => 'Hired',
            '5' => 'Rejected',
            '6' => 'Deleted',
        ],
        'status_code' => [
            'awaiting_review' => '0',
            'reviewed' => '1',
            'contacting' => '2',
            'offered' => '3',
            'hired' => '4',
            'rejected' => '5',
            'deleted' => '6',
        ],
        'is_interested' => [
            '0' => 'No',
            '1' => 'Yes',
            '2' => 'May be',
        ],
        'is_interested_code' => [
            'no' => '0',
            'yes' => '1',
            'may_be' => '2',
        ],
    ],

    'email_subscription' => [
        'subscribe_from' => [
            '0' => 'Admin',
            '1' => 'Employer',
            '2' => 'Job Seeker',
        ],
        'subscribe_from_code' => [
            'admin' => '0',
            'employer' => '1',
            'jobseeker' => '2',
        ],
    ],

    'broadcasting' => [
        'operation' => [
            '1' => 'Add',
            '2' => 'Edit',
            '3' => 'Delete',
            '4' => 'Delete Multiple',
        ],

        'operation_code' => [
            'add' => '1',
            'edit' => '2',
            'delete' => '3',
            'delete_multiple' => '4',
        ],
    ],

    'contact' => [
        'contact_from' => [
            '0' => 'Employer',
            '1' => 'Job Seeker',
        ],
        'contact_from_code' => [
            'employer' => '0',
            'jobseeker' => '1',
        ],
    ],

    'testimonials' => [
        '0' => 'No',
        '1' => 'Yes',
    ],
    'testimonials_code' => [
        'no' => '0',
        'yes' => '1'
    ],

    'site_settings' => [
        '0' => 'No',
        '1' => 'Yes',
    ],
    'site_settings_code' => [
        'no' => '0',
        'yes' => '1'
    ],

    'plan_package' => [
        '0' => 'Inactive',
        '1' => 'Active',
    ],
    'plan_package_code' => [
        'inactive' => '0',
        'active' => '1'
    ],

    'masters' => [
        'show_homepage' => [
            '0' => 'No',
            '1' => 'Yes'
        ],
    ],

    'permission' => [
        'has_permission' => '1',
        'has_not_permission' => '0',
        'role_guard_name' => 'web',
        'user_has_not_permission' => "You don\'t have permission to this functionality",
        'user_already_has_permission' => "Given permission already exists",
        'user_clinic_mapping_error' => "User clinic is not mapped yet",
        'module_error' => "Module not in request",
        'invalid_module_error' => "Invalid Module",
        'validation_error_status_code' => 422,
        'permission_assign_success' => 'Permission assign successfully',
        'permission_revert_success' => 'Permission reverted successfully',
        'permission_revert_failure' => 'Permission revert failed',
        'permission_not_found' => 'Permission not found',
        'role_not_found' => 'Role not found',
    ],

    'site' => [
        'logo_url' => "/images/logo.png",
        'employer_site_url' => 'https://employer.makemyhealthcareer.com/',
        'jobseeker_site_url' => 'https://makemyhealthcareer.com/',
        'admin_site_url' => 'https://admin.makemyhealthcareer.com/'
    ],

    'image' => [
        'dir_path' => '/storage/',
        'default_types' => 'gif|jpg|png|jpeg',
        'user_default_img' => 'images/default.jpg',
    ],

    'allowed_ip_addresses' => [
        'telescope' => env('TELESCOPE_ALLOWED_IP_ADDRESSES'),
    ],

];
