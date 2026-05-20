<?php

return [

    'NotificationController' => [
        'all_marked_as_read' => 'All notifications have been marked as read.',
        'marked_as_read' => 'Notification marked as read.',
        'deleted' => 'Notification deleted.',
        'all_deleted' => 'All notifications have been deleted.',
    ],

    'comment' => [
        'new-comment' => 'New comment.',
        'comment-line' => ':first_name :last_name added a comment on the :value_type_file_folder : :name_file_folder',
        'comment-message-to-array' => ':first_name :last_name added a comment on the :value_type_file_folder: :name_file_folder at location :path_file_folder',
        'show-file' => 'Show file.',
    ],

    '2fa-activate' => [
        'activate' => 'Enabling Two-Factor Authentication',
        'for-activate' => 'To enable two-factor authentication, please click the button below.',
        'activate-now' => 'Activate now',
        'link-expire-15m' => 'The link expires in 15 minutes',
    ],

    'link' => [
        'subject' => 'A folder has been shared with you',
        'greeting' => 'Hello,',
        'intro' => 'A user has shared a folder with you via a secure link.',
        'action' => 'Access the folder',
        'outro' => 'If you weren’t expecting this message, feel free to ignore it.',
        'salutation' => 'Regards, The Wesend team',
    ],

    'welcome' => [
        'subject' => 'Welcome to our platform',
        'greeting' => 'Hello :name,',
        'intro' => 'Thank you for joining us. Here is your information:',
        'name' => 'Name: :name',
        'email' => 'Email: :email',
        'phone' => 'Phone: :phone',
        'address' => 'Address: :address',
        'domain_info' => 'You can access your client space at the following domain:',
        'action' => 'Access your domain',
        'questions' => 'If you have any questions, feel free to contact us.',
        'salutation' => 'Best regards, The Team.',
        'not_provided' => 'Not provided',
    ],

    'reset_password' => [
        'subject' => 'Reset your password',
        'line_1' => 'You have requested a password reset.',
        'action' => 'Reset Password',
        'line_2' => 'If you did not request a password reset, please ignore this email.',
    ],

    'welcome_user' => [
        'subject' => 'Welcome to our platform',
        'greeting' => 'Hello :name,',
        'intro_1' => 'Thank you for joining us.',
        'intro_2' => 'Here is your information:',
        'name' => 'Lastname/Firstname: :name',
        'email' => 'Email: :email',
        'phone' => 'Phone: :phone',
        'address' => 'Address: :address',
        'domain_info' => 'You can access your client area via the following domain:',
        'action' => 'Access your domain',
        'questions' => 'If you have any questions, feel free to contact us.',
        'salutation' => "Best regards,\nThe Wesend Team.",
        'not_provided' => 'Not provided',
    ],

    'group_added' => [
        'subject' => 'You have been added to a group',
        'greeting' => 'Hello :name,',
        'line_1' => 'You have been added to the group :group.',
        'footer' => 'You can now collaborate with other group members.',
        'salutation' => 'Best regards, The Team.',
    ],

    'shared' => [
        'subject' => 'A document has been shared with you',
        'greeting' => 'Hello :first_name :last_name',
        'line' => ':from_first_name :from_last_name has shared a document with you: :document with the role :role',
        'action' => 'View the document',
        'salutation' => "Best regards,\nThe Wesend Team.",
        'subcopy' => 'If you’re having trouble clicking the ":actionText" button, copy and paste the URL below into your web browser: :url',
    ],

    'link_download' => [
        'subject' => 'A link has been downloaded',
        'greeting' => 'Hello :first_name :last_name',
        'line' => 'The content of the link created on :created_at has been downloaded.',
        'salutation' => "Kind regards,\nThe Wesend team.",
    ],
];
