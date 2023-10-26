"""Tests for sitemaps and sitemap index."""

from xml.etree import ElementTree

import pytest
from urllib.parse import urlencode

XML_NAMESPACE = "http://www.sitemaps.org/schemas/sitemap/0.9"


@pytest.mark.parametrize(
    "start, size, sitemap_size", [(0, 10, 10), (123, 456, 789), (-100000, 10, 10)]
)
def test_sitemap_index_query_string(client, start, size, sitemap_size):
    """Test sitemap index with values on the query string."""
    query_string = urlencode(
        {"start": start, "size": size, "sitemap_size": sitemap_size}
    )
    url = "/sitemapindex.xml?" + query_string
    response = client.get(url)
    assert response.status_code == 200
    assert response.content_type == "application/xml"
    xml = ElementTree.fromstring(response.data)
    assert_sitemap_index_xml_correct(xml, start, size, sitemap_size)


@pytest.mark.parametrize(
    "start, size, sitemap_size", [(0, 10, 10), (123, 456, 789), (-100000, 10, 10)]
)
def test_sitemap_index_config(client, config, start, size, sitemap_size):
    """Test sitemap index with configured values."""
    config.update(
        {
            "SITEMAP_INDEX_DEFAULT_START": start,
            "SITEMAP_INDEX_DEFAULT_SIZE": size,
            "SITEMAP_DEFAULT_SIZE": sitemap_size,
        }
    )
    response = client.get("/sitemapindex.xml")
    assert response.status_code == 200
    assert response.content_type == "application/xml"
    xml = ElementTree.fromstring(response.data)
    assert_sitemap_index_xml_correct(xml, start, size, sitemap_size)


def assert_sitemap_index_xml_correct(xml, start, size, sitemap_size):
    """Assert that sitemap index XML is correct."""
    assert xml.tag == "{{{}}}sitemapindex".format(XML_NAMESPACE)

    locs = xml.findall("./s:sitemap/s:loc", namespaces={"s": XML_NAMESPACE})
    assert len(locs) == size

    expected_urls = []
    for sitemap_index in range(0, size):
        expected_qs = urlencode(
            {"start": start + (sitemap_size * sitemap_index), "size": sitemap_size}
        )
        expected_url = "https://www.unixtimesta.mp/sitemap.xml?" + expected_qs
        expected_urls.append(expected_url)

    assert [l.text for l in locs] == expected_urls


@pytest.mark.parametrize(
    "start, size, real_size", [(0, 10, 10), (1234, 5678, 1000), (-100, 10, 10)]
)
def test_sitemap_query_string(client, start, size, real_size):
    """Test sitemap."""
    url = "/sitemap.xml?start={}&size={}".format(start, size)
    response = client.get(url)
    assert response.status_code == 200
    assert response.content_type == "application/xml"
    root = ElementTree.fromstring(response.data)
    assert root.tag == "{{{}}}urlset".format(XML_NAMESPACE)
    locs = root.findall("./s:url/s:loc", namespaces={"s": XML_NAMESPACE})
    assert len(locs) == real_size
    timestamps = range(start, start + real_size)
    urls = ["https://www.unixtimesta.mp/{}".format(t) for t in timestamps]
    assert [l.text for l in locs] == urls
