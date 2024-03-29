"""Configuration for Unix Timestamp Flask application."""

import os

SERVER_NAME = os.environ.get("SERVER_NAME")
if not SERVER_NAME:
    if heroku_app_name := os.environ.get("HEROKU_APP_NAME"):
        SERVER_NAME = f"{heroku_app_name}.herokuapp.com"

SITEMAP_DEFAULT_START = os.environ.get("SITEMAP_DEFAULT_START", 0)
SITEMAP_DEFAULT_SIZE = os.environ.get("SITEMAP_DEFAULT_SIZE", 1000)
SITEMAP_MAX_SIZE = os.environ.get("SITEMAP_MAX_SIZE", 1000)

SITEMAP_INDEX_DEFAULT_START = os.environ.get("SITEMAP_DEFAULT_START", 0)
SITEMAP_INDEX_DEFAULT_SIZE = os.environ.get("SITEMAP_INDEX_DEFAULT_SIZE", 1000)

ROBOTS_SITEMAP_INDEX_DEFAULT_START = os.environ.get(
    "ROBOTS_SITEMAP_INDEX_DEFAULT_START", -40000
)
ROBOTS_SITEMAP_INDEX_DEFAULT_SIZE = os.environ.get(
    "ROBOTS_SITEMAP_INDEX_DEFAULT_SIZE", 1000
)

SENTRY_CONFIG = {"release": os.environ.get("HEROKU_SLUG_COMMIT")}

LOG_LEVEL = os.environ.get("LOG_LEVEL", "INFO")

DEFAULT_LOCALE = os.environ.get("DEFAULT_LOCALE", "en-US")
