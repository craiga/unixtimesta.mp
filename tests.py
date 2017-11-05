"""Tests for Unix Timestamp Flask application."""

import unittest
from contextlib import contextmanager
from urllib.parse import urlparse, quote
from datetime import datetime, MINYEAR, MAXYEAR
from calendar import monthrange
from itertools import product
import re
from math import ceil, floor
from xml.etree import ElementTree

from flask import template_rendered
from pytz import utc
from dateutil.parser import parse
from werkzeug.urls import url_encode

import unixtimestamp
from unixtimestamp import parse_accept_language


@contextmanager
def captured_templates(app):
    """Capture all templates being rendered along with the context used."""
    recorded = []

    def record(sender, template, context, **extra):
        # pylint:disable=unused-argument
        recorded.append((template, context))

    template_rendered.connect(record, app)

    try:
        yield recorded
    finally:
        template_rendered.disconnect(record, app)


def max_timestamp_for_datetime():
    """Calculate the latest timestamp Python will allow in a datetime."""
    max_datetime = datetime(year=MAXYEAR, month=12, day=31,
                            hour=23, minute=59, second=59)
    return int(max_datetime.timestamp())


def min_timestamp_for_datetime():
    """Return the earliest timestamp Python will allow in a datetime."""
    if MINYEAR != 1:
        raise RuntimeError('Cannot calculate min timestamp')

    return -62135596800  # 1st Jan 1; calculation in code causes OverflowError


class TestCase(unittest.TestCase):
    """Base test case."""

    def setUp(self):
        """Set up test case."""
        unixtimestamp.app.testing = True
        # Debug enabled in tests as a workaround for
        # https://github.com/kennethreitz/flask-sslify/issues/50.
        unixtimestamp.app.debug = True
        self.app = unixtimestamp.app.test_client()


class ShowTimestampTestCase(TestCase):
    """Tests for showing timestamp."""

    def test_timestamp(self):
        """Test getting timestamps."""
        for timestamp in (0, 1, 123456, '-0', -1, -123456):
            with captured_templates(unixtimestamp.app) as templates:
                response = self.app.get('/{}'.format(timestamp))
                self.assertEqual(200, response.status_code)
                self.assertEqual(1, len(templates))
                context = templates[0][1]
                self.assertEqual(int(timestamp), context['timestamp'])
                self.assertEqual(int(timestamp),
                                 context['datetime'].timestamp())

    def test_max_timestamp(self):
        """Test getting maximum timestamp."""
        with captured_templates(unixtimestamp.app) as templates:
            timestamp = max_timestamp_for_datetime()
            response = self.app.get('/{}'.format(timestamp))
            self.assertEqual(200, response.status_code)
            self.assertEqual(1, len(templates))
            context = templates[0][1]
            self.assertEqual(int(timestamp), context['timestamp'])
            self.assertEqual(MAXYEAR, context['datetime'].year)

    def test_min_timestamp(self):
        """Test getting minimum timestamp."""
        with captured_templates(unixtimestamp.app) as templates:
            timestamp = min_timestamp_for_datetime()
            response = self.app.get('/{}'.format(timestamp))
            self.assertEqual(200, response.status_code)
            self.assertEqual(1, len(templates))
            context = templates[0][1]
            self.assertEqual(int(timestamp), context['timestamp'])
            self.assertEqual(MINYEAR, context['datetime'].year)

    def test_locale(self):
        """Test locale is passed into template."""
        with captured_templates(unixtimestamp.app) as templates:
            lang_header = ('Accept-Language', 'fr-CA,fr;q=0.5')
            response = self.app.get('/123456',
                                    headers=((lang_header),))
            self.assertEqual(200, response.status_code)
            self.assertEqual(1, len(templates))
            context = templates[0][1]
            self.assertEqual('fr-CA', context['locale'])

    def test_overflow(self):
        """Test handling of too large or small dates."""
        for timestamp in (max_timestamp_for_datetime() + 1,
                          min_timestamp_for_datetime() - 1,
                          9999999999999999,
                          99999999999999999,
                          999999999999999999):
            with captured_templates(unixtimestamp.app) as templates:
                response = self.app.get('/{}'.format(timestamp))
                self.assertEqual(404, response.status_code)
                self.assertEqual(1, len(templates))
                context = templates[0][1]
                self.assertEqual(int(timestamp), context['timestamp'])
                self.assertNotIn('datetime', context.keys())


class DateRedirectTestCase(TestCase):
    """Tests for date URL redirects."""

    valid_years = (MINYEAR, 1969, 1970, MAXYEAR)  # '70 is epoch, '69 is 70-1
    invalid_years = (MINYEAR - 1, MAXYEAR + 1)
    valid_months = (1, 2, 11)  # Jan has 31d, Feb is special, Nov has 30d.
    invalid_months = (0, 13)
    valid_days = (1, 28,)  # the last day of the month will be calculated
    invalid_days = (0, 32)  # the last day of the month + 1 will be calculated
    valid_hours = (0, 23)
    invalid_hours = (-1, 24)
    valid_minutes = (0, 59)
    invalid_minutes = (-1, 60)
    valid_seconds = (0, 59)
    invalid_seconds = (-1, 60)

    def valid_datetime_redirects(self):
        """
        Generate valid, iterable test data.

        Test data returned as a series of 2-tuples containing a date URL path
        (e.g. "/yyyy/mm/dd") and the expected timestamp path the site should
        redirect to.
        """
        # A list of n-tuples of lists to generate valid dates
        valid_datetime_lists = (
            (self.valid_years, self.valid_months, self.valid_days),
            (self.valid_years, self.valid_months, self.valid_days,
             self.valid_hours),
            (self.valid_years, self.valid_months, self.valid_days,
             self.valid_hours, self.valid_minutes),
            (self.valid_years, self.valid_months, self.valid_days,
             self.valid_hours, self.valid_minutes, self.valid_seconds))

        for valid_datetime_list in valid_datetime_lists:
            for valid_datetime_parts in product(*valid_datetime_list):
                path = '/' + '/'.join([str(i) for i in valid_datetime_parts])
                valid_datetime = datetime(*valid_datetime_parts, tzinfo=utc)
                redirect = '/{:.0f}'.format(valid_datetime.timestamp())
                yield (path, redirect)

        # Ensure special cases are tested
        for year, month in product(self.valid_years, self.valid_months):
            # Month without day
            path = '/{:d}/{:d}'.format(year, month)
            valid_datetime = datetime(year=year, month=month, day=1,
                                      tzinfo=utc)
            redirect = '/{:.0f}'.format(valid_datetime.timestamp())
            yield (path, redirect)

            # Last day of the month
            last_day_of_month = monthrange(year, month)[1]
            path = '/{:d}/{:d}/{:d}'.format(year, month, last_day_of_month)
            valid_datetime = datetime(year=year, month=month,
                                      day=last_day_of_month, tzinfo=utc)
            redirect = '/{:.0f}'.format(valid_datetime.timestamp())
            yield (path, redirect)

    def invalid_datetime_redirects(self):
        """
        Generate invalid iterable test data.

        Test data returned as a series of invalid date URL paths.
        """
        # A list of n-tuples of lists to generate invalid dates
        invalid_datetime_lists = (
            (self.invalid_years, self.valid_months),
            (self.valid_years, self.invalid_months),
            (self.valid_years, self.valid_months, self.invalid_days),
            (self.valid_years, self.valid_months, self.valid_days,
             self.invalid_hours),
            (self.valid_years, self.valid_months, self.valid_days,
             self.valid_hours, self.invalid_minutes),
            (self.valid_years, self.valid_months, self.valid_days,
             self.valid_hours, self.valid_minutes, self.invalid_seconds))

        for invalid_datetime_list in invalid_datetime_lists:
            for invalid_datetime_parts in product(*invalid_datetime_list):
                yield '/' + '/'.join([str(i) for i in invalid_datetime_parts])

        # Test last day of month + 1
        for year, month in product(self.valid_years, self.valid_months):
            last_day_of_month = monthrange(year, month)[1]
            yield '/{:d}/{:d}/{:d}'.format(year, month, last_day_of_month + 1)

    def test_valid_redirects(self):
        """Test redirection to timestamps based on valid date components."""
        for url, expected_redirect in self.valid_datetime_redirects():
            response = self.app.get(url)
            self.assertEqual(response.status_code, 301)
            redirect = urlparse(response.location).path
            self.assertEqual(expected_redirect, redirect)

    def test_invalid_redirects(self):
        """Test redirection to timestamps based on invalid date components."""
        for url in self.invalid_datetime_redirects():
            response = self.app.get(url)
            self.assertEqual(response.status_code, 404)


class StringRedirectTestCase(TestCase):
    """Tests for datetime description URL redirects."""

    def test_redirect(self):
        """Test datetime description URL redirects."""
        for valid_date_string in ('31st March 1978', '2017-07-29'):
            url = '/{}'.format(quote(valid_date_string))
            expected_datetime = parse(valid_date_string, fuzzy=True)
            expected_redirect = '/{:.0f}'.format(expected_datetime.timestamp())
            response = self.app.get(url)
            self.assertEqual(response.status_code, 302)
            redirect = urlparse(response.location).path
            self.assertEqual(expected_redirect, redirect)

        for invalid_date_string in ('foobar', '.9999999999999999'):
            url = '/{}'.format(quote(invalid_date_string))
            response = self.app.get(url)
            self.assertEqual(response.status_code, 404)


class PostRedirectTestCase(TestCase):
    """Test redirecting post requests."""

    def test_redirect(self):
        """Test redirecting post requests."""
        response = self.app.post('/', data={'time': 'foobar'})
        self.assertEqual(response.status_code, 302)
        redirect = urlparse(response.location).path
        self.assertEqual('/foobar', redirect)


class NowTestCase(TestCase):
    """Test requests for now."""

    def test_redirect(self):
        """Test redirecting requests for now."""
        for url in ('/', '/now'):
            lower_bound = floor(datetime.now().timestamp())
            response = self.app.get(url)
            upper_bound = ceil(datetime.now().timestamp())
            self.assertEqual(response.status_code, 302)
            redirect = urlparse(response.location).path
            match = re.match(r'^/(\d+)$', redirect)
            timestamp = int(match.group(1))
            self.assertTrue(lower_bound <= timestamp <= upper_bound)


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


class SitemapTestCase(TestCase):
    """Tests for sitemap requests."""

    XML_NAMESPACE = 'http://www.sitemaps.org/schemas/sitemap/0.9'

    def test_sitemap_index(self):
        """Test sitemap index."""
        for start, size, sitemap_size in ((0, 10, 10),
                                          (123, 456, 789),
                                          (-100000, 10, 10)):
            # Test with values on the query string.
            query_string = url_encode({'start': start, 'size': size,
                                       'sitemap_size': sitemap_size})
            url = '/sitemapindex.xml?' + query_string
            response = self.app.get(url)
            self.assertEqual(200, response.status_code)
            self.assertEqual('application/xml', response.content_type)
            xml = ElementTree.fromstring(response.data)
            self.assert_sitemap_xml_correct(xml, start, size, sitemap_size)

            # Test with values in configuration.
            config = {'SITEMAP_INDEX_DEFAULT_START': start,
                      'SITEMAP_INDEX_DEFAULT_SIZE': size,
                      'SITEMAP_DEFAULT_SIZE': sitemap_size}
            unixtimestamp.app.config.update(config)
            response = self.app.get('/sitemapindex.xml')
            self.assertEqual(200, response.status_code)
            self.assertEqual('application/xml', response.content_type)
            xml = ElementTree.fromstring(response.data)
            self.assert_sitemap_xml_correct(xml, start, size, sitemap_size)

    def assert_sitemap_xml_correct(self, xml, start, size, sitemap_size):
        """Assert that sitemap XML is correct."""
        self.assertEqual('{{{}}}sitemapindex'.format(self.XML_NAMESPACE),
                         xml.tag)

        locs = xml.findall('./s:sitemap/s:loc',
                           namespaces={'s': self.XML_NAMESPACE})
        self.assertEqual(len(locs), size)

        expected_urls = []
        for sitemap_index in range(0, size):
            expected_qs = url_encode({
                'start': start + (sitemap_size * sitemap_index),
                'size': sitemap_size
            })
            expected_url = 'http://localhost/sitemap.xml?' + expected_qs
            expected_urls.append(expected_url)

        self.assertEqual(expected_urls, [l.text for l in locs])

    def test_sitemap(self):
        """Test sitemap."""
        test_data = ((0, 10, 10), (1234, 5678, 1000), (-100, 10, 10))
        for start, size, real_size in test_data:
            url = '/sitemap.xml?start={}&size={}'.format(start, size)
            response = self.app.get(url)
            self.assertEqual(200, response.status_code)
            self.assertEqual('application/xml', response.content_type)
            root = ElementTree.fromstring(response.data)
            self.assertEqual('{{{}}}urlset'.format(self.XML_NAMESPACE),
                             root.tag)
            locs = root.findall('./s:url/s:loc',
                                namespaces={'s': self.XML_NAMESPACE})
            self.assertEqual(len(locs), real_size)
            timestamps = range(start, start + real_size)
            urls = ['http://localhost/{}'.format(t) for t in timestamps]
            self.assertEqual(urls, [l.text for l in locs])


class ParseLocaleTestCase(TestCase):
    """Tests for locale parsing."""

    def test_parse_accept_language(self):
        """Test parsing of locale strings."""
        for expected_locale, accept_language in (
                ('en-US', 'en-US'),
                ('en-US', 'en-US,en;q=0.5'),
                ('ru', 'ru,en'),
                ('tr-TR', 'tr-TR,tr;q=0.8,en-US;q=0.6,en;q=0.4')):
            locale = parse_accept_language(accept_language)
            self.assertEqual(expected_locale, locale)


if __name__ == '__main__':
    unittest.main()
