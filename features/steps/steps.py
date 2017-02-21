"""Step definitions for behavioural tests."""


@given(u'the user visits {path}')
def step_impl(context, path):
    """Code for the user visiting a path on the web site."""
    url = 'http://localhost:8000{}'.format(path)
    context.response = context.client.get(url)


@then(u'the user sees {some_string}')
def step_impl(context, some_string):
    """Code to assert that the user sees some string in the response."""
    response_string = context.response.get_data(as_text=True)
    fail_msg = '{} not in {}'.format(some_string, response_string)
    assert some_string in response_string, fail_msg
