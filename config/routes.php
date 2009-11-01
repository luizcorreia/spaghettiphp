<?php
/**
 * Esse arquivo é onde você pode definir rotas e prefixos para sua aplicação.
 * Rotas são usadas para direcionar URLs para determinadas partes da aplicação,
 * sem que você precise renomear controllers e actions. Já os prefixos permitem
 * que você separe diversas partes da aplicação, como um painel de administração,
 * por exemplo.
 * 
 */

import('core.Mapper');

Mapper::connect(
    '/:controller/:action',
    array(
        'controller' => 'home',
        'action' => 'index'
    ),
    array(
        'controller' => '([a-z-_]+)',
        'action' => '([a-z-_]+)'
    )
);

?>