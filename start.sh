#!/bin/bash

docker-compose up -d
ip=$(docker inspect nginx_php_mysql_mysql_1 | python3 -c "import sys, json; print(json.load(sys.stdin)[0]['NetworkSettings']['Networks']['nginx_php_mysql_backend']['IPAddress'])")
echo "$ip;
$(tail -n3 db_config)" > db_config
docker restart nginx_php_mysql_php_1
cd proxy
docker-compose up -d

