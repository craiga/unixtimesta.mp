"""Unix Timestamp Flask application."""

import logging
import os
import re
import sys
from datetime import datetime

import flask
from pytz import utc
from dateutil.parser import parse
from raven.contrib.flask import Sentry
from flask_talisman import Talisman

app = flask.Flask(__name__, static_url_path="")
app.config.from_object("config")

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
        ],
        "img-src": ["camo.githubusercontent.com"],
    },
)


# Sentry DSN should be configured by setting SENTRY_DSN environment variable.
# Other configuration is done in app.config.SENTRY_CONFIG.
sentry = Sentry(
    app, logging=True, level=logging.getLevelName(app.config.get("LOG_LEVEL"))
)

# pylint: disable=wrong-import-position
from unixtimestamp import views, error_handlers
