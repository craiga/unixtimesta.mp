"""
Tests for unixtimesta.mp.
"""

import unittest
from contextlib import contextmanager

from flask import template_rendered

import unixtimestamp


@contextmanager
def captured_templates(app):
    """
    Captures all templates being rendered along with the context used.
    """
    recorded = []

    def record(sender, template, context, **extra):
        # pylint:disable=unused-argument
        recorded.append((template, context))

    template_rendered.connect(record, app)

    try:
        yield recorded
    finally:
        template_rendered.disconnect(record, app)


class TestCase(unittest.TestCase):
    """
    Base test class.
    """
    def setUp(self):
        unixtimestamp.app.config['TESTING'] = True
        self.app = unixtimestamp.app.test_client()


class ShowTimestampTestCase(TestCase):
    """
    Tests for showing timestamp.
    """
    def test_timestamp(self):
        for timestamp in (0, 1, 123456):
            with captured_templates(unixtimestamp.app) as templates:
                response = self.app.get('/{}'.format(timestamp))
                self.assertEqual(200, response.status_code)
                self.assertEqual(1, len(templates))
                context = templates[0][1]
                self.assertEqual(timestamp, context['timestamp'])
                self.assertEqual(timestamp, context['datetime'].timestamp())

    def test_locale(self):
        with captured_templates(unixtimestamp.app) as templates:
            locale = 'fr-CA'
            lang_header = ('Accept-Language', locale)
            response = self.app.get('/123456',
                                    headers=((lang_header),))
            self.assertEqual(200, response.status_code)
            self.assertEqual(1, len(templates))
            context = templates[0][1]
            self.assertEqual(locale, context['locale'])

    def test_invalid_timestamp(self):
        for timestamp in (-1, -123456):
            response = self.app.get('/{}'.format(timestamp))
            self.assertEqual(404, response.status_code)


if __name__ == '__main__':
    unittest.main()
