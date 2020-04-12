Code for the web site [https://www.unixtimesta.mp/](https://www.unixtimesta.mp/).

[![Build Status](https://img.shields.io/circleci/build/github/craiga/unixtimesta.mp)](https://circleci.com/gh/craiga/unixtimesta.mp) ![Security Headers](https://img.shields.io/security-headers?url=https%3A%2F%2Fwww.unixtimesta.mp%2F)


# Getting Started for Development

Set up the project by creating a Pipenv virtual environment.

    pipenv install --dev

Install the required NPM packages and build the client-side assets (note that a Sass build is done automatically as part of the install).

    npm install

Finally, run the the Flask development server and access the application in a web browser at [http://localhost:5000](http://localhost:5000).

    FLASK_DEBUG=1 pipenv run flask run

If you make changes to the SCSS files, you can rebuild the CSS using the following command:

    npm run sass -- scss/:unixtimestamp/static/css/


## Ensuring Code Quality

Code is formatted with [black](https://black.readthedocs.io/en/latest/):

     pipenv run black .

Run the test suite using [pytest](https://pytest.org/) and [behave](http://behave.readthedocs.io/en/latest/).

    pipenv run pytest
    pipenv run behave

Test code quality with [pylint](https://www.pylint.org).

    find . -iname "*.py" | xargs pipenv run pylint


# Credits

Icon adapted from http://openclipart.org/detail/192402/clock-icon-by-cinemacookie-192402.


# Reporting Issues

[Report bugs, issues and requests through GitHub.](https://github.com/craiga/unixtimesta.mp/issues)
