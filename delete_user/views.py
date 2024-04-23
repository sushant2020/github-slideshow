from django.shortcuts import render

# Create your views here.

from django.http import JsonResponse
from django.views.decorators.csrf import csrf_exempt
from django.utils.decorators import method_decorator
from django.views import View
import json
import jwt
from datetime import datetime, timedelta
from django.db import connection
from password_generator import PasswordGenerator
from django.urls import get_resolver
import pdb


SECRET_KEY = 'The-secret-key'  # Replace with a strong, secret key
ALGORITHM = 'HS256'

@method_decorator(csrf_exempt, name='dispatch')
class DeleteteUserAPI(View):
    def post(self, request, *args, **kwargs):
        try:
            # Get data from the frontend
            # header_dict = request.headers
            # token = header_dict["Authorization"].replace('Bearer ','') 
            
            # decoded_data = jwt.decode(token, SECRET_KEY, algorithms=[ALGORITHM])
            data = json.loads(request.body)
            email = data.get('email', '')
            # token_data = {
            #     'email': email,
            #     'exp': datetime.utcnow() + timedelta(hours=1)  # Token expiration time
            # }
            # token = jwt.encode(token_data, SECRET_KEY, algorithm=ALGORITHM)
            # Run a raw SQL query to check if the user exists
            query = f'''UPDATE user_management
                        SET IsActive = 0
                        WHERE Email = '{email}';
                    ''' 
            print(query)   
            with connection.cursor() as cursor:
                cursor.execute(query)
                
                
            # If user_data is not None, the user exists

                # Create a JWT token
            

            # Format the response JSON
            response_data = {
                "success": True,
                "message": "User deleted."
                        }

            return JsonResponse(response_data, status=200)
            
        # except jwt.ExpiredSignatureError:
        #     # Token has expired
        #     return JsonResponse({'success': False, 'message': 'Token has expired'}, status=401)

        # except jwt.InvalidTokenError:
        #     # Invalid token
        #     return JsonResponse({'success': False, 'message': 'Invalid token'}, status=401)
        except Exception as e:
            # Handle other exceptions
            return JsonResponse({'success': False, 'message': str(e)}, status=500)