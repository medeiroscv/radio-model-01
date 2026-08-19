# Rádio Model 01 — RadioCMS

Sistema de rádio web com autogerenciamento de programação, feito com Laravel 12 + Vue 3 + Inertia + Tailwind.

## Requisitos

- PHP 8.3+ com extensões: `pdo_mysql`, `mbstring`, `openssl`, `gd`, `curl`, `zip`, `xml`, `fileinfo`
- MySQL 5.7+ ou MariaDB 10.3+
- Web server: Apache, Nginx ou IIS (com suporte a rewrite)

> Os assets (CSS/JS) já vêm compilados na pasta `public/build`. **Não é necessário Node.js/npm.**

## Instalação

1. **Descompacte** o pacote na raiz do seu site (ex.: `htdocs`, `www`, ou a pasta do domínio).

2. **Aponte o servidor web para a pasta `public`** (padrão Laravel). A pasta `public` contém o `index.php` — o front controller do aplicativo.

   - Se o seu host **não permitir** apontar o documento raiz para `public`, não há problema: o pacote inclui um `index.php` e um `.htaccess` na raiz que fazem o aplicativo funcionar mesmo com o documento raiz apontando para a pasta do projeto. Nesse caso, garanta que o `mod_rewrite` do Apache esteja ativo.

3. **Crie o banco de dados** no MySQL (o instalador cria as tabelas, mas não o banco):

   ```sql
   CREATE DATABASE radiocms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

4. **Acesse a URL do instalador** no navegador: `https://seusite.com/install`

5. **Preencha o formulário**:
   - Conexão com o banco (host, porta, nome do banco, usuário e senha)
   - Dados da rádio (nome, descrição, etc.)
   - Conta de administrador (e-mail e senha)

6. Clique em **Instalar**. Ao concluir, acesse o painel em `https://seusite.com/admin`.

> Se um erro de permissão de escrita ocorrer, garanta que as pastas `.env`, `storage` e `bootstrap/cache` tenham permissão de escrita pelo servidor web.

## Atualizações via GitHub

O site pode se atualizar diretamente pelo GitHub, sem precisar subir arquivos por FTP.

### Publicando uma nova versão (desenvolvedor)

1. Publique **todo** o projeto em um repositório no GitHub (público) — incluindo as pastas `vendor` e `public/build`, pois o site não tem Composer/Node no servidor.
2. Antes de commitar, rode `npm run build` para que o frontend já vá compilado.
3. Opcionalmente crie uma **Release** com uma tag no formato `v1.0.1` (o número da versão fica no arquivo `VERSION`).

### Atualizando o site (dono)

1. No servidor, edite o arquivo `.env` e informe o repositório:

   ```env
   UPDATE_REPO=seuusuario/radio-cms
   ```

   Para repositórios privados (ou para evitar o limite de consultas do GitHub), adicione também:

   ```env
   UPDATE_TOKEN=seu_personal_access_token
   ```

2. No painel administrativo, acesse **Sistema → Atualizações** e clique em **Atualizar agora**.

A atualização baixa a nova versão, substitui os arquivos (preservando `.env`, `storage`, uploads e dados), roda as migrations e limpa os caches automaticamente.

## Servidor embutido (apenas desenvolvimento)

Você pode testar localmente com:

```bash
php artisan serve
```

> **Atenção (Windows):** o servidor embutido do PHP pode cair durante a instalação (por limitação do próprio `php -S` no Windows). Se isso acontecer, basta rodar `php artisan migrate --force` manualmente e recarregar o site. Em produção, use Apache/Nginx/IIS.

## Rotas principais

| Rota | Descrição |
|------|-----------|
| `/` | Site público |
| `/install` | Instalador (redireciona quando já instalado) |
| `/admin` | Painel administrativo |

## Estrutura

- `public/` — front controller e assets compilados
- `app/Http/Controllers/` — controladores do site, admin e instalador
- `resources/js/` — frontend Vue 3 + TypeScript
- `tests/Feature/InstallerFlowTest.php` — teste ponta a ponta da instalação

## Testes

```bash
php artisan test
```