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
            # header_dict = request.headers
            # token = header_dict["Authorization"].replace('Bearer ','') 
            
            # decoded_data = jwt.decode(token, SECRET_KEY, algorithms=[ALGORITHM])
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
            
        # except jwt.ExpiredSignatureError:
        #     # Token has expired
        #     return JsonResponse({'success': False, 'message': 'Token has expired'}, status=401)

        # except jwt.InvalidTokenError:
        #     # Invalid token
        #     return JsonResponse({'success': False, 'message': 'Invalid token'}, status=401)
        except Exception as e:
            # Handle other exceptions
            return JsonResponse({'success': False, 'message': str(e)}, status=500)

@method_decorator(csrf_exempt, name='dispatch')
class ForgotPasswordAPI(View):
    def post(self, request, *args, **kwargs):
        try:
            # Get data from the frontend
            # header_dict = request.headers
            # token = header_dict["Authorization"].replace('Bearer ','') 
            
            # decoded_data = jwt.decode(token, SECRET_KEY, algorithms=[ALGORITHM])
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
            
        # except jwt.ExpiredSignatureError:
        #     # Token has expired
        #     return JsonResponse({'success': False, 'message': 'Token has expired'}, status=401)

        # except jwt.InvalidTokenError:
        #     # Invalid token
        #     return JsonResponse({'success': False, 'message': 'Invalid token'}, status=401)
        except Exception as e:
            # Handle other exceptions
            return JsonResponse({'success': False, 'message': str(e)}, status=500)


@method_decorator(csrf_exempt, name='dispatch')
class ForgotPasswordRedirectAPI(View):
    def post(self, request, *args, **kwargs):
        try:
            # Get data from the frontend
            # header_dict = request.headers
            # token = header_dict["Authorization"].replace('Bearer ','') 
            
            # decoded_data = jwt.decode(token, SECRET_KEY, algorithms=[ALGORITHM])
            data = json.loads(request.body)
            email = data.get('email', '')
            query = f"select * from user_management where email = '{email}' and IsActive = 1"
            with connection.cursor() as cursor:
                cursor.execute(query)
                data = cursor.fetchone()
            if data:
                response_data = {"success":True,"data":"Reset Password","message":"Redirect to reset password link"}
                return JsonResponse(response_data, status=200)
            else:
                response_data = {"success":False,"data":"Email doesn't exist","message":"Please enter correct email"}
                return JsonResponse(response_data, status=500)
        # except jwt.ExpiredSignatureError:
        #     # Token has expired
        #     return JsonResponse({'success': False, 'message': 'Token has expired'}, status=401)

        # except jwt.InvalidTokenError:
        #     # Invalid token
        #     return JsonResponse({'success': False, 'message': 'Invalid token'}, status=401)
        except Exception as e:
            # Handle other exceptions
            return JsonResponse({'success': False, 'message': str(e)}, status=500)
