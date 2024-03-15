install:
	composer install

install_pact_cli:
	curl -fsSL https://raw.githubusercontent.com/pact-foundation/pact-ruby-standalone/master/install.sh | PACT_CLI_VERSION=v2.0.3 bash

publish_pacts:
	pact/bin/pact-broker publish src/pacts/ --consumer-app-version=$$(git rev-parse --short HEAD) --branch $$(git rev-parse --abbrev-ref HEAD) --broker-base-url=http://localhost:9292/

broker_up:
	docker-compose up -d

broker_down:
	docker-compose down

test:
	vendor/bin/phpunit --verbose --testdox

.PHONY: test
