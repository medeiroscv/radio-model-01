<?php

return [
    'repo' => env('UPDATE_REPO'),
    'token' => env('UPDATE_TOKEN'),

    // Arquivos aplicados por requisição. Lotes curtos evitam Gateway Timeout.
    'batch_size' => (int) env('UPDATE_BATCH_SIZE', 180),
];
