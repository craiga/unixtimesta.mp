.PHONY: help
help: ## Display this help screen.
	@grep -E '^\S.+:.*?## .*$$' Makefile | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: pyenv-virtualenv
pyenv-virtualenv:  ## Create a virtual environment managed by pyenv-virtualenv.
	pyenv install --skip-existing `cat runtime.txt | sed "s/python-//"`
	pyenv virtualenv `cat runtime.txt | sed "s/python-//"` unixtimesta.mp
	echo "unixtimesta.mp" > .python-version

.PHONY: pyenv-virtualenv-delete
pyenv-virtualenv-delete:  ## Delete a virtual environment managed by pyenv-virtualenv.
	pyenv virtualenv-delete --force `cat .python-version || echo unixtimesta.mp`
	rm -f .python-version

.env:  ## Create .env file suitable for development.
	printf "FLASK_APP=unixtimestamp\n# SENTRY_DSN=\nSENTRY_ENVIRONMENT=`whoami`\n" > .env
