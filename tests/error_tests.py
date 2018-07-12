"""Tests for error handling."""

from unittest.mock import patch

import unixtimestamp
from tests import captured_templates, TestCase


class NotFoundTestCase(TestCase):
    """Test for 404 handler."""

    def test_not_found(self):
        """Test for 404 handler."""
        with captured_templates(unixtimestamp.app) as templates:
            response = self.app.get('/blahblahblah', follow_redirects=True)
            self.assertEqual(404, response.status_code)
            self.assertEqual(1, len(templates))
            template = templates[0][0]
            self.assertEqual('page_not_found.html', template.name)


class ServerErrorTestCase(TestCase):
    """Test for 500 handler."""

    def setUp(self):
        """Disable Flask testing mode so error handlers are used."""
        super().setUp()
        unixtimestamp.app.testing = False

    def tearDown(self):
        """Re-enable Flask testing mode."""
        unixtimestamp.app.testing = True
        super().tearDown()

    @patch('unixtimestamp.views.parse_accept_language')
    def test_server_error(self, mock_parse_accept_language):
        """Test for 500 handler."""
        mock_parse_accept_language.side_effect = RuntimeError()
        with captured_templates(unixtimestamp.app) as templates:
            # HACK: As we've disabled testing mode, flask-sslify will redirect
            # requests to https. Must pretend to be an HTTPS request.
            response = self.app.get('/123456789',
                                    headers={'X-Forwarded-Proto': 'https'})
            self.assertEqual(500, response.status_code)
            self.assertEqual(1, len(templates))
            template = templates[0][0]
            self.assertEqual('server_error.html', template.name)
