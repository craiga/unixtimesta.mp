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
        unixtimestamp.app.debug = False
        unixtimestamp.app.testing = True
        self.app = unixtimestamp.app.test_client()
