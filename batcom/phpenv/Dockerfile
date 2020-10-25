# Pull base image.
FROM ubuntu:14.04

# Install.
RUN \
  #sed -i 's/# \(.*multiverse$\)/\1/g' /etc/apt/sources.list && \
#163 src
  #sed -i '1ideb http://mirrors.163.com/ubuntu/ trusty main restricted universe multiverse\ndeb http://mirrors.163.com/ubuntu/ trusty-security main restricted universe multiverse\ndeb http://mirrors.163.com/ubuntu/ trusty-updates main restricted universe multiverse\ndeb http://mirrors.163.com/ubuntu/ trusty-proposed main restricted universe multiverse\ndeb http://mirrors.163.com/ubuntu/ trusty-backports main restricted universe multiverse\ndeb-src http://mirrors.163.com/ubuntu/ trusty main restricted universe multiverse\ndeb-src http://mirrors.163.com/ubuntu/ trusty-security main restricted universe multiverse\ndeb-src http://mirrors.163.com/ubuntu/ trusty-updates main restricted universe multiverse\ndeb-src http://mirrors.163.com/ubuntu/ trusty-proposed main restricted universe multiverse\ndeb-src http://mirrors.163.com/ubuntu/ trusty-backports main restricted universe multiverse' /etc/apt/sources.list && \
  echo -n 'deb http://mirrors.163.com/ubuntu/ trusty main restricted universe multiverse\ndeb http://mirrors.163.com/ubuntu/ trusty-security main restricted universe multiverse\ndeb http://mirrors.163.com/ubuntu/ trusty-updates main restricted universe multiverse\ndeb http://mirrors.163.com/ubuntu/ trusty-proposed main restricted universe multiverse\ndeb http://mirrors.163.com/ubuntu/ trusty-backports main restricted universe multiverse\ndeb-src http://mirrors.163.com/ubuntu/ trusty main restricted universe multiverse\ndeb-src http://mirrors.163.com/ubuntu/ trusty-security main restricted universe multiverse\ndeb-src http://mirrors.163.com/ubuntu/ trusty-updates main restricted universe multiverse\ndeb-src http://mirrors.163.com/ubuntu/ trusty-proposed main restricted universe multiverse\ndeb-src http://mirrors.163.com/ubuntu/ trusty-backports main restricted universe multiverse\ndeb http://nginx.org/packages/ubuntu/ trusty nginx \ndeb-src http://nginx.org/packages/ubuntu/ trusty nginx\n' > /etc/apt/sources.list && \
  echo "deb http://ppa.launchpad.net/ondrej/php5-5.6/ubuntu trusty main" > /etc/apt/sources.list.d/ondrej-php5-5_6-trusty.list && \
  apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 4F4EA0AAE5267A6C && \
  apt-key adv --keyserver keyserver.ubuntu.com --recv-keys ABF5BD827BD9BF62 && \
  apt-get update && \
  apt-get -y upgrade && \
  apt-get install -y build-essential openssh-server openssh-client && \
  apt-get install -y software-properties-common && \
  apt-get install -y curl git htop man unzip vim wget && \
  wget -c http://nginx.org/keys/nginx_signing.key &&apt-key add nginx_signing.key && \
  apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 4F4EA0AAE5267A6C && \
  apt-get update && \
  apt-get -y upgrade && \
  apt-get install -y php5-cli php5-fpm php5-mysql php5-pgsql php5-sqlite php5-curl php5-gd php5-mcrypt php5-intl php5-imap php5-tidy php*-pear php5-odbc php5-mhash libmcrypt* libmcrypt-dev php5-common php5-ps php5-json php5-dev libcurl3-openssl-dev php5-imagick php5-memcache php5-pspell php5-recode php5-xmlrpc php5-xsl php5-mongo php5-redis libevent-dev && \
  pecl install swoole && \
  pecl install channel://pecl.php.net/libevent-0.1.0 && \
  apt-get install -y supervisor nginx mysql-server mysql-client && \
  apt-get clean && \
  rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
 # mysql config
ADD ./my.cnf /etc/mysql/my.cnf
# nginx config
ADD ./nginx.conf /etc/nginx/nginx.conf
ADD ./fastcgi_params /etc/nginx/fastcgi_params
ADD ./default.conf /etc/nginx/conf.d/default.conf
RUN chmod 755 -R /usr/share/nginx
RUN chown nginx:nginx -R /usr/share/nginx
#php config
ADD ./php.ini /etc/php5/fpm/php.ini
ADD ./php.ini /etc/php5/cli/php.ini
ADD ./php-fpm.conf /etc/php5/fpm/php-fpm.conf
ADD ./www.conf /etc/php5/fpm/pool.d/www.conf

#add src
COPY src/ /usr/share/nginx/www/

# Supervisor Config
ADD ./supervisord.conf /etc/supervisord.conf
ADD ./start.sh /start.sh
RUN chmod 755 /start.sh
# private expose
EXPOSE 3306
EXPOSE 80
# volume for mysql database and wordpress install
VOLUME ["/var/lib/mysql", "/usr/share/nginx/www"]
CMD ["/bin/bash", "/start.sh"]
