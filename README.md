# Carrega arquivos para o frontend

## Em desenvolvimento

## Objetivo geral

Criar um micro serviço que retorne arquivos css, html, e javascript minificados

### Objetivo específico

1. Criar um repositório de código fonte de componentes js, css e html;
2. Criar uma base de dados para associar aplicação (frontend) com os componentes que esta vai utilizar;
3. Ao criar o arquivo index.html injeta o script e o link (css) chamando a api
4. Por sua vez a api verifica todos os arquivos js/css/html que esta precisa e junta todos em um só, na ordem que foi definida;
5. Minifica os mesmos e retorna um arquivo único;

### Performance

1. Na primeira requisição de uma aplicação o serviço deve gravar um cache do arquivo minificado;
2. Quando solicitada novamente a api deve verificar se houve alteração em algum componente, desde a última minificação;
3. Se não houve deve retornar a que está em cache;
4. Se houve então refaz a minificação e salva novamente em cache;