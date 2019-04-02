"""Tests for redirection on POST request."""

from urllib.parse import urlparse


def test_post_redirect(client):
    """Test redirecting post requests."""
    response = client.post("/", data={"time": "foobar"})
    assert response.status_code == 302
    assert urlparse(response.location).path == "/foobar"
