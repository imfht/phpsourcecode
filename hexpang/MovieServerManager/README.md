# Movie Server Manager
A PHP Movie Server Web Manager.

## Notice(071420)

1. Add new movie source from **https://watchsomuch.org/** (yeeee!)
2. This is the last version, this project is **NO LONGER MAINTAINED**
3. 2020 奥力给!

## Required
1. Aria2
2. php*-curl
3. php*-ssh2
4. composer

## Environment(.env)
```
SERVER_HOST=localhost
SERVER_PORT=22
SERVER_USERNAME=pi
SERVER_PASSWORD=rsapberrypi
SERVER_ARIA=http://localhost:6800/jsonrpc
MOVIE_FILE_URL=/tmp
```

## Installation
1. Clone
2. composer update
3. copy .env.example to .env
4. php artisan key:gen

## Screenshots
![1](https://raw.githubusercontent.com/HexPang/MovieServerManager/master/screenshots/1.png)

![2](https://raw.githubusercontent.com/HexPang/MovieServerManager/master/screenshots/2.png)

![3](https://raw.githubusercontent.com/HexPang/MovieServerManager/master/screenshots/3.png)

![4](https://raw.githubusercontent.com/HexPang/MovieServerManager/master/screenshots/4.png)
