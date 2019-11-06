help: ## What you're currently reading
	@IFS=$$'\n' ; \
	help_lines=(`fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##/:/'`); \
	printf "Usage: make [target]\n\n" ; \
	printf "%-30s %s\n" "[target]" "help" ; \
	printf "%-30s %s\n" "--------" "----" ; \
	for help_line in $${help_lines[@]}; do \
		IFS=$$':' ; \
		help_split=($$help_line) ; \
		help_command=`echo $${help_split[0]} | sed -e 's/^ *//' -e 's/ *$$//'` ; \
		help_info=`echo $${help_split[2]} | sed -e 's/^ *//' -e 's/ *$$//'` ; \
		printf '\033[36m'; \
		printf "%-30s %s" $$help_command ; \
		printf '\033[0m'; \
		printf "%s\n" $$help_info; \
	done; \
	printf "\n"; 
.PHONY: help

now: ## run current tests
	php php-cs-fixer.phar fix
	./vendor/bin/phpunit
	php -d memory_limit=-1 ./vendor/bin/phpstan --memory-limit=-1 analyse -l 7 -c phpstan.neon data lib src tests
.PHONY: now

