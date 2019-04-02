"""Tests for Unix Timestamp Flask application."""


import pytest

import unixtimestamp


@pytest.fixture
def app():
    """Configure the app for testing."""
    the_app = unixtimestamp.create_app()
    the_app.testing = True
    return the_app
