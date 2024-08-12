.PHONY: build up down ps status logs shell restart

build:
	docker compose build

up:
	docker compose up -d

down:
	docker compose down

ps:
	docker compose ps

logs:
	docker compose logs -f

shell:
	docker exec -it laravel_php /bin/bash

restart: down up

pint:
	docker exec -it laravel_php ./vendor/bin/pint

larastan:
	docker exec -it laravel_php ./vendor/bin/phpstan --memory-limit=2G

test:
	docker exec -it laravel_php php artisan test
