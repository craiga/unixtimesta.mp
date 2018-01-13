"""Tests for error handling."""

import unixtimestamp
from tests import captured_templates, TestCase


class NotFoundTestCase(TestCase):
    """Test for 404 handler."""

    def test_not_found(self):
        """Test for 404 handler."""
        with captured_templates(unixtimestamp.app) as templates:
            response = self.app.get('/blahblahblah')
            self.assertEqual(404, response.status_code)
            self.assertEqual(1, len(templates))
            template = templates[0][0]
            self.assertEqual('page_not_found.html', template.name)
