"""Unix Timestamp Flask application."""

import os
from datetime import datetime

from flask import Flask, render_template, request, redirect, url_for, abort
from pytz import utc
from dateutil.parser import parse

app = Flask(__name__)
app.config.from_object('config')



@app.route('/<int:timestamp>')
def show_timestamp(timestamp):
    """Display a timestamp."""
    locale = request.headers.get('Accept-Language', 'en-US')
    return render_template('timestamp.html',
                           timestamp=timestamp,
                           datetime=datetime.fromtimestamp(timestamp),
                           locale=locale,
                           ga_tracking_id=os.environ.get('GA_TRACKING_ID'))


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
    """
    Redirect to a timestamp based on the components in the URL.

    Only year and month are required; year, month, day, hour, minute and second
    are supported.
    """
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
    return render_template('usage.html',
                           ga_tracking_id=os.environ.get('GA_TRACKING_ID'))


@app.route('/<string:datetime_string>')
def redirect_to_timestamp_string(datetime_string):
    """Redirect to a timestamp based on the given description of a datetime."""
    try:
        timestamp = parse(datetime_string, fuzzy=True)
    except ValueError:
        abort(404)

    url = url_for('show_timestamp', timestamp=timestamp.timestamp())
    return redirect(url, code=302)


@app.route('/', methods=['POST'])
def handle_post():
    """Handle post request."""
    return redirect('/{}'.format(request.form.get('time')))


@app.route('/')
@app.route('/now')
def redirect_to_now():
    """Redirect to current timestamp."""
    url = url_for('show_timestamp', timestamp=datetime.now().timestamp())
    return redirect(url, code=302)


@app.errorhandler(404)
def page_not_found(error):  # pylint:disable=unused-argument
    """Page not found."""
    return (render_template('page_not_found.html',
                            ga_tracking_id=os.environ.get('GA_TRACKING_ID')),
            404)


if __name__ == '__main__':
    app.debug = bool(os.environ.get("DEBUG", False))
    app.run(host='0.0.0.0', port=int(os.environ.get("PORT", 5000)))
