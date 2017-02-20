@given(u'the user visits {path}')
def step_impl(context, path):
    context.response = context.client.get('http://localhost:8000{}'.format(path))

@then(u'the user sees {some_string}')
def step_impl(context, some_string):
    assert some_string in context.response.get_data(as_text=True)
