"""Tests for Unix Timestamp Flask application."""

import unittest
from contextlib import contextmanager

from flask import template_rendered

import unixtimestamp


@contextmanager
def captured_templates(app):
    """Capture all templates being rendered along with the context used."""
    recorded = []

    def record(sender, template, context, **extra):
        # pylint:disable=unused-argument
        recorded.append((template, context))

    template_rendered.connect(record, app)

    try:
        yield recorded
    finally:
        template_rendered.disconnect(record, app)


class TestCase(unittest.TestCase):
    """Base test case."""

    def setUp(self):
        """Set up test case."""
        unixtimestamp.app.testing = True
        # Debug enabled in tests as a workaround for
        # https://github.com/kennethreitz/flask-sslify/issues/50.
        unixtimestamp.app.debug = True
        self.app = unixtimestamp.app.test_client()
