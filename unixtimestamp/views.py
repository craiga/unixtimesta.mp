"""Unix Timestamp Flask application."""

import locale
import math
import os
from datetime import datetime

import flask
from flask import request

from dateutil.parser import parse
from flask_accept import accept_fallback
from pytz import utc

import unixtimestamp  # pylint:disable=cyclic-import

blueprint = flask.Blueprint("views", __name__)


def render_timestamp_html(**kwargs):
    """Render a timestamp in HTML."""
    ga_tracking_id = os.environ.get("GA_TRACKING_ID")
    sentry_public_dsn = os.environ.get("SENTRY_PUBLIC_DSN")

    language = request.accept_languages.best
    if not language:
        language = flask.current_app.config.get("DEFAULT_LOCALE")

    return flask.render_template(
        "timestamp.html",
        locale=language,
        ga_tracking_id=ga_tracking_id,
        sentry_public_dsn=sentry_public_dsn,
        **kwargs,
    )


def render_timestamp(timestamp, renderer):
    """Render a timestamp."""
    language = request.accept_languages.best
    if not language:
        language = flask.current_app.config.get("DEFAULT_LOCALE")

    try:
        locale.setlocale(locale.LC_ALL, (language.replace("-", "_"), "UTF-8"))
    except locale.Error:
        unixtimestamp.logger.warning("Failed setting locale to %s UTF-8", language)

    try:
        timestamp_datetime = datetime.utcfromtimestamp(timestamp)
        timestamp_datetime = utc.localize(timestamp_datetime)
        return renderer(timestamp=timestamp, datetime=timestamp_datetime)
    except (ValueError, OverflowError, OSError):
        unixtimestamp.logger.info("Triggering a 404 error.", exc_info=True)
        return renderer(timestamp=timestamp), 404


@blueprint.route("/<int:timestamp>")
@accept_fallback
def show_timestamp(timestamp):
    """Display a timestamp as HTML."""
    return render_timestamp(timestamp, renderer=render_timestamp_html)


@show_timestamp.support("application/json")
def show_timestamp_json(timestamp):
    """Display a timestamp as JSON."""
    return render_timestamp(timestamp, renderer=flask.jsonify)


@blueprint.route("/-<int:negative_timestamp>")
def show_negative_timestamp(negative_timestamp):
    """Display a negative timestamp (i.e. one before the epoch)."""
    return show_timestamp(negative_timestamp * -1)


@blueprint.route("/<int:year>/<int:month>")
@blueprint.route("/<int:year>/<int:month>/<int:day>")
@blueprint.route("/<int:year>/<int:month>/<int:day>/<int:hour>")
@blueprint.route("/<int:year>/<int:month>/<int:day>/<int:hour>/<int:minute>")
@blueprint.route(
    "/<int:year>/<int:month>/<int:day>/<int:hour>/<int:minute>/<int:second>"
)
# pylint:disable=too-many-arguments
def redirect_to_timestamp(year, month, day=1, hour=0, minute=0, second=0):
    """
    Redirect to a timestamp based on the components in the URL.

    Only year and month are required; year, month, day, hour, minute and second
    are supported.
    """
    try:
        timestamp = datetime(
            year=year,
            month=month,
            day=day,
            hour=hour,
            minute=minute,
            second=second,
            tzinfo=utc,
        )
    except (ValueError, OverflowError):
        unixtimestamp.logger.info("Triggering a 404 error.", exc_info=True)
        flask.abort(404)

    url = flask.url_for("views.show_timestamp", timestamp=timestamp.timestamp())
    return flask.redirect(url, code=301)


@blueprint.route("/<float:timestamp>")
def redirect_to_rounded_timestamp(timestamp):
    """Redirect to a rounded timestamp."""
    url = flask.url_for("views.show_timestamp", timestamp=math.floor(timestamp))
    return flask.redirect(url, code=302)


@blueprint.route("/usage")
def show_usage():
    """Display usage information."""
    return flask.render_template(
        "usage.html", ga_tracking_id=os.environ.get("GA_TRACKING_ID")
    )


def make_streamed_response(template, content_type, **context):
    """Make a stream."""
    app = flask.current_app
    app.update_template_context(context)
    tpl = app.jinja_env.get_template(template)  # pylint: disable=no-member
    stream = tpl.stream(context)
    response = flask.Response(stream)
    response.headers["Content-Type"] = content_type
    return response


@blueprint.route("/sitemap.xml")
def sitemap():
    """Display sitemap XML."""
    config = flask.current_app.config
    start = int(request.args.get("start", config.get("SITEMAP_DEFAULT_START")))
    max_size = int(config.get("SITEMAP_MAX_SIZE"))
    size = min(
        int(request.args.get("size", config.get("SITEMAP_DEFAULT_SIZE"))), max_size
    )
    return make_streamed_response(
        "sitemap.xml", "application/xml", timestamps=range(start, start + size)
    )


@blueprint.route("/sitemapindex.xml")
def sitemap_index():
    """Display sitemap index XML."""
    config = flask.current_app.config

    # Get the first timestamp to display in the first sitemap
    first_sitemap_start = int(
        request.args.get("start", config.get("SITEMAP_INDEX_DEFAULT_START"))
    )

    # Get the size of each sitemap
    sitemap_size = int(
        request.args.get("sitemap_size", config.get("SITEMAP_DEFAULT_SIZE"))
    )

    # Get the number of sitemaps to include
    size = int(request.args.get("size", config.get("SITEMAP_INDEX_DEFAULT_SIZE")))

    # Calculate a list of sitemap start timestamps
    last_sitemap_start = first_sitemap_start + (sitemap_size * size)
    sitemap_starts = range(first_sitemap_start, last_sitemap_start, sitemap_size)

    # Render the sitemap index
    return make_streamed_response(
        "sitemapindex.xml",
        "application/xml",
        sitemap_starts=sitemap_starts,
        sitemap_size=sitemap_size,
    )


@blueprint.route("/robots.txt")
def robots():
    """Show robots.txt."""
    config = flask.current_app.config
    # Calculate sitemap index starts
    first_index = int(config.get("ROBOTS_SITEMAP_INDEX_DEFAULT_START"))
    robots_size = int(config.get("ROBOTS_SITEMAP_INDEX_DEFAULT_SIZE"))
    index_size = int(config.get("SITEMAP_INDEX_DEFAULT_SIZE"))
    sitemap_size = int(config.get("SITEMAP_DEFAULT_SIZE"))
    last_index = first_index + (robots_size * index_size * sitemap_size)
    sitemap_starts = range(first_index, last_index, (index_size * sitemap_size))

    # Render the sitemap index
    return make_streamed_response(
        "robots.txt",
        "text/plain",
        sitemap_starts=sitemap_starts,
        sitemap_size=sitemap_size,
        sitemap_index_size=index_size,
    )


@blueprint.route("/<string:datetime_string>")
def redirect_to_timestamp_string(datetime_string):
    """Redirect to a timestamp based on the given description of a datetime."""
    try:
        timestamp = parse(datetime_string, fuzzy=True)
    except (ValueError, OverflowError):
        unixtimestamp.logger.info("Triggering a 404 error.", exc_info=True)
        flask.abort(404)

    if timestamp.tzinfo is None:
        timestamp = utc.localize(timestamp)

    url = flask.url_for("views.show_timestamp", timestamp=timestamp.timestamp())
    return flask.redirect(url, code=302)


@blueprint.route("/", methods=["POST"])
def handle_post():
    """Handle post request."""
    return flask.redirect(f"/{request.form.get('time')}")


@blueprint.route("/")
@blueprint.route("/now")
def redirect_to_now():
    """Redirect to current timestamp."""
    url = flask.url_for("views.show_timestamp", timestamp=datetime.now().timestamp())
    return flask.redirect(url, code=302)


@blueprint.route("/humans.txt")
def humans():
    """Show humans.txt."""
    return flask.current_app.send_static_file("humans.txt")


@blueprint.route("/favicon.ico")
def favicon():
    """Show favicon.ico."""
    return flask.current_app.send_static_file("favicon.ico")
