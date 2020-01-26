"""Unix Timestamp Flask application."""

import logging
import os
import re
import sys
from datetime import datetime

import flask

from dateutil.parser import parse
from flask_talisman import Talisman
from pytz import utc
from raven.contrib.flask import Sentry

from unixtimestamp import error_handlers, views


def create_app():
    """Create the Unix Timestamp Flask application."""
    the_app = flask.Flask(__name__, static_url_path="")
    the_app.config.from_object("config")
    the_app.register_blueprint(views.blueprint)
    the_app.register_blueprint(error_handlers.blueprint)
    return the_app


app = create_app()

# Workaround for
# https://github.com/PyCQA/pylint/issues/1061#issuecomment-393858322
logger = flask.logging.create_logger(app)

logger.addHandler(logging.StreamHandler(sys.stdout))
logger.setLevel(logging.getLevelName(app.config.get("LOG_LEVEL")))

Talisman(
    app,
    content_security_policy={
        "style-src": [
            "'self'",
            "'unsafe-inline'",
            "maxcdn.bootstrapcdn.com",
            "fonts.googleapis.com",
            "fonts.gstatic.com",
        ],
        "script-src": [
            "'self'",
            "'unsafe-inline'",
            "code.jquery.com",
            "cdn.ravenjs.com",
            "cdn.usefathom.com",
        ],
        "img-src": ["camo.githubusercontent.com"],
    },
)

# Sentry DSN should be configured by setting SENTRY_DSN environment variable.
# Other configuration is done in app.config.SENTRY_CONFIG.
sentry = Sentry(
    app, logging=True, level=logging.getLevelName(app.config.get("LOG_LEVEL"))
)
