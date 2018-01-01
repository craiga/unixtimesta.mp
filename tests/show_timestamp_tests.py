"""Tests for showing timestamps."""

from datetime import datetime, MINYEAR, MAXYEAR

import unixtimestamp
from tests import captured_templates, TestCase


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
