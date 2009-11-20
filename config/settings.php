<?php
/**
 *  settings.php é o arquivo das principais configurações do Spaghetti*. Através delas,
 *  você pode configurar o comportamento da sua aplicação.
 */

/**
  *  App.environment define o ambiente em que sua aplicação se encontra. Geralmente
  *  existem os environments 'development' (para desenvolvimento), 'production'
  *  (para produção) e 'test' (para testes automatizados). Entretanto, você pode
  *  criar quantos environments você desejar.
  */
Config::write('App.environment', 'development');

/**
  *  App.defaultExtension define qual a extensão de arquivo padrão o Spaghetti*
  *  deverá usar caso nenhuma outra tenha sido definida.
  */
Config::write('App.defaultExtension', 'htm');

/**
  *  App.encoding define o conjunto de caracteres usado pela aplicação.
  */
Config::write('App.encoding', 'utf-8');

/**
  *  Security.salt é uma string qualquer que precede dados criptografados, tornando
  *  menos improvável a quebra de hashes usando rainbow tables.
  */
Config::write('Security.salt', 'e6628645a7');

/**
  *  Define se o Spaghetti* está funcionando com o módulo de reescrita de URL ativado.
  *  Caso seu servidor não tenha o recurso de reescrita de URL, defina a configuração
  *  abaixo para false, e sua aplicação estará disponível através de URLs como
  *  /index.php/controller/action/id
  */
Config::write('App.rewriteUrl', true);

/**
  *  Inclui as configurações específicas do ambiente definido em App.environment.
  */
import('config.environments.' . Config::read('App.environment'));

/**
  *  Define a configuração error_reporting do PHP de acordo com Debug.level, definida
  *  no environment atual usado pela aplicação.
  */
Debug::errorReporting(Config::read('Debug.level'));