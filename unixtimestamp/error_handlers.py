"""Unix Timestamp error handlers."""

import os

from flask import Blueprint, g, render_template, request

import unixtimestamp  # pylint:disable=cyclic-import

blueprint = Blueprint(__name__, __name__)


@blueprint.errorhandler(404)
def page_not_found(error):  # pylint:disable=unused-argument
    """Page not found."""
    template = "404 error triggered by %s request to %s, path=%s."
    unixtimestamp.logger.info(template, request.method, request.url, request.path)
    return (
        render_template(
            "page_not_found.html", ga_tracking_id=os.environ.get("GA_TRACKING_ID")
        ),
        404,
    )


@blueprint.errorhandler(500)
def server_error(error):  # pylint:disable=unused-argument
    """Server error."""
    return (
        render_template(
            "server_error.html",
            event_id=g.sentry_event_id,
            public_dsn=unixtimestamp.sentry.client.get_public_dsn("https"),
        ),
        500,
    )
