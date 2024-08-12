.PHONY: build up down ps status logs

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
