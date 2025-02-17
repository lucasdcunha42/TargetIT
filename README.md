# Guia de Instalação

## 1. Clonar o Repositório
Clone o repositório do projeto e acesse a pasta do mesmo:
```sh
git clone https://github.com/seu-usuario/TargetIT.git
cd TargetIT
```

## 2. Instalar Dependências
Instale as dependências do PHP com o Composer:
```sh
composer install
```

## 3. Configurar Variáveis de Ambiente
Copie o arquivo de exemplo `.env` e edite conforme necessário:
```sh
cp .env.example .env
```
Abra o arquivo `.env` e configure as credenciais do banco de dados:
```properties
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=targetit
DB_USERNAME=root
DB_PASSWORD=
```

## 4. Gerar Chave da Aplicação
Gere a chave única da aplicação Laravel:
```sh
php artisan key:generate
```

## 5. Executar Migrações
Crie as tabelas no banco de dados rodando as migrações:
```sh
php artisan migrate:fresh
```

## 6. Gerar Chave JWT
Se o projeto utiliza autenticação JWT, gere a chave secreta:
```sh
php artisan jwt:secret
```

## 7. Criar o Primeiro Usuário Administrador
O projeto inclui um comando Artisan para criar o primeiro usuário administrador. Execute:
```sh
php artisan users:create
```
Siga as instruções exibidas no terminal para definir as credenciais do administrador.

## 8. Iniciar o Servidor
Para rodar a aplicação localmente, execute:
```sh
php artisan serve
```
A aplicação estará disponível em `http://localhost:8000`.

## 9. Acessar a Documentação da API
A documentação da API está disponível em:
[http://127.0.0.1:8000/docs#introduction](http://127.0.0.1:8000/docs#introduction)

---
Com esses passos, seu ambiente estará pronto para desenvolvimento. Caso tenha dúvidas, consulte a documentação do Laravel ou verifique possíveis erros no terminal.

