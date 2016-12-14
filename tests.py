import unittest
from pprint import pprint
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
        recorded.append((template, context))
    template_rendered.connect(record, app)
    try:
        yield recorded
    finally:
        template_rendered.disconnect(record, app)


class TestCase(unittest.TestCase):
    def setUp(self):
        unixtimestamp.app.config['TESTING'] = True
        self.app = unixtimestamp.app.test_client()

class TimestampTestCase(TestCase):
    def test_timestamp(self):
        with captured_templates(unixtimestamp.app) as templates:
            response = self.app.get('/123456789')
            self.assertEqual(200, response.status_code)
            self.assertEqual(1, len(templates))
            context = templates[0][1]
            self.assertEqual(123456789, context['timestamp'])


if __name__ == '__main__':
    unittest.main()
