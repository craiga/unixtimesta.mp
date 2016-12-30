"""Configuration for Unix Timestamp Flask application."""

import os


SERVER_NAME = os.environ.get('SERVER_NAME')
if not SERVER_NAME:
    if os.environ.get('HEROKU_APP_NAME'):
        SERVER_NAME = '{}.{}'.format(os.environ.get('HEROKU_APP_NAME'),
                                     'herokuapp.com')
