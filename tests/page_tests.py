"""Tests for miscellaneous pages."""

import unixtimestamp
from tests import TestCase


class UsageTestCase(TestCase):
    """Test for usage information."""

    def test_usage(self):
        """Test for usage information."""
        response = self.app.get("/usage", follow_redirects=True)
        self.assertEqual(200, response.status_code)


class HumansTestCase(TestCase):
    """Test for humans.txt."""

    def test_humans_txt(self):
        """Test for humans.txt."""
        with self.app.get("/humans.txt", follow_redirects=True) as response:
            self.assertEqual(200, response.status_code)
            self.assertRegex(response.content_type, "^text/plain")
            self.assertIn(b"Craig Anderson", response.data)


class RobotsTestCase(TestCase):
    """Tests for robots.txt."""

    def test_robots_txt(self):
        """Test for robots.txt."""
        with self.app.get("/robots.txt", follow_redirects=True) as response:
            self.assertEqual(200, response.status_code)
            self.assertRegex(response.content_type, "^text/plain")
            self.assertIn(
                b"Sitemap: https://www.unixtimesta.mp/sitemapindex.xml",
                response.data,
            )


class FaviconTestCase(TestCase):
    """Tests for favicon.ico."""

    def test_favicon_ico(self):
        """Test for favicon.ico."""
        with self.app.get("/favicon.ico", follow_redirects=True) as response:
            self.assertEqual(200, response.status_code)
            self.assertRegex(response.content_type, "^image/.*icon")
