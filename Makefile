.PHONY: build up down ps status logs shell restart pint larastan test install migrate post-install generate_key wait seed

install:
	docker run --rm \
		-v $(shell pwd):/var/www/html \
		-w /var/www/html \
		composer:latest \
		composer install --ignore-platform-reqs --prefer-dist --no-ansi --no-interaction --no-progress --no-scripts

	@if [ ! -f .env ]; then \
		cp .env.example .env; \
	else \
		echo ".env already exists"; \
	fi

post-install: build up generate_key wait migrate seed

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

restart:
	docker compose restart

pint:
	docker exec -it laravel_php ./vendor/bin/pint

larastan:
	docker exec -it laravel_php ./vendor/bin/phpstan --memory-limit=2G

test:
	docker exec -it laravel_php php artisan test

wait:
	@echo "Waiting for MySQL to start..."
	@sleep 10

migrate:
	docker exec -it laravel_php php artisan migrate

generate_key:
	docker exec -it laravel_php php artisan key:generate

seed:
	docker exec -it laravel_php php artisan db:seed
