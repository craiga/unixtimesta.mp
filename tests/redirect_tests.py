"""Tests for redirection."""

from urllib.parse import urlparse, quote
from datetime import datetime, MINYEAR, MAXYEAR
from calendar import monthrange
from itertools import product
import re
from math import ceil, floor

from pytz import utc
from dateutil.parser import parse

from tests import TestCase


class DateRedirectTestCase(TestCase):
    """Tests for date URL redirects."""

    DATETIME_OVERFLOW = 9999999999  # value which triggers an OverflowError

    valid_years = (MINYEAR, 1969, 1970, MAXYEAR)  # '70 is epoch, '69 is 70-1
    invalid_years = (MINYEAR - 1, MAXYEAR + 1, DATETIME_OVERFLOW)
    valid_months = (1, 2, 11, 12)  # Jan has 31d, Feb is special, Nov has 30d.
    invalid_months = (0, 13, DATETIME_OVERFLOW)
    valid_days = (1, 28)  # the last day of the month will be calculated
    invalid_days = (0, 32, DATETIME_OVERFLOW)  # …and last of the month + 1
    valid_hours = (0, 23)
    invalid_hours = (-1, 24, DATETIME_OVERFLOW)
    valid_minutes = (0, 59)
    invalid_minutes = (-1, 60, DATETIME_OVERFLOW)
    valid_seconds = (0, 59)
    invalid_seconds = (-1, 60, DATETIME_OVERFLOW)

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
            (
                self.valid_years,
                self.valid_months,
                self.valid_days,
                self.valid_hours,
            ),
            (
                self.valid_years,
                self.valid_months,
                self.valid_days,
                self.valid_hours,
                self.valid_minutes,
            ),
            (
                self.valid_years,
                self.valid_months,
                self.valid_days,
                self.valid_hours,
                self.valid_minutes,
                self.valid_seconds,
            ),
        )

        for valid_datetime_list in valid_datetime_lists:
            for valid_datetime_parts in product(*valid_datetime_list):
                path = "/" + "/".join([str(i) for i in valid_datetime_parts])
                valid_datetime = datetime(*valid_datetime_parts, tzinfo=utc)
                redirect = "/{:.0f}".format(valid_datetime.timestamp())
                yield (path, redirect)

        # Ensure special cases are tested
        for year, month in product(self.valid_years, self.valid_months):
            # Month without day
            path = "/{:d}/{:d}".format(year, month)
            valid_datetime = datetime(
                year=year, month=month, day=1, tzinfo=utc
            )
            redirect = "/{:.0f}".format(valid_datetime.timestamp())
            yield (path, redirect)

            # Last day of the month
            last_day_of_month = monthrange(year, month)[1]
            path = "/{:d}/{:d}/{:d}".format(year, month, last_day_of_month)
            valid_datetime = datetime(
                year=year, month=month, day=last_day_of_month, tzinfo=utc
            )
            redirect = "/{:.0f}".format(valid_datetime.timestamp())
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
            (
                self.valid_years,
                self.valid_months,
                self.valid_days,
                self.invalid_hours,
            ),
            (
                self.valid_years,
                self.valid_months,
                self.valid_days,
                self.valid_hours,
                self.invalid_minutes,
            ),
            (
                self.valid_years,
                self.valid_months,
                self.valid_days,
                self.valid_hours,
                self.valid_minutes,
                self.invalid_seconds,
            ),
        )

        for invalid_datetime_list in invalid_datetime_lists:
            for invalid_datetime_parts in product(*invalid_datetime_list):
                yield "/" + "/".join([str(i) for i in invalid_datetime_parts])

        # Test last day of month + 1
        for year, month in product(self.valid_years, self.valid_months):
            last_day_of_month = monthrange(year, month)[1]
            yield "/{:d}/{:d}/{:d}".format(year, month, last_day_of_month + 1)

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
        for valid_date_string in (
            "31st March 1978",
            "2017-07-29",
            "0001-01-01",
        ):

            expected_datetime = parse(valid_date_string, fuzzy=True)
            expected_datetime = utc.localize(expected_datetime)
            expected_redirect = "/{:.0f}".format(expected_datetime.timestamp())

            url = "/{}".format(quote(valid_date_string))
            response = self.app.get(url)
            self.assertEqual(response.status_code, 302)
            redirect = urlparse(response.location).path
            self.assertEqual(expected_redirect, redirect)

        for invalid_date_string in ("foobar", ".9999999999999999"):
            url = "/{}".format(quote(invalid_date_string))
            response = self.app.get(url)
            self.assertEqual(response.status_code, 404)

    def test_naive_dates_are_utc(self):
        """Test that naive dates are handled as UTC."""
        response = self.app.get("/31st March 1978")
        naive_location = urlparse(response.location).path
        response = self.app.get("/31st March 1978 UTC")
        utc_location = urlparse(response.location).path
        response = self.app.get("/31st March 1978 +10:00")
        aest_location = urlparse(response.location).path
        self.assertEqual(naive_location, utc_location)
        self.assertNotEqual(naive_location, aest_location)


class PostRedirectTestCase(TestCase):
    """Test redirecting post requests."""

    def test_redirect(self):
        """Test redirecting post requests."""
        response = self.app.post("/", data={"time": "foobar"})
        self.assertEqual(response.status_code, 302)
        redirect = urlparse(response.location).path
        self.assertEqual("/foobar", redirect)


class NowTestCase(TestCase):
    """Test requests for now."""

    def test_redirect(self):
        """Test redirecting requests for now."""
        for url in ("/", "/now"):
            lower_bound = floor(datetime.now().timestamp())
            response = self.app.get(url)
            upper_bound = ceil(datetime.now().timestamp())
            self.assertEqual(response.status_code, 302)
            redirect = urlparse(response.location).path
            match = re.match(r"^/(\d+)$", redirect)
            timestamp = int(match.group(1))
            self.assertTrue(lower_bound <= timestamp <= upper_bound)


class RoundingTestCase(TestCase):
    """Test requests for times with decimal points should be redirected."""

    def test_redirect(self):
        """Test redirecting requests for with decimal points."""
        for url in ("/123.123", "/123.987"):
            response = self.app.get(url)
            self.assertEqual(response.status_code, 302)
            redirect = urlparse(response.location).path
            self.assertEqual(redirect, "/123")
