"""Tests for parse_accept_language."""

from unixtimestamp.utils import parse_accept_language
from tests import TestCase


class ParseLocaleTestCase(TestCase):
    """Tests for locale parsing."""

    def test_parse_accept_language(self):
        """Test parsing of locale strings."""
        default_locale = 'fr-CA'
        for expected_locale, accept_language in (
                ('en-US', 'en-US'),
                ('en-US', 'en-US,en;q=0.5'),
                ('ru', 'ru,en'),
                ('tr-TR', 'tr-TR,tr;q=0.8,en-US;q=0.6,en;q=0.4'),
                ('es-ES', 'es-ES_tradnl'),
                ('fr-CA', None),
                ('fr-CA', ''),
                ('fr-CA', '*')):
            with self.subTest(expected_locale=expected_locale,
                              accept_language=accept_language):
                locale = parse_accept_language(accept_language, default_locale)
                self.assertEqual(expected_locale, locale)
