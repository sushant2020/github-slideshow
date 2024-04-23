from django.shortcuts import render
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
import jwt

SECRET_KEY = 'Razor@0666!!!'  
ALGORITHM = 'HS256'


@method_decorator(csrf_exempt, name='dispatch')
class EditUserAPI(View):
    def post(self, request, *args, **kwargs):
        try:
            # header_dict = request.headers
            # token = header_dict["Authorization"].replace('Bearer ','') 
            
            # decoded_data = jwt.decode(token, SECRET_KEY, algorithms=[ALGORITHM])
            data = json.loads(request.body)
            
            updates = []

            if 'user_name' in data:
                updates.append(f"Username = '{data['user_name']}'")
            if 'first_name' in data:
                updates.append(f"FirstName = '{data['first_name']}'")
            if 'last_name' in data:
                updates.append(f"LastName = '{data['last_name']}'")
            if 'organization' in data:
                updates.append(f"Organization = '{data['organization']}'")  
            if 'phone_number' in data:
                updates.append(f"PhoneNumber = '{data['phone_number']}'")  
                
            if updates:
                updates.append("updated_at = GETDATE()")  # Always update the updated_date
                updates.append("updated_by = 'Super Admin'")
                update_str = ', '.join(updates)

                with connection.cursor() as cursor:
                    cursor.execute(f'''
                        UPDATE user_management
                        SET {update_str}
                        WHERE Email = '{data['email']}'
                    ''')
                
                
                response_data = {
                    "success": True,
                    "message": "User information updated successfully",
                }
                return JsonResponse(response_data, status=200)
            else:
                return JsonResponse({'success': False, 'message': "No valid data provided for update"}, status=400)
        # except jwt.ExpiredSignatureError:
        #     # Token has expired
        #     return JsonResponse({'success': False, 'message': 'Token has expired'}, status=401)

        # except jwt.InvalidTokenError:
        #     # Invalid token
        #     return JsonResponse({'success': False, 'message': 'Invalid token'}, status=401)
        except Exception as e:
            return JsonResponse({'success': False, 'message': f"Unable to edit user's info: {str(e)}"}, status=500)
