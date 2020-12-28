# Сервис "Каталог"

 *Порядок запуска*

    1. Cоздать сеть (запустить create-network.sh)
    2. Запустить контейнеры (docker-compose.yml)
    3. Произвести миграцию БД: docker exec -ti basic_service php bin/console doctrine:migrations:migrate
    4. Инициализировать заполнение БД тестовыми данными: docker exec -ti basic_service php bin/console doctrine:fixtures:load 
    5. Ознакомиться с документацией, доступна по url адресу /api/doc
    6. Для использования API сервиса необходима авторизация. 
       Авторизация доступна в документации: логин/пароль akson/akson
    7. Запустить тесты в терминале: docker exec -ti basic_service php bin/phpunit --bootstrap vendor/autoload.php tests
