from flask import Flask, request, redirect, render_template, url_for, flash
from flask_login import LoginManager, UserMixin, login_user, login_required, logout_user, current_user
import boto3
import os
import json
import random
import subprocess

app = Flask(__name__)
app.config['SECRET_KEY'] = str(random.randint(0, 100000000000))
login_manager = LoginManager(app)
login_manager.login_view = 'login'

# DynamoDB Client
dynamodb = boto3.client('dynamodb', region_name=os.getenv('AWS_REGION', 'us-east-1'))

class User(UserMixin):
    def __init__(self, username, password):
        self.id = username
        self.username = username
        self.password = password

@login_manager.user_loader
def load_user(username):
    scan_filter = """{
    "username": {
        "ComparisonOperator": "EQ",
        "AttributeValueList": [{"S": "%s"}]
    }
}
""" % (username)

    try:
        response = dynamodb.scan(
            TableName="blackbox_lab_3", 
            ScanFilter=json.loads(scan_filter)
        )
        if 'Items' in response:
            item = response['Items'][0]
            return User(username=item['username']['S'], password=item['password']['S'])
    except Exception as e:
        print(e)
    return None

@app.route('/', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        # Parse the form data from the request body
        user_data = {
            'username': request.form.get('username'),
            'password': request.form.get('password')
        }
        
        # Construct the DynamoDB query
        scan_filter = """{
    "username": {
        "ComparisonOperator": "EQ",
        "AttributeValueList": [{"S": "%s"}]
    },
    "password": {
        "ComparisonOperator": "EQ",
        "AttributeValueList": [{"S": "%s"}]
    }
}
""" % (user_data['username'], user_data['password'])
        
        try:
            # Perform the Query operation using the raw, unsanitized input
            response = dynamodb.scan(TableName="blackbox_lab_3", ScanFilter=json.loads(scan_filter))

            # Check if the response contains an item
            if response.get('Items'):
                user = User(username=user_data['username'], password=user_data['password'])
                login_user(user)
                return redirect(url_for('administration'))
            else:
                flash(f'Invalid credentials. Query: {scan_filter}', 'danger')
        
        except Exception as e:
            return str(e) + '<br>\n' + f"query: {scan_filter}"
    
    return render_template('login.html')

@app.route('/logout')
@login_required
def logout():
    logout_user()
    return redirect(url_for('login'))

@app.route('/administration')
@login_required
def administration():
    return render_template('admin.html')

@app.route('/config')
@login_required
def config_view():
    path = request.args.get('path', 'file:///etc/training.conf')
    content = "Nothing to do..."
    
    if path.lower().startswith("http://") or path.lower().startswith("https://"):
        if not path.startswith('http://localhost/'):
            content = "ERROR: This isn't a galactic feature. You can only access localhost files!"
            return render_template('config.html', content=content)
    
    elif not path.startswith('file://'):
        path = 'file://' + path
    
    try:
        cmd = "curl --output - -s " + path
        content = subprocess.run(cmd.split(" "), capture_output=True).stdout
    except Exception as e:
        content = str(e)

    try:
        return render_template('config.html', content=content)
    except Exception as e:
        return render_template('config.html', content=str(content))

@app.route('/info')
def info():
    url = request.args.get('url', 'https://training.hacktricks.xyz')
    if not url.startswith('http://') and not url.startswith('https://'):
        url = 'http://' + url
    
    return redirect(url)

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=80, debug=False)
