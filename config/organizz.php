<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Super administrateurs (e-mail)
    |--------------------------------------------------------------------------
    |
    | Liste d’e-mails (séparés par des virgules) qui reçoivent automatiquement
    | le statut super admin à la création du compte. Complète la colonne
    | users.is_super_admin (promotion manuelle ou migration).
    |
    */
    'super_admin_emails' => array_values(array_filter(array_map(
        static fn (string $e): string => strtolower(trim($e)),
        explode(',', (string) env('SUPER_ADMIN_EMAILS', 'raphael.degand@ldmedia.be'))
    ))),

];
