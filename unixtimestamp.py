"""
Unix Timestamp Flask application.
"""

import os
from datetime import datetime

from flask import Flask, render_template, request

app = Flask(__name__)


@app.route('/<int:timestamp>')
def show_timestamp(timestamp):
    """Display the current timestamp."""
    locale = request.headers.get('Accept-Language', 'en-US')
    return render_template('timestamp.html',
                           timestamp=timestamp,
                           datetime=datetime.fromtimestamp(timestamp),
                           locale=locale)


if __name__ == '__main__':
    app.debug = bool(os.environ.get("DEBUG", True))
    app.run(host='0.0.0.0', port=int(os.environ.get("PORT", 5000)))
