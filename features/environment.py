"""Behavioural testing environment."""

import os
import sys
import tempfile

from unixtimestamp import app


def before_feature(context, feature):
    """Add the Flask testing client to the context object."""
    app.config['TESTING'] = True
    context.client = app.test_client()
