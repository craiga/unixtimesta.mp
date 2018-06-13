"""Unix Timestamp utility functions."""

import re


def parse_accept_language(accept_language, default_locale):
    """Parse locale from Accept-Language header."""
    try:
        match = re.search(r'^[A-Za-z]{2}(\-[A-Za-z]{2})?', accept_language)
        return match.group(0)
    except (TypeError, AttributeError):
        return default_locale
