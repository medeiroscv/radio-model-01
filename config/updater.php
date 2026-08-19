<?php

return [
    // Repositorio GitHub no formato "dono/repositorio" (ex.: meuuser/radio-cms)
    'repo' => env('UPDATE_REPO'),

    // Token GitHub opcional (repositorios privados / evitar limite de rate-limit)
    'token' => env('UPDATE_TOKEN'),
];