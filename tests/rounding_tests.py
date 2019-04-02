"""Tests for redirection."""

from urllib.parse import urlparse

import pytest


@pytest.mark.parametrize("url", ["/123.123", "/123.987"])
def test_redirect(client, url):
    """Test redirecting requests for with decimal points."""
    response = client.get(url)
    assert response.status_code == 302
    assert urlparse(response.location).path == "/123"
