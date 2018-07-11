"""Unix Timestamp Flask application."""

import logging
import os
import re
import sys
from datetime import datetime

import flask
from flask import (Flask, render_template, request, redirect, url_for, abort,
                   make_response, g)
from pytz import utc
from dateutil.parser import parse
from raven.contrib.flask import Sentry
from flask_sslify import SSLify

app = Flask(__name__, static_url_path='')
app.config.from_object('config')

# Workaround for
# https://github.com/PyCQA/pylint/issues/1061#issuecomment-393858322
logger = flask.logging.create_logger(app)

logger.addHandler(logging.StreamHandler(sys.stdout))
logger.setLevel(logging.getLevelName(app.config.get('LOG_LEVEL')))

SSLify(app)

# Sentry DSN should be configured by setting SENTRY_DSN environment variable.
# Other configuration is done in app.config.SENTRY_CONFIG.
sentry = Sentry(app, logging=True,
                level=logging.getLevelName(app.config.get('LOG_LEVEL')))

from unixtimestamp import views, error_handlers  # noqa: E402 pylint:disable=wrong-import-position
