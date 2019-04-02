"""Tests for content types."""


import pytest


def test_default_content_type(client):
    """Test default content type."""
    response = client.get("/123456")
    assert response.content_type.startswith("text/html")


@pytest.mark.parametrize(
    "content_type", ["text/html", "application/xhtml+xml", "umm/what"]
)
def test_html_content_type(client, content_type):
    """Test HTML content type."""
    response = client.get("/123456", headers={"Accept": content_type})
    assert response.content_type.startswith("text/html")


def test_json_content_type(client):
    """Test JSON content type."""
    response = client.get("/123456", headers={"Accept": "application/json"})
    assert response.content_type.startswith("application/json")
