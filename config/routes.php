<?php
/**
 *  Esse arquivo é onde você pode definir rotas para sua aplicação. Rotas são
 *  usadas para direcionar URLs para determinadas partes da aplicação, sem que
 *  você precise renomear controllers e actions. Já os prefixos permitem que você
 *  separe diversas partes da aplicação, como um painel de administração, por
 *  exemplo.
 */

$defaults = array(
    'controller' => 'home',
    'action' => 'index'
);
$regex = array(
    'controller' => '([a-z-_]+)',
    'action' => '([a-z-_]+)',
    'id' => '([0-9]+)'
);
Mapper::connect('/', $defaults, $regex);
Mapper::connect('/:controller', $defaults, $regex);
Mapper::connect('/:controller/:id', $defaults, $regex);
Mapper::connect('/:controller/:action', $defaults, $regex);
Mapper::connect('/:controller/:action/:id', $defaults, $regex);
# Mapper::connect('/:controller/:action/:id/*', $defaults, $regex);