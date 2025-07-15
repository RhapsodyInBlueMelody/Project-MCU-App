from logging import debug # This import is not used, you can remove it
from flask import Flask, request, jsonify
from functools import wraps

app = Flask(__name__)

API_KEY = "testonetwo"


def token_required(f):
    @wraps(f)
    def decorated(*args, **kwargs):
        token = None

        # Check for token in different locations, in order of preference
        # 1. Authorization header (Bearer token)
        auth_header = request.headers.get('Authorization')
        if auth_header and auth_header.startswith('Bearer '):
            token = auth_header.split(' ')[1]
        
        # 2. x-api-key header (if not found yet)
        if not token:
            token = request.headers.get('x-api-key')
            
        # 3. Query parameter 'token' (if not found yet)
        if not token:
            token = request.args.get('token')
            
        # 4. JSON body 'token' (if not found yet and request is JSON)
        if not token and request.is_json:
            token = request.json.get('token')

        # Now, check if a token was found at all
        if not token:
            return jsonify({"message": "Token is missing desuwa!! >_<", "status": "error"}), 401 # 401 Unauthorized
        
        # Check if the found token is valid
        if token != API_KEY:
            return jsonify({"message": "Token is invalid desuwa!! >_<", "status": "error"}), 403 # 403 Forbidden

        # If token is valid, proceed with the original function
        return f(*args, **kwargs)
    return decorated


@app.route('/api/hello', methods=['POST'])
@token_required
def hello_api():
    data = request.json
    name = data.get('name', 'Guest')
    message = f"Hello, {name}! your data recieved."
    return jsonify({"message": message, "status": "success"})


if __name__ == '__main__':
    app.run(debug=True)
