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

            # Run a raw SQL query to check if the user exists
            with connection.cursor() as cursor:
                cursor.execute(
                    f"SELECT * FROM user_management WHERE email = '{email}'"
                )
                user_data = cursor.fetchone()

            if user_data:
                # Check if the password matches
                if password == user_data[16]:  # Replace with the actual column index for the password
                    # Create a JWT token
                    token_data = {
                        'email': email,
                        'exp': datetime.utcnow() + timedelta(hours=1)  # Token expiration time
                    }
                    token = jwt.encode(token_data, SECRET_KEY, algorithm=ALGORITHM)

                    # Format the response JSON for successful login
                    response_data = {
                        "success": "true",
                        "data": {
                            "token": token,
                            "name": user_data[3]+' '+user_data[4],  # Replace with the actual column index for the name
                            "email": email,
                            "created_at": user_data[8],  # Replace with the actual column index for the created_at
                            "roles": user_data[14].split(',')  # Assuming roles are stored as a comma-separated string
                        },
                        "message": "User login successful."
                    }

                    return JsonResponse(response_data, status=200)
                else:
                    # Password incorrect
                    return JsonResponse({'success': False, 'message': 'Incorrect password'}, status=401)
            else:
                # Email doesn't exist
                return JsonResponse({'success': False, 'message': 'Email does not exist'}, status=404)

        except Exception as e:
            # Handle other exceptions
            return JsonResponse({'success': False, 'message': str(e)}, status=500)
