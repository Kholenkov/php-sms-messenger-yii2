# Тестовое приложение SMS messenger

## Установка

~~~
cd /var/www/
sudo mkdir sms-messenger-yii2
sudo chown www-data sms-messenger-yii2/
cd sms-messenger-yii2/

git clone https://github.com/Kholenkov/php-sms-messenger-yii2.git .
~~~

## Запуск через Docker

~~~
cd /var/www/sms-messenger-yii2/
cp .env.docker-example .env
cd app/
cp .env.docker-example .env
# Указать путь для данных MySQL.

cd ..

docker-compose build
docker-compose up -d
docker-compose exec sms-messenger-yii2-php-fpm php src/yii migrate --interactive=0
~~~
