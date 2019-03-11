"""Tests for error handling."""

from unittest.mock import patch

import unixtimestamp
from tests import TestCase, captured_templates


class NotFoundTestCase(TestCase):
    """Test for 404 handler."""

    def test_not_found(self):
        """Test for 404 handler."""
        with captured_templates(unixtimestamp.app) as templates:
            response = self.app.get("/blahblahblah")
            self.assertEqual(404, response.status_code)
            self.assertEqual(1, len(templates))
            template = templates[0][0]
            self.assertEqual("page_not_found.html", template.name)


class ServerErrorTestCase(TestCase):
    """Test for 500 handler."""

    def setUp(self):
        """Disable Flask testing mode so error handlers are used."""
        super().setUp()
        unixtimestamp.app.debug = False
        unixtimestamp.app.testing = False

    def tearDown(self):
        """Re-enable Flask testing mode."""
        unixtimestamp.app.debug = True
        unixtimestamp.app.testing = True
        super().tearDown()

    @patch("unixtimestamp.views.render_timestamp_html")
    def test_server_error(self, mock_render_timestamp_html):
        """Test for 500 handler."""
        mock_render_timestamp_html.side_effect = RuntimeError()
        with captured_templates(unixtimestamp.app) as templates:
            response = self.app.get("/123456789", follow_redirects=True)
            self.assertEqual(500, response.status_code)
            self.assertEqual(1, len(templates))
            template = templates[0][0]
            self.assertEqual("server_error.html", template.name)
