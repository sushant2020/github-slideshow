# login/views.py

from django.http import JsonResponse
from django.views.decorators.csrf import csrf_exempt
from django.utils.decorators import method_decorator
from django.views import View
import json
import jwt
from datetime import datetime, timedelta
from django.db import connection
import pdb
import hashlib
SECRET_KEY = 'Razor@0666!!!'  # Replace with a strong, secret key
ALGORITHM = 'HS256'

@method_decorator(csrf_exempt, name='dispatch')
class LoginAPI(View):
    def post(self, request, *args, **kwargs):
        try:
            # Get data from the frontend
            data = json.loads(request.body)
            email = data.get('email', '')
            password = data.get('password', '')

            # Hash the received password using SHA-256
            hashed_password = hashlib.sha256(password.encode()).hexdigest()

            # Run a raw SQL query to check if the user exists and the password matches
            with connection.cursor() as cursor:
                cursor.execute(
                    "SELECT * FROM user_management WHERE email = %s AND IsActive = 1", [email]
                )
                user_data = cursor.fetchone()
            
            
            if user_data:
                # Compare the hashed password with the PasswordHash value
                if user_data[16] == password:  # Assuming PasswordHash is stored in the 3rd column
                    # Create a JWT token
                    token_data = {
                        'email': email,
                        'exp': datetime.utcnow() + timedelta(hours=1)  # Token expiration time
                    }
                    token = jwt.encode(token_data, SECRET_KEY, algorithm=ALGORITHM)

                    # Format the response JSON for successful login
                    response_data = {
                        "success": True,
                        "data": {
                            "token": token,
                            "name": user_data[3]+' '+user_data[4],  # Replace with the actual column index for the name
                            "email": email,
                            "created_at": user_data[8],  # Assuming created_at is stored in column 9
                            "roles": user_data[14]  # Assuming roles are stored as a comma-separated string in column 15
                        },
                        "message": "User login successful."
                    }

                    return JsonResponse(response_data, status=200)
                else:
                    # Password incorrect
                    return JsonResponse({'success': False, 'message': 'Incorrect password'}, status=401)
            else:
                # Email doesn't exist or user is inactive
                return JsonResponse({'success': False, 'message': 'User does not exist or is inactive'}, status=404)

        except Exception as e:
            # Handle other exceptions
            return JsonResponse({'success': False, 'message': str(e)}, status=500)
