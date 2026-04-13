compose := docker-compose -f docker-compose.yml

up: 
	$(MAKE) system-up

down: 
	$(MAKE) system-down

build: 
	$(MAKE) system-build

restart: 
	$(MAKE) system-restart

system-up:
	$(compose) up -d

system-build:
	$(compose) up -d --build

system-down:
	$(compose) down

system-restart:
	$(compose) down && $(compose) up -d

ssh:
	$(compose) exec -u application web bash

ssh-root:
	$(compose) exec web bash

ssh_mysql:
	$(compose) exec mysql bash

fix-host:
	sudo sh .bin/fix_hostnames.sh

# Run artisan as www-data
art:
	$(compose) exec -u www-data web php artisan

art1:
	docker-compose -f docker-compose.yml exec -u application web php artisan $(filter-out $@,$(MAKECMDGOALS))

fix-perms:
	$(compose) exec web bash -c "chmod -R 775 storage bootstrap/cache database/migrations"

optimize:
	$(compose) exec -u www-data web php artisan config:cache && \
	$(compose) exec -u www-data web php artisan route:cache && \
	$(compose) exec -u www-data web php artisan view:cache

# Queue management
queue:
	$(compose) up -d queue

queue-restart:
	$(compose) restart queue

queue-logs:
	$(compose) logs -f queue

queue-stop:
	$(compose) stop queue

# Scheduler management
scheduler:
	$(compose) up -d scheduler

scheduler-restart:
	$(compose) restart scheduler

scheduler-logs:
	$(compose) logs -f scheduler

scheduler-stop:
	$(compose) stop scheduler

# Mailpit
mail:
	$(compose) up -d mailpit

mail-stop:
	$(compose) stop mailpit

# View all logs
logs:
	$(compose) logs -f

# Database commands
fresh:
	$(compose) exec -u application web php artisan migrate:fresh --seed

# Status check
status:
	$(compose) ps