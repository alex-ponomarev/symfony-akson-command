# Сервис "Каталог"

 *Порядок запуска*

    1. Cоздать сеть (запустить create-network.sh)
    2. Запустить контейнеры (docker-compose.yml): docker-compose up -d
    3. Произвести миграцию БД: docker exec -ti basic_service php bin/console doctrine:migrations:migrate
    4. Инициализировать заполнение БД тестовыми данными: docker exec -ti basic_service php bin/console doctrine:fixtures:load 
    5. В файле .env установить актуальные значение URL для констант хранящих адреса сервиса Каталог и сервиса Продукты
    6. Ознакомиться с документацией, доступна по url адресу /api/doc
    7. Для использования API сервиса необходима авторизация. 
       Авторизация доступна в документации: логин/пароль akson/akson
    8. Запустить тесты в терминале: docker exec -ti basic_service php bin/phpunit --bootstrap vendor/autoload.php tests
