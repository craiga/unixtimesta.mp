"""Tests for content types."""

from tests import TestCase


class ContentTypeTextMixin:
    """Mixin for content type tests."""

    def test_content_type(self):
        """Test expected content type is returned when in header."""
        for content_type in [self.CONTENT_TYPE] + self.ALIAS_CONTENT_TYPES:
            response = self.app.get("/123456", headers={"Accept": content_type})
            resp_ct = response.content_type
            self.assertTrue(resp_ct.startswith(self.CONTENT_TYPE))


class HtmlTestCase(ContentTypeTextMixin, TestCase):
    """Tests for HTML content type."""

    CONTENT_TYPE = "text/html"
    ALIAS_CONTENT_TYPES = ["application/xhtml+xml"]
    OTHER_CONTENT_TYPE = "application/json"

    def test_default_content_type(self):
        """Test that HTML is the default content type."""
        response = self.app.get("/123456", headers={"Accept": ""})
        self.assertTrue(response.content_type.startswith(self.CONTENT_TYPE))


class JsonTestCase(ContentTypeTextMixin, TestCase):
    """Tests for JSON content type."""

    CONTENT_TYPE = "application/json"
    ALIAS_CONTENT_TYPES = []
    OTHER_CONTENT_TYPE = "text/html"
