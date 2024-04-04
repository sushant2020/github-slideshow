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
import hashlib

SECRET_KEY = 'The-secret-key'  # Replace with a strong, secret key
ALGORITHM = 'HS256'

@method_decorator(csrf_exempt, name='dispatch')
class UpdatetePasswordrAPI(View):
    def post(self, request, *args, **kwargs):
        try:
            # Get data from the frontend
            data = json.loads(request.body)
            email = data.get('email', '')
            new_password = data.get('new_password','')
            old_password = data.get('old_password','')
            verify_password = data.get('verify_password','')
            
            if new_password != verify_password:
                return JsonResponse({'success': False, 'message': "Oops! It seems the passwords you've entered don't match. Please ensure they are identical before proceeding"}, status=500)

            password_hash = hashlib.sha256(str(new_password).encode()).hexdigest()
            #password_query = f''' Select Password from user_management where Email ='{email}''''
            query = f'''
                        UPDATE user_management
                        SET Password = '{new_password}', PasswordHash = '{password_hash}'
                        WHERE Email = '{email}'
                        AND Password = '{old_password}';
                    ''' 
            print(query)   
            with connection.cursor() as cursor:
                # cursor.execute(password_query)
                # res = cursor.fetchone[0]
                # if res == old_password:
                #     return JsonResponse({'success': False, 'message': ""}, status=500)
                
                cursor.execute(query)
                
            response_data = {
                "success": True,
                "message": "Password changed"
                        }

            return JsonResponse(response_data, status=200)
            

        except Exception as e:
            # Handle other exceptions
            return JsonResponse({'success': False, 'message': str(e)}, status=500)

@method_decorator(csrf_exempt, name='dispatch')
class ForgotPasswordAPI(View):
    def post(self, request, *args, **kwargs):
        try:
            # Get data from the frontend
            data = json.loads(request.body)
            email = data.get('email', '')
            new_password = data.get('new_password','')

            verify_password = data.get('verify_password','')
            
            if new_password != verify_password:
                return JsonResponse({'success': False, 'message': "Oops! It seems the passwords you've entered don't match. Please ensure they are identical before proceeding"}, status=500)
            
            password_hash = hashlib.sha256(str(new_password).encode()).hexdigest()
            query = f'''UPDATE user_management
                        SET Password = '{new_password}', PasswordHash = '{password_hash}'
                        WHERE Email = '{email}';

                    ''' 
            print(query)   
            with connection.cursor() as cursor:
                cursor.execute(query)
                
            response_data = {
                "success": True,
                "message": "Password changed"
                        }

            return JsonResponse(response_data, status=200)
            

        except Exception as e:
            # Handle other exceptions
            return JsonResponse({'success': False, 'message': str(e)}, status=500)
