"""Tests for miscellaneous pages."""

from tests import TestCase


class UsageTestCase(TestCase):
    """Test for usage information."""

    def test_usage(self):
        """Test for usage information."""
        response = self.app.get('/usage')
        self.assertEqual(200, response.status_code)


class HumansTestCase(TestCase):
    """Test for humans.txt."""

    def test_humans_txt(self):
        """Test for humans.txt."""
        with self.app.get('/humans.txt') as response:
            self.assertEqual(200, response.status_code)
            self.assertRegex(response.content_type, '^text/plain')
            self.assertIn(b'Craig Anderson', response.data)


class RobotsTestCase(TestCase):
    """Tests for robots.txt."""

    def test_robots_txt(self):
        """Test for robots.txt."""
        with self.app.get('/robots.txt') as response:
            self.assertEqual(200, response.status_code)
            self.assertRegex(response.content_type, '^text/plain')
            self.assertIn(b'Sitemap: http://localhost/sitemapindex.xml',
                          response.data)
