<?php

return [
    'app_name' => 'AlertBook',
    'project' => 'Developed by Research For Development (RFD)',

    'ribbon' => [
        'platform' => 'Humanitarian platform — GBV incident management',
        'secure_access' => 'Secure access',
        'switch_to' => 'Français',
    ],

    'nav' => [
        'login' => 'Sign in',
        'register' => 'Create an account',
        'activation_required' => 'Activation required',
        'dashboard' => 'Dashboard',
    ],

    'hero' => [
        'pill' => 'Institutional case management system',
        'title' => 'Secure, structured and traceable management of GBV incidents',
        'subtitle' => 'AlertBook supports humanitarian organizations in incident intake, validation, follow-up and referrals, with strict access control and audit logging.',
        'cta_login' => 'Access the platform',
        'cta_register' => 'Create an account',
        'process_label' => 'Process:',
        'process_text' => 'Account creation → activation by superadmin → organization, province and role assignment.',
    ],

    'highlights' => [
        'protection_kicker' => 'Protection',
        'protection_title' => 'Confidentiality & roles',
        'protection_text' => 'Access restricted by role and province, confidentiality levels.',

        'coord_kicker' => 'Coordination',
        'coord_title' => 'Structured referrals',
        'coord_text' => 'Referral to partners and response tracking.',

        'acc_kicker' => 'Accountability',
        'acc_title' => 'Audit & reporting',
        'acc_text' => 'Audit logs, exports, A4 PDF sheet.',
    ],

    'panel' => [
        'kicker' => 'Confidentiality framework',
        'title' => 'Data protection principles',
        'items' => [
            [
                'title' => 'Data minimization',
                'desc'  => 'Collection limited to what is required for follow-up.',
            ],
            [
                'title' => 'Action traceability',
                'desc'  => 'Audit log for validation, assignment, archiving, etc.',
            ],
            [
                'title' => 'Access control',
                'desc'  => 'Roles, provincial scope, and validation workflow.',
            ],
        ],
        'note_title' => 'Important note',
        'note_text'  => 'No sensitive content is publicly displayed on the site.',
    ],

    'features' => [
        'kicker' => 'Capabilities',
        'title' => 'Key capabilities',
        'subtitle' => 'A complete workflow from intake to referral and reporting.',
        'cards' => [
            [
                'icon' => '📌',
                'title' => 'Structured intake',
                'desc' => 'Create incidents with validation workflow, status, severity, photo and location.',
            ],
            [
                'icon' => '🧩',
                'title' => 'Violence types',
                'desc' => 'Attach multiple violence types per incident with descriptions.',
            ],
            [
                'icon' => '📝',
                'title' => 'Case notes',
                'desc' => 'Chronological notes with confidentiality and attachments.',
            ],
            [
                'icon' => '🤝',
                'title' => 'Referrals',
                'desc' => 'Refer to service providers and track response status.',
            ],
            [
                'icon' => '📄',
                'title' => 'A4 PDF sheet',
                'desc' => 'Generate a professional A4 incident sheet.',
            ],
            [
                'icon' => '📊',
                'title' => 'Exports & dashboards',
                'desc' => 'Excel/CSV exports and visualizations by province, status, timeframe.',
            ],
        ],
        'tip' => 'Tip: use exports to produce a coordination-ready follow-up matrix.',
    ],

    'cta' => [
        'kicker' => 'Controlled access',
        'title' => 'Create an account and request activation',
        'text' => 'After registration, a superadmin will activate your account and assign your organization, province and role.',
        'btn_register' => 'Create an account',
        'btn_login' => 'Sign in',
    ],

    'footer' => [
        'tagline' => 'GBV Incident Management — by Research For Development (RFD)',
        'rights' => 'All rights reserved.',
    ],
];
