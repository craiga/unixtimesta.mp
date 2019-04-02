"""
Tests for datetime redirection.

Ensures URLs in forms like /YYYY/MM/DD work as expected.
"""

from calendar import monthrange
from datetime import MAXYEAR, MINYEAR, datetime
from itertools import product
from urllib.parse import urlparse

import pytest
from pytz import utc

DATETIME_OVERFLOW = 9999999999  # value which triggers an OverflowError

VALID_YEARS = (MINYEAR, 1969, 1970, MAXYEAR)  # '70 is epoch, '69 is 70-1
INVALID_YEARS = (MINYEAR - 1, MAXYEAR + 1, DATETIME_OVERFLOW)
VALID_MONTHS = (1, 2, 11, 12)  # Jan has 31d, Feb is special, Nov has 30d.
INVALID_MONTHS = (0, 13, DATETIME_OVERFLOW)
VALID_DAYS = (1, 28)  # the last day of the month will be calculated
INVALID_DAYS = (0, 32, DATETIME_OVERFLOW)  # …and last of the month + 1
VALID_HOURS = (0, 23)
INVALID_HOURS = (-1, 24, DATETIME_OVERFLOW)
VALID_MINUTES = (0, 59)
INVALID_MINUTES = (-1, 60, DATETIME_OVERFLOW)
VALID_SECONDS = (0, 59)
INVALID_SECONDS = (-1, 60, DATETIME_OVERFLOW)


def valid_datetime_redirects():
    """
    Generate valid, iterable test data.

    Test data returned as a series of 2-tuples containing a date URL path
    (e.g. "/yyyy/mm/dd") and the expected timestamp path the site should
    redirect to.
    """
    # A list of n-tuples of lists to generate valid dates
    valid_datetime_lists = (
        (VALID_YEARS, VALID_MONTHS, VALID_DAYS),
        (VALID_YEARS, VALID_MONTHS, VALID_DAYS, VALID_HOURS),
        (VALID_YEARS, VALID_MONTHS, VALID_DAYS, VALID_HOURS, VALID_MINUTES),
        (
            VALID_YEARS,
            VALID_MONTHS,
            VALID_DAYS,
            VALID_HOURS,
            VALID_MINUTES,
            VALID_SECONDS,
        ),
    )

    for valid_datetime_list in valid_datetime_lists:
        for valid_datetime_parts in product(*valid_datetime_list):
            path = "/" + "/".join([str(i) for i in valid_datetime_parts])
            valid_datetime = datetime(*valid_datetime_parts, tzinfo=utc)
            redirect = "/{:.0f}".format(valid_datetime.timestamp())
            yield (path, redirect)

    # Ensure special cases are tested
    for year, month in product(VALID_YEARS, VALID_MONTHS):
        # Month without day
        path = "/{:d}/{:d}".format(year, month)
        valid_datetime = datetime(year=year, month=month, day=1, tzinfo=utc)
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


def invalid_datetime_redirects():
    """
    Generate invalid iterable test data.

    Test data returned as a series of invalid date URL paths.
    """
    # A list of n-tuples of lists to generate invalid dates
    invalid_datetime_lists = (
        (INVALID_YEARS, VALID_MONTHS),
        (VALID_YEARS, INVALID_MONTHS),
        (VALID_YEARS, VALID_MONTHS, INVALID_DAYS),
        (VALID_YEARS, VALID_MONTHS, VALID_DAYS, INVALID_HOURS),
        (VALID_YEARS, VALID_MONTHS, VALID_DAYS, VALID_HOURS, INVALID_MINUTES),
        (
            VALID_YEARS,
            VALID_MONTHS,
            VALID_DAYS,
            VALID_HOURS,
            VALID_MINUTES,
            INVALID_SECONDS,
        ),
    )

    for invalid_datetime_list in invalid_datetime_lists:
        for invalid_datetime_parts in product(*invalid_datetime_list):
            yield "/" + "/".join([str(i) for i in invalid_datetime_parts])

    # Test last day of month + 1
    for year, month in product(VALID_YEARS, VALID_MONTHS):
        last_day_of_month = monthrange(year, month)[1]
        yield "/{:d}/{:d}/{:d}".format(year, month, last_day_of_month + 1)


@pytest.mark.parametrize("url, expected_redirect", valid_datetime_redirects())
def test_valid_redirects(client, url, expected_redirect):
    """Test redirection to timestamps based on valid date components."""
    response = client.get(url)
    assert response.status_code == 301
    redirect = urlparse(response.location).path
    assert redirect == expected_redirect


@pytest.mark.parametrize("url", invalid_datetime_redirects())
def test_invalid_redirects(client, url):
    """Test redirection to timestamps based on invalid date components."""
    response = client.get(url)
    assert response.status_code == 404
