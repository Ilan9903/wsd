<?php

return [

    'NotificationController' => [
        'all_marked_as_read' => 'Toutes les notifications ont été marquées comme lues.',
        'marked_as_read' => 'Notification marquée comme lue.',
        'deleted' => 'Notification supprimée',
        'all_deleted' => 'Toutes les notifications ont été supprimées.',
    ],

    'comment' => [
        'new-comment' => 'Nouveau commentaire',
        'comment-line' => ':first_name :last_name a ajouté un commentaire sur le :value_type_file_folder : :name_file_folder',
        'comment-message-to-array' => ':first_name :last_name a ajouté un commentaire sur le :value_type_file_folder : :name_file_folder à l\'emplacement :path_file_folder',
        'show-file' => 'Voir le document',
    ],

    '2fa-activate' => [
        'activate' => 'Activation de la double authentification',
        'for-activate' => 'Pour activer la double authentification, veuillez cliquer sur le bouton ci-dessous.',
        'activate-now' => 'Activer maintenant',
        'link-expire-15m' => 'Le lien expire dans 15 minutes',
    ],

    'link' => [
        'subject' => 'Un dossier a été partagé avec vous',
        'greeting' => 'Bonjour,',
        'intro' => 'Un utilisateur a partagé un dossier avec vous via un lien sécurisé.',
        'action' => 'Accéder au dossier',
        'outro' => 'Si vous ne vous attendiez pas à recevoir ce message, vous pouvez l’ignorer.',
        'salutation' => "Cordialement,\nL’équipe Wesend.",
    ],

    'welcome' => [
        'subject' => 'Bienvenue sur notre plateforme',
        'greeting' => 'Bonjour :name,',
        'intro' => 'Merci de nous avoir rejoints. Voici vos informations :',
        'name' => 'Nom : :name',
        'email' => 'Email : :email',
        'phone' => 'Téléphone : :phone',
        'address' => 'Adresse : :address',
        'domain_info' => 'Vous pouvez accéder à votre espace client via le domaine suivant :',
        'action' => 'Accéder à votre domaine',
        'questions' => 'Si vous avez des questions, n’hésitez pas à nous contacter.',
        'salutation' => "Cordialement,\nL’équipe Wesend.",
        'not_provided' => 'Non renseigné',
    ],

    'reset_password' => [
        'subject' => 'Réinitialisation de votre mot de passe',
        'line_1' => 'Vous avez demandé une réinitialisation de votre mot de passe.',
        'action' => 'Réinitialiser le mot de passe',
        'line_2' => 'Si vous n\'avez pas demandé cette réinitialisation, ignorez cet e-mail.',
    ],

    'welcome_user' => [
        'subject' => 'Bienvenue sur notre plateforme',
        'greeting' => 'Bonjour :name,',
        'intro_1' => 'Merci de nous avoir rejoints.',
        'intro_2' => ' Voici vos informations :',
        'name' => 'Nom/Prénom: :name',
        'email' => 'Email: :email',
        'phone' => 'Téléphone: :phone',
        'address' => 'Adresse: :address',
        'domain_info' => 'Vous pouvez accéder à votre espace client via le domaine suivant :',
        'action' => 'Accéder à votre domaine',
        'questions' => 'Si vous avez des questions, n’hésitez pas à nous contacter.',
        'salutation' => "Cordialement,\nL’équipe Wesend.",
        'not_provided' => 'Non renseigné',
    ],

    'group_added' => [
        'subject' => 'Vous avez été ajouté à un groupe',
        'greeting' => 'Bonjour :name,',
        'line_1' => 'Vous avez été ajouté au groupe :group.',
        'footer' => 'Vous pouvez maintenant collaborer avec les membres du groupe.',
        'salutation' => "Cordialement,\nL’équipe Wesend.",
    ],

    'shared' => [
        'subject' => 'Un document a été partagé avec vous',
        'greeting' => 'Bonjour :first_name :last_name',
        'line' => ':from_first_name :from_last_name vous a partagé un document : :document avec le rôle :role',
        'action' => 'Voir le document',
        'salutation' => "Cordialement,\nL’équipe Wesend.",
        'subcopy' => 'Si vous avez des difficultés à cliquer sur le bouton ":actionText", copiez et collez l’URL ci-dessous dans votre navigateur : :url',
    ],

    'link_download' => [
        'subject' => 'Un lien a été téléchargé',
        'greeting' => 'Bonjour :first_name :last_name',
        'line' => 'Le contenu du lien créé le :created_at a été téléchargé.',
        'salutation' => "Cordialement,\nL’équipe Wesend.",
    ],
];
