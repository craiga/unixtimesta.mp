"""Tests for showing timestamps."""

from datetime import MAXYEAR, datetime

import pytest


def max_timestamp_for_datetime():
    """Calculate the latest timestamp Python will allow in a datetime."""
    max_datetime = datetime(
        year=MAXYEAR, month=12, day=31, hour=23, minute=59, second=59
    )
    return int(max_datetime.timestamp())


def min_timestamp_for_datetime():
    """Return the earliest timestamp Python will allow in a datetime."""
    return -62135596800  # 1st Jan 1; calculation in code causes OverflowError


@pytest.mark.parametrize(
    "timestamp, expected_strings",
    [
        (0, ["1 January 1970", "00:00:00 UTC"]),
        (1, ["1 January 1970", "00:00:01 UTC"]),
        (1234566789, ["13 February 2009"]),
        ("-0", ["1 January 1970", "00:00:00 UTC"]),
        (-1, ["31 December 1969", "23:59:59 UTC"]),
        (-1234566789, ["18 November 1930"]),
        (
            max_timestamp_for_datetime(),
            ["31 December {}".format(MAXYEAR), "23:59:59 UTC"],
        ),
        (min_timestamp_for_datetime(), ["1 January 0001", "00:00:00 UTC"]),
    ],
)
def test_timestamp(client, timestamp, expected_strings):
    """Test getting timestamps."""
    response = client.get("/{}".format(timestamp))
    assert response.status_code == 200
    for expected_string in expected_strings:
        assert expected_string in response.get_data(as_text=True)


@pytest.mark.parametrize(
    "timestamp",
    [
        max_timestamp_for_datetime() + 1,
        min_timestamp_for_datetime() - 1,
        9999999999999999,
        99999999999999999,
        999999999999999999,
    ],
)
def test_overflow(client, timestamp):
    """Test handling of too large or small dates."""
    response = client.get("/{}".format(timestamp))
    assert response.status_code == 404
    assert str(timestamp) in response.get_data(as_text=True)
