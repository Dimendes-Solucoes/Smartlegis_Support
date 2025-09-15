## Smartlegis Support

Este é um projeto **Laravel** com **Vue.js** e **Inertia.js**, utilizando **PostgreSQL** como banco de dados. O ambiente de desenvolvimento é totalmente gerenciado com **Docker Compose**, o que facilita a configuração e garante a consistência para todos os desenvolvedores.

### 1\. Requisitos

Antes de começar, certifique-se de ter o **Docker** e o **Docker Compose** instalados na sua máquina.

  - [Instalar Docker](https://docs.docker.com/get-docker/)
  - [Instalar Docker Compose](https://docs.docker.com/compose/install/)

-----

### 2\. Configuração Inicial

Siga estes passos para preparar o ambiente pela primeira vez.

1.  **Clone o repositório:**

    ```bash
    git clone git@github.com:Dimendes-Solucoes/Smartlegis_Back-end.git
    cd Smartlegis_Back-end
    ```

2.  **Configurar o arquivo de ambiente:**
    Copie o arquivo de exemplo e configure suas variáveis de ambiente.

    ```bash
    cp .env.example .env
    ```

    Abra o novo arquivo `.env` e configure as variáveis do banco de dados e das portas para coincidir com as definidas no `docker-compose.yml`.

    ```ini
    DB_CONNECTION=pgsql
    DB_HOST=db
    DB_PORT=5432
    DB_DATABASE=nomedoseubd
    DB_USERNAME=seuser
    DB_PASSWORD=suasenha

    # Porta do Nginx (Backend) na sua máquina
    APP_PORT=8000
    ```

3.  **Inicie os containers:**
    O comando abaixo inicia todos os serviços (PHP, Nginx, Node e PostgreSQL) em segundo plano, e constrói as imagens pela primeira vez.

    ```bash
    docker-compose up -d --build
    ```

4.  **Instalar dependências e rodar migrações:**
    Agora, rode os comandos para instalar o Composer e o NPM, e para criar as tabelas no banco de dados.

    ```bash
    # Instalar dependências PHP (dentro do container 'app')
    docker-compose exec app composer install

    # Instalar dependências JavaScript (dentro do container 'node')
    docker-compose exec node npm install

    # Gerar a chave da aplicação
    docker-compose exec app php artisan key:generate

    # Rodar as migrações do banco de dados
    docker-compose exec app php artisan migrate
    ```

-----

### 3\. Como Rodar o Projeto

Após a configuração inicial, você só precisa de um comando para começar a trabalhar.

  - **Para iniciar os serviços:**
    ```bash
    docker-compose up -d
    ```
  - **Para parar os serviços:**
    ```bash
    docker-compose down
    ```

O servidor de desenvolvimento estará acessível em **http://localhost:8000** e o HMR do Vite em **http://localhost:5173**.

-----

### 4\. Comandos Úteis

Para executar comandos dentro dos containers, use `docker-compose exec`.

  - **Executar qualquer comando Artisan:**

    ```bash
    docker-compose exec app php artisan [SEU_COMANDO]
    ```

    (Ex: `php artisan cache:clear`, `php artisan tinker`)

  - **Acessar o terminal do container do PHP:**

    ```bash
    docker-compose exec app bash
    ```

  - **Para parar o servidor de desenvolvimento do Vite (npm run dev):**

    ```bash
    docker-compose stop node
    ```

-----

### 5\. Sessão para Mudanças

Após realizar alguma alteração, use o comando abaixo para reconstruir as imagens:

```bash
docker-compose up -d --build
```

Isso garante que todos os ambientes estejam alinhados com as novas versões.