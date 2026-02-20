# Smartlegis Support

**Smartlegis Support** é um sistema auxiliador ao Smartlegis que supre necessidades que ainda não foram desenvolvidas no sistema principal.


## Requisitos

Antes de iniciar, certifique-se de que os seguintes softwares e extensões estão instalados:

### Ambiente

- PHP >= 8.1 (recomendado 8.3)
- Composer
- PostgreSQL


### Extensões PHP necessárias

As seguintes extensões PHP são obrigatórias para o funcionamento correto do Laravel e de suas dependências:

- `ctype`
- `curl`
- `dom`
- `fileinfo`
- `filter`
- `gd`
- `hash`
- `iconv`
- `json`
- `libxml`
- `mbstring`
- `openssl`
- `pcre`
- `phar`
- `session`
- `simplexml`
- `tokenizer`
- `xml`
- `xmlwriter`
- `zlib`

Você pode verificar se todas estão instaladas de duas formas:

**Usando o Composer:**

```bash
composer check-platform-reqs
````

**Ou listando as extensões ativas no PHP:**

```bash
php -m
```

Depois, compare a lista com as extensões acima.

## Instalação

**1. Clone o repositório**

```bash
git clone git@github.com:Dimendes-Solucoes/Smartlegis_Support.git
cd Smartlegis_Support
````

**2. Instale as dependências PHP com o Composer**

```bash
composer install
```

**3. Copie o arquivo `.env.example` para `.env`**

```bash
cp .env.example .env
```

**4. Configure as variáveis de ambiente no `.env`**

Edite as seguintes variáveis conforme seu ambiente:

```env
APP_NAME="Nome do Projeto"
APP_URL=localhost

DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=banco de dados
DB_USERNAME=nome do usuario
DB_PASSWORD=senha
```

**5. Gere a chave da aplicação**

```bash
php artisan key:generate
```

**6. Gere o link simbólico**
```bash
php artisan storage:link
```

## Banco de Dados

O sistema é um auxiliar ao SmartlegisBackend, então utilize o mesmo banco de dados configurado no outro repositório

## Inicie o servidor de desenvolvimento

```bash
php artisan serve
```

A aplicação estará disponível em [http://localhost:8000](http://localhost:8000).
