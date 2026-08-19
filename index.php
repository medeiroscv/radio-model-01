<?php

/**
 * RadioCMS — Front controller de compatibilidade.
 *
 * O Laravel exige que o document root do servidor web aponte para a pasta
 * "public". Se o seu host não permite isso (ex.: hospedagem compartilhada em
 * que a raiz do site é a pasta do projeto), este arquivo garante que o
 * aplicativo funcione normalmente. Ele simplesmente carrega o front controller
 * real da pasta public.
 */

require __DIR__.'/public/index.php';