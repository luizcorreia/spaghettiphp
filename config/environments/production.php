<?php
/**
  *  Configurações específicas para o ambiente de produção quando
  *  App.environment = 'production'
  */

/**
  *  Debug.level define quais tipos de mensagem de erro devem ser mostradas. Os
  *  valores possíveis para essa configuração são:
  *  - 0: nenhum erro é mostrado
  *  - 1: são mostrados todos erros, exceto notices e strict coding standards
  *  - 2: são mostrados todos erros, incluindo coding standards, exceto notices
  *  - 3: são mostrados todos erros, incluindo coding standards
  */
Config::write('Debug.level', 0);