# Install

**[中文文档](../DOCKER.md)**

- **Install(Docker)**
- [Install(Server)](SERVER.md)
- [Install(Bt Panel)](../BT.md)

## Setup (using Docker)

> `Docker` & `Docker Compose` must be installed

#### 1. Clone the repository

```bash
// using ssh
git clone git@github.com:kuaifan/wookteam.git
// or you can use https
git clone https://github.com/kuaifan/wookteam.git

// enter directory
cd wookteam

// copy .env
cp .env.docker .env
```

#### 2. Build image & install

```bash
./cmd build php
./cmd composer install
./cmd artisan key:generate
./cmd artisan migrate --seed
./cmd php bin/wookteam --port=8080 --ssl=4433
./cmd up -d
./cmd npm install
./cmd npm run prod
./cmd restart
```

Installed, project url: **`http://IP:PORT`** (`PORT` is the parameter `8080` in the build).

### Change port

```bash
./cmd php bin/wookteam --port=8080 --ssl=4433
./cmd up -d
```

#### Stop server

```bash
./cmd stop
```

> P.S: Once application is setup, whenever you want to start the server (if it is stopped) run below command

```bash
./cmd start
```

### Shortcuts for running command

> You can do this using the following command

```bash
./cmd artisan "your command"          // To run a artisan command
./cmd php "your command"              // To run a php command
./cmd composer "your command"         // To run a composer command
./cmd supervisorctl "your command"    // To run a supervisorctl command
./cmd test "your command"             // To run a phpunit command
./cmd npm "your command"              // To run a npm command
./cmd yarn "your command"             // To run a yarn command
./cmd mysql "your command"            // To run a mysql command
```

## Default Account

- admin/123456
- system/123456

## Upgrade

**Note: Please backup your data before upgrading!**

- Go to the directory and run the following commands in turn:

```bash
git fetch --all
git reset --hard origin/master
git pull
./cmd composer update
./cmd artisan migrate

./cmd npm install
./cmd npm run prod

./cmd restart
```
