Code for the web site [https://www.unixtimesta.mp/](https://www.unixtimesta.mp/).


# Getting Started

To run the site locally, create a local virtual environment (you can do this using `make pyenv-virtualenv` if you use [pyenv-virtualenv](https://github.com/pyenv/pyenv-virtualenv)), and then run:

- `make .env` to write a default `.env` file;
- `pip install --requirement requirements.txt` to install Python packages;
- `npm install` to install JavaScript packages;
- `npm run build` to build the frontend assets; and
- `flask run --debug` to start the website.


If you're using macOS, you might need to run Flask with the `--port n` option as well. [This is because macOS uses Flask's preferred port (5000) for something else](https://stackoverflow.com/questions/69818376/localhost5000-unavailable-in-macos-v12-monterey).


# Credits

Icon adapted from http://openclipart.org/detail/192402/clock-icon-by-cinemacookie-192402.


# Reporting Issues

[Report bugs, issues and requests through GitHub.](https://github.com/craiga/unixtimesta.mp/issues)
