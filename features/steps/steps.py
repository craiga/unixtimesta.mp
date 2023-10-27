"""Step definitions for behavioural tests."""

from behave import given, then  # pylint:disable=no-name-in-module


@given("the user visits {path}")
def visit(context, path):
    """Code for the user visiting a path on the web site."""
    url = f"http://localhost:8000{path}"
    context.response = context.client.get(url, follow_redirects=True)


@then("the user sees {some_string}")
def sees(context, some_string):
    """Code to assert that the user sees some string in the response."""
    response_string = context.response.get_data(as_text=True)
    fail_msg = f"{some_string} not in {response_string}"
    assert some_string in response_string, fail_msg
