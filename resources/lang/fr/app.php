<?php

return [
    'app_name' => 'AlertBook',
    'project' => 'Développé par Research For Development (RFD)',

    'ribbon' => [
        'platform' => 'Plateforme humanitaire — Gestion des incidents VBG',
        'secure_access' => 'Accès sécurisé',
        'switch_to' => 'English',
    ],

    'nav' => [
        'login' => 'Se connecter',
        'register' => 'Créer un compte',
        'activation_required' => 'Activation requise',
        'dashboard' => 'Dashboard',
    ],

    'hero' => [
        'pill' => 'Système institutionnel de gestion de cas',
        'title' => 'Gestion sécurisée, structurée et traçable des incidents VBG',
        'subtitle' => "AlertBook soutient les organisations humanitaires dans l’enregistrement, la validation, le suivi et le référencement des incidents, avec un contrôle strict des accès et un journal d’audit.",
        'cta_login' => 'Accéder à la plateforme',
        'cta_register' => 'Créer un compte',
        'process_label' => 'Processus :',
        'process_text' => "Création de compte → activation par superadmin → attribution organisation, province et rôle.",
    ],

    'highlights' => [
        'protection_kicker' => 'Protection',
        'protection_title' => 'Confidentialité & rôles',
        'protection_text' => 'Accès limité par rôle et province, niveaux de confidentialité.',

        'coord_kicker' => 'Coordination',
        'coord_title' => 'Référencement structuré',
        'coord_text' => 'Orientation vers partenaires et suivi des réponses.',

        'acc_kicker' => 'Redevabilité',
        'acc_title' => 'Audit & reporting',
        'acc_text' => 'Journal d’audit, exports, fiche PDF A4.',
    ],

    'panel' => [
        'kicker' => 'Cadre de confidentialité',
        'title' => 'Principes de protection des données',
        'items' => [
            [
                'title' => 'Minimisation des données',
                'desc'  => 'Collecte limitée aux informations nécessaires au suivi.',
            ],
            [
                'title' => 'Traçabilité des actions',
                'desc'  => 'Journal d’audit pour validation, assignation, archivage, etc.',
            ],
            [
                'title' => 'Contrôle d’accès',
                'desc'  => 'Rôles, périmètre provincial, et workflow de validation.',
            ],
        ],
        'note_title' => 'Note importante',
        'note_text'  => 'Aucun contenu sensible n’est affiché publiquement sur le site.',
    ],

    'features' => [
        'kicker' => 'Fonctionnalités',
        'title' => 'Fonctionnalités clés',
        'subtitle' => "Un flux de travail complet, depuis l’enregistrement jusqu’au référencement et au reporting.",
        'cards' => [
            [
                'icon' => '📌',
                'title' => 'Enregistrement structuré',
                'desc' => 'Création d’incidents avec validation, statut, sévérité, photo et localisation.',
            ],
            [
                'icon' => '🧩',
                'title' => 'Types de violences',
                'desc' => 'Association de plusieurs violences par incident, avec descriptions.',
            ],
            [
                'icon' => '📝',
                'title' => 'Notes de dossier',
                'desc' => 'Ajout de notes chronologiques avec confidentialité et pièces jointes.',
            ],
            [
                'icon' => '🤝',
                'title' => 'Référencements',
                'desc' => 'Orientation vers les structures de prise en charge et suivi de la réponse.',
            ],
            [
                'icon' => '📄',
                'title' => 'Fiche PDF A4',
                'desc' => 'Impression d’une fiche d’incident professionnelle en A4.',
            ],
            [
                'icon' => '📊',
                'title' => 'Exports & tableaux de bord',
                'desc' => 'Exports Excel/CSV et visualisations par province, statut, période.',
            ],
        ],
        'tip' => 'Conseil : utilisez l’export pour produire une matrice de suivi conforme aux exigences de coordination.',
    ],

    'cta' => [
        'kicker' => 'Accès contrôlé',
        'title' => 'Créer un compte et demander l’activation',
        'text' => 'Après inscription, un superadmin activera votre compte et attribuera votre organisation, province et rôle.',
        'btn_register' => 'Créer un compte',
        'btn_login' => 'Se connecter',
    ],

    'footer' => [
        'tagline' => 'Gestion des incidents VBG — Projet Mukwege',
        'rights' => 'Tous droits réservés.',
    ],
];
