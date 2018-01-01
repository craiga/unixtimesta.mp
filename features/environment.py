"""Behavioural testing environment."""

from unixtimestamp import app


def before_feature(context, feature):  # pylint:disable=unused-argument
    """Add the Flask testing client to the context object."""
    app.testing = True
    # Debug enabled in tests as a workaround for
    # https://github.com/kennethreitz/flask-sslify/issues/50.
    app.debug = True
    context.client = app.test_client()
