"""Tests for showing timestamps."""


def test_default_locale(client, config):
    """Test default locale is set and passed into template."""
    config.update({"DEFAULT_LOCALE": "fr-FR"})
    response = client.get("/123456789")
    assert "November" not in response.get_data(as_text=True)
    assert "novembre" in response.get_data(as_text=True)


def test_client_locale(client):
    """Test client locale is set and passed into template."""
    response = client.get("/123456789", headers={"Accept-Language": "fr-FR"})
    assert "November" not in response.get_data(as_text=True)
    assert "novembre" in response.get_data(as_text=True)
