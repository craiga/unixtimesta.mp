"""
unixtimesta.mp Flask application.
"""

import os
from flask import Flask

app = Flask(__name__)


@app.route('/')
def hello():
    """A simple "Hello, world!" response to get started."""
    return "Hello world!"


if __name__ == '__main__':
    app.run(host='0.0.0.0', port=int(os.environ.get("PORT", 5000)))
