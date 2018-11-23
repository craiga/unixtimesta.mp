"""Tests for sitemaps and sitemap index."""

from xml.etree import ElementTree

from werkzeug.urls import url_encode

import unixtimestamp
from tests import TestCase


class SitemapTestCase(TestCase):
    """Tests for sitemap requests."""

    XML_NAMESPACE = "http://www.sitemaps.org/schemas/sitemap/0.9"

    def test_sitemap_index(self):
        """Test sitemap index."""
        for start, size, sitemap_size in (
            (0, 10, 10),
            (123, 456, 789),
            (-100000, 10, 10),
        ):
            # Test with values on the query string.
            query_string = url_encode(
                {"start": start, "size": size, "sitemap_size": sitemap_size}
            )
            url = "/sitemapindex.xml?" + query_string
            response = self.app.get(url, follow_redirects=True)
            self.assertEqual(200, response.status_code)
            self.assertEqual("application/xml", response.content_type)
            xml = ElementTree.fromstring(response.data)
            self.assert_sitemap_xml_correct(xml, start, size, sitemap_size)

            # Test with values in configuration.
            config = {
                "SITEMAP_INDEX_DEFAULT_START": start,
                "SITEMAP_INDEX_DEFAULT_SIZE": size,
                "SITEMAP_DEFAULT_SIZE": sitemap_size,
            }
            unixtimestamp.app.config.update(config)
            response = self.app.get("/sitemapindex.xml", follow_redirects=True)
            self.assertEqual(200, response.status_code)
            self.assertEqual("application/xml", response.content_type)
            xml = ElementTree.fromstring(response.data)
            self.assert_sitemap_xml_correct(xml, start, size, sitemap_size)

    def assert_sitemap_xml_correct(self, xml, start, size, sitemap_size):
        """Assert that sitemap XML is correct."""
        self.assertEqual(
            "{{{}}}sitemapindex".format(self.XML_NAMESPACE), xml.tag
        )

        locs = xml.findall(
            "./s:sitemap/s:loc", namespaces={"s": self.XML_NAMESPACE}
        )
        self.assertEqual(len(locs), size)

        expected_urls = []
        for sitemap_index in range(0, size):
            expected_qs = url_encode(
                {
                    "start": start + (sitemap_size * sitemap_index),
                    "size": sitemap_size,
                }
            )
            expected_url = (
                "https://www.unixtimesta.mp/sitemap.xml?" + expected_qs
            )
            expected_urls.append(expected_url)

        self.assertEqual(expected_urls, [l.text for l in locs])

    def test_sitemap(self):
        """Test sitemap."""
        test_data = ((0, 10, 10), (1234, 5678, 1000), (-100, 10, 10))
        for start, size, real_size in test_data:
            url = "/sitemap.xml?start={}&size={}".format(start, size)
            response = self.app.get(url, follow_redirects=True)
            self.assertEqual(200, response.status_code)
            self.assertEqual("application/xml", response.content_type)
            root = ElementTree.fromstring(response.data)
            self.assertEqual(
                "{{{}}}urlset".format(self.XML_NAMESPACE), root.tag
            )
            locs = root.findall(
                "./s:url/s:loc", namespaces={"s": self.XML_NAMESPACE}
            )
            self.assertEqual(len(locs), real_size)
            timestamps = range(start, start + real_size)
            urls = [
                "https://www.unixtimesta.mp/{}".format(t) for t in timestamps
            ]
            self.assertEqual(urls, [l.text for l in locs])
