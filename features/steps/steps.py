"""Step definitions for behavioural tests."""


@given(u'the user visits {path}')
def step_impl(context, path):
    """Code for the user visiting a path on the web site."""
    url = 'http://localhost:8000{}'.format(path)
    context.response = context.client.get(url)


@then(u'the user sees {some_string}')
def step_impl(context, some_string):
    """Code to assert that the user sees some string in the response."""
    assert some_string in context.response.get_data(as_text=True)
