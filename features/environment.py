import os
import sys
import tempfile

# # Set Path
# pwd = os.path.abspath(os.path.dirname(__file__))
# project = os.path.basename(pwd)
# new_path = pwd.strip(project)
# activate_this = os.path.join(new_path, 'unixtimestamp')
# sys.path.append(activate_this)

from unixtimestamp import app

def before_feature(context, feature):
    app.config['TESTING'] = True
    context.client = app.test_client()
