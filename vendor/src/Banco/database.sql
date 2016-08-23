/* database deve ser criada previamente
CREATE DATABASE IF NOT EXISTS service_load CHARACTER SET utf8 COLLATE utf8_general_ci;
*/

/* cria as tabelas se elas não existirem */
CREATE TABLE  (
  id bigint NOT NULL AUTO_INCREMENT,
  
  PRIMARY KEY (id),
  KEY indices ()
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8