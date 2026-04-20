<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Rendez-vous
    |--------------------------------------------------------------------------
    */

    'rdv' => [
        // Nombre maximum de RDV par medecin par jour
        'max_per_day' => env('MONRDV_RDV_MAX_PER_DAY', 20),

        // Heure de debut de la journee de travail (0-23)
        'work_start' => env('MONRDV_RDV_WORK_START', 8),

        // Heure de fin de la journee de travail (0-23, exclusive)
        'work_end' => env('MONRDV_RDV_WORK_END', 18),

        // Duree d'un creneau en minutes (30 = slots de 30 min)
        'slot_minutes' => env('MONRDV_RDV_SLOT_MINUTES', 30),
    ],

];
