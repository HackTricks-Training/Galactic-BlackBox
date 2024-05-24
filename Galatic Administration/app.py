from flask import Flask, request, redirect, render_template
import boto3
import os
import subprocess
import json

app = Flask(__name__)

# DynamoDB Client
dynamodb = boto3.client('dynamodb', region_name=os.getenv('AWS_REGION', 'us-east-1'))

@app.route('/', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        # Parse the raw JSON data from the request body
        data = request.get_data(as_text=True)
        user_data = json.loads(data)
                
        # Example exploit: {
            # "username": "admin\"}, \"password\": {\"S\": {\"$ne\": \"\"}} #",
            # "password": ""
        # }
        
        query_template = '''
        {
            "username": {"S": "%s"},
            "password": {"S": "%s"}
        }
        ''' % (user_data['username'], user_data['password'])
        
        # Directly use the user input to construct the DynamoDB query
        try:
            response = dynamodb.get_item(
                TableName='users',
                Key=json.loads(query_template)
            )
            if 'Item' in response:
                return redirect('/administration')
        except Exception as e:
            return str(e) + '\n' + f"query: {query_template}"
    
    return render_template('login.html')

@app.route('/administration')
def administration():
    return render_template('admin.html')

@app.route('/config')
def config_view():
    path = request.args.get('path', 'file:///etc/training.conf')
    content = "Nothing to do..."
    
    if path.startswith("http://"):
        if not path.startswith('http://127.0.0.1/'):
            content = "ERROR: This isn't a galactic feature. You can access localhost files only!"
    
    if not path.startswith('file://'):
        path = 'file://' + path
    
    try:
        cmd = "curl -s " + path
        content = subprocess.run(cmd.split(" "), capture_output=True, text=True)
       
    except Exception as e:
        content = str(e)

    return render_template('config.html', content=content)

@app.route('/info')
def info():
    url = request.args.get('url', 'https://training.hacktricks.xyz')
    if not url.startswith('http://') and not url.startswith('https://'):
        url = 'http://' + url
    
    return redirect(url)

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=80)
