"""Tests for string redirection."""

import re
from datetime import datetime
from math import ceil, floor
from urllib.parse import quote, urlparse

import pytest
from dateutil.parser import parse
from pytz import utc


@pytest.mark.parametrize(
    "datetime_string", ["31st March 1978", "2017-07-29", "0001-01-01"]
)
def test_valid_redirect(client, datetime_string):
    """Test datetime string redirects."""
    expected_datetime = parse(datetime_string, fuzzy=True)
    expected_datetime = utc.localize(expected_datetime)
    expected_redirect = f"/{expected_datetime.timestamp():.0f}"

    response = client.get("/" + quote(datetime_string))
    assert response.status_code == 302
    assert urlparse(response.location).path == expected_redirect


@pytest.mark.parametrize("datetime_string", ["foobar", ".9999999999999999"])
def test_invalid_redirects(client, datetime_string):
    """Test invalid datetime string redirects."""
    response = client.get("/" + datetime_string)
    assert response.status_code == 404


def test_naive_dates_are_utc(client):
    """Test that naive dates are handled as UTC."""
    response = client.get("/31st March 1978")
    naive_location = urlparse(response.location).path
    response = client.get("/31st March 1978 UTC")
    utc_location = urlparse(response.location).path
    response = client.get("/31st March 1978 +10:00")
    aest_location = urlparse(response.location).path
    assert naive_location == utc_location
    assert naive_location != aest_location


def test_now(client):
    """Test redirecting requests for now."""
    lower_bound = floor(datetime.now().timestamp())
    response = client.get("/now")
    upper_bound = ceil(datetime.now().timestamp())
    assert response.status_code == 302
    redirect = urlparse(response.location).path
    match = re.match(r"^/(\d+)$", redirect)
    timestamp = int(match.group(1))
    assert lower_bound <= timestamp <= upper_bound
