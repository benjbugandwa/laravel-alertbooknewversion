<?php

return [
    'app_name' => 'AlertBook',
    'project' => 'Developed by UNHCR for the Protection Cluster',

    'ribbon' => [
        'platform' => 'Humanitarian platform — Alert Management',
        'secure_access' => 'Secure access',
        'switch_to' => 'Français',
    ],

    'nav' => [
        'login' => 'Login',
        'register' => 'Register',
        'activation_required' => 'Activation required',
        'dashboard' => 'Dashboard',
    ],

    'hero' => [
        'pill' => 'Institutional Alert Management System',
        'title' => 'Secure, harmonized and structured alert management',
        'subtitle' => 'AlertBook supports humanitarian organizations in recording, validating, tracking, and referring alerts, with strict access control and audit logging.',
        'cta_login' => 'Access the platform',
        'cta_register' => 'Create an account',
        'process_label' => 'Process:',
        'process_text' => 'Account creation → activation by superadmin → assignment of organization, province, and role.',
    ],

    'highlights' => [
        'protection_kicker' => 'Protection',
        'protection_title' => 'Confidentiality & roles',
        'protection_text' => 'Access restricted by role and province, with multiple confidentiality levels.',

        'coord_kicker' => 'Coordination',
        'coord_title' => 'Structured referrals',
        'coord_text' => 'Referral to partners and response tracking.',

        'acc_kicker' => 'Accountability',
        'acc_title' => 'Audit & reporting',
        'acc_text' => 'Audit logs, exports, and A4 PDF report.',
    ],

    'panel' => [
        'kicker' => 'Confidentiality Framework',
        'title' => 'Data protection principles',
        'items' => [
            [
                'title' => 'Data minimization',
                'desc'  => 'Collection limited to information necessary for follow-up.',
            ],
            [
                'title' => 'Action traceability',
                'desc'  => 'Audit logs for validation, assignment, archiving, etc.',
            ],
            [
                'title' => 'Access control',
                'desc'  => 'Roles, provincial scope, and validation workflow.',
            ],
        ],
        'note_title' => 'Important note',
        'note_text'  => 'No sensitive content is publicly displayed on the platform.',
    ],

    'features' => [
        'kicker' => 'Features',
        'title' => 'Key features',
        'subtitle' => 'A complete workflow from registration to referral and reporting.',
        'cards' => [
            [
                'icon' => '📌',
                'title' => 'Structured registration',
                'desc' => 'Create alerts with validation, status, severity, photo, and location.',
            ],
            [
                'icon' => '🧩',
                'title' => 'Violation types',
                'desc' => 'Link multiple violations to a single incident with descriptions.',
            ],
            [
                'icon' => '📝',
                'title' => 'Case notes',
                'desc' => 'Add chronological notes with confidentiality and attachments.',
            ],
            [
                'icon' => '🤝',
                'title' => 'Referrals',
                'desc' => 'Refer cases to service providers and track responses.',
            ],
            [
                'icon' => '📄',
                'title' => 'A4 PDF report',
                'desc' => 'Print a professional incident report in A4 format.',
            ],
            [
                'icon' => '📊',
                'title' => 'Exports & dashboards',
                'desc' => 'Excel/CSV exports and visualizations by province, status, and period.',
            ],
        ],
        'tip' => 'Tip: use exports to produce monitoring matrices aligned with coordination requirements.',
    ],

    'cta' => [
        'kicker' => 'Controlled access',
        'title' => 'Create an account and request activation',
        'text' => 'After registration, a superadmin will activate your account and assign your organization, province, and role.',
        'btn_register' => 'Create an account',
        'btn_login' => 'Login',
    ],

    'footer' => [
        'tagline' => 'Alert and Incident Management — AlertBook Project',
        'rights' => 'All rights reserved.',
    ],
];
