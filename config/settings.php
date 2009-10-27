<?php
/**
 *  Esse é o arquivo das principais configurações do Spaghetti*. Através delas,
 *  você pode configurar o comportamento da sua aplicação.
 */

/**
  *  App.environment define o ambiente em que sua aplicação se encontra. Geralmente
  *  existem os environments development (para desenvolvimento), production (para
  *  produção) e test (para testes automatizados).
  */
Config::write("App.environment", "development");

/**
  *  App.defaultExtension define qual a extensão de arquivo padrão o Spaghetti*
  *  deverá usar caso nenhuma outra tenha sido definida.
  */
Config::write("App.defaultExtension", "htm");

/**
  *  App.encoding define o conjunto de caracteres usado pela aplicação.
  */
Config::write("App.encoding", "utf-8");

/**
  *  Debug.level define quais tipos de mensagem de erro devem ser mostradas. O valor
  *  0 não mostra mensagem algum, e 1 mostra todos os erros.
  */
Config::write("Debug.level", 1);

/**
  *  Security.salt é uma string qualquer que precede dados criptografados, tornando
  *  menos improvável a quebra de hashes usando rainbow tables.
  */
Config::write("Security.salt", "e6628645a7");

?>