## Сборка и запуск проекта
```shell
docker compose build
```

```shell
docker compose up -d
```

```shell
docker exec -it systemeio-test-app composer install
```

```shell
docker exec -it systemeio-test-app bin/console doctrine:migrations:migrate
docker exec -it systemeio-test-app bin/console doctrine:fixtures:load
```

## Тесты
```shell
docker exec -it systemeio-test-app bin/phpunit
```


