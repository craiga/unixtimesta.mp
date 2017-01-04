"""Configuration for Unix Timestamp Flask application."""

import os


SERVER_NAME = os.environ.get('SERVER_NAME')
if not SERVER_NAME:
    if os.environ.get('HEROKU_APP_NAME'):
        SERVER_NAME = '{}.{}'.format(os.environ.get('HEROKU_APP_NAME'),
                                     'herokuapp.com')

SITEMAP_DEFAULT_START = 0
SITEMAP_DEFAULT_SIZE = 20000


SITEMAP_INDEX_DEFAULT_START = -1000
SITEMAP_INDEX_DEFAULT_SIZE = 20000