"""
Unix Timestamp Flask application.
"""

import os
from datetime import datetime

from flask import Flask, render_template, request, redirect, url_for, abort
from pytz import utc

app = Flask(__name__)


@app.route('/<int:timestamp>')
def show_timestamp(timestamp):
    """Display a timestamp."""
    locale = request.headers.get('Accept-Language', 'en-US')
    return render_template('timestamp.html',
                           timestamp=timestamp,
                           datetime=datetime.fromtimestamp(timestamp),
                           locale=locale)


@app.route('/-<int:negative_timestamp>')
def show_negative_timestamp(negative_timestamp):
    """Display a negative timestamp (i.e. one before the epoch)."""
    return show_timestamp(negative_timestamp * -1)


@app.route('/<int:year>/<int:month>')
@app.route('/<int:year>/<int:month>/<int:day>')
@app.route('/<int:year>/<int:month>/<int:day>/<int:hour>')
@app.route('/<int:year>/<int:month>/<int:day>/<int:hour>/<int:minute>')
@app.route(
    '/<int:year>/<int:month>/<int:day>/<int:hour>/<int:minute>/<int:second>')
# pylint:disable=too-many-arguments
def redirect_to_timestamp(year, month, day=1, hour=0, minute=0, second=0):
    """Display the current timestamp."""
    try:
        timestamp = datetime(year=year, month=month, day=day, hour=hour,
                             minute=minute, second=second, tzinfo=utc)
    except ValueError:
        abort(404)

    url = url_for('show_timestamp', timestamp=timestamp.timestamp())
    return redirect(url, code=301)


@app.route('/usage')
def show_usage():
    """Display usage information."""
    return render_template('usage.html')


if __name__ == '__main__':
    app.debug = bool(os.environ.get("DEBUG", True))
    app.run(host='0.0.0.0', port=int(os.environ.get("PORT", 5000)))
