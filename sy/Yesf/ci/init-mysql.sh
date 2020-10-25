#!/bin/bash

main() {
	sudo mysql -e "USE mysql; UPDATE user SET password=PASSWORD('123456') where User='root'; FLUSH PRIVILEGES;"
	sudo service mysql restart
	mysql -uroot -p123456 < $TRAVIS_BUILD_DIR/tests/TestApp/mysql.sql
}

main
