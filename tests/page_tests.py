"""Tests for miscellaneous pages."""

import pytest


@pytest.mark.parametrize(
    "url, content_type_prefix, content",
    [
        ("/usage", "text/html", None),
        ("/humans.txt", "text/plain", "Craig Anderson"),
        (
            "/robots.txt",
            "text/plain",
            "Sitemap: https://www.unixtimesta.mp/sitemapindex.xml",
        ),
        ("/favicon.ico", "image/", None),
    ],
)
def test_get(client, url, content_type_prefix, content):
    """Test for usage information."""
    response = client.get(url)
    assert response.status_code == 200
    assert response.content_type.startswith(content_type_prefix)
    if content:
        assert content in response.get_data(as_text=True)
