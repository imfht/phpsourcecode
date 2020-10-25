#!/bin/bash

installExt() {
	stage=$(mktemp -d)

	cd $stage
	wget -O ${1}.tar.gz https://github.com/${2}/archive/${3}.tar.gz
	tar -zxf ${1}.tar.gz
	cd ${4}
	phpize
	./configure ${5}
	make -j4
	sudo make install
	if [[ -f "$TRAVIS_BUILD_DIR/ci/config/${1}.ini" ]];then
		phpenv config-add "$TRAVIS_BUILD_DIR/ci/config/${1}.ini"
	fi

	cd $TRAVIS_BUILD_DIR
	sudo rm -rf $stage
}

installHiRedis() {
	stage=$(mktemp -d)

	cd $stage
	wget -O hiredis.tar.gz https://github.com/redis/hiredis/archive/v${1}.tar.gz
	tar -zxf hiredis.tar.gz
	cd hiredis-${1}
	make -j4
	sudo make install
	sudo ldconfig

	cd $TRAVIS_BUILD_DIR
	sudo rm -rf $stage
}

main() {
	swoole_ver="4.2.13"
	hiredis_ver="0.14.0"
	yac_ver="2.0.2"
	seaslog_ver="2.0.2"
	yaconf_ver="1.0.7"

	# PHP Version
	is_php_73=$(php -r "echo version_compare(PHP_VERSION, '7.3');")

	# Install hiredis
	installHiRedis $hiredis_ver

	# Install swoole
	installExt "swoole" "swoole/swoole-src" "v${swoole_ver}" "swoole-src-${swoole_ver}" "--enable-sockets=yes --enable-openssl=yes --enable-mysqlnd=yes"

	# Install SeasLog
	mkdir -p $HOME/log/www
	chmod -R 0777 $HOME/log/www
	sed -i "s@LOG_DIR@$HOME/log/www@" $TRAVIS_BUILD_DIR/ci/config/seaslog.ini
	installExt "seaslog" "SeasX/SeasLog" "SeasLog-${seaslog_ver}" "SeasLog-SeasLog-${seaslog_ver}"

	# Install Yac
	if [[ "$is_php_73" == "-1" ]];then
		installExt "yac" "laruence/yac" "yac-${yac_ver}" "yac-yac-${yac_ver}"
	else
		echo -e "Skip install Yac\n"
	fi

	# Install Yaconf
	if [[ "$is_php_73" == "-1" ]];then
		installExt "yaconf" "laruence/yaconf" "yaconf-${yaconf_ver}" "yaconf-yaconf-${yaconf_ver}"
	else
		echo -e "Skip install Yaconf\n"
	fi
}

main