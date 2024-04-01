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
import random
from django.core.mail import send_mail
from django.utils.html import strip_tags
from django.template.loader import render_to_string


# def show_urls():
#     urlconf = get_resolver()
#     for value in urlconf.reverse_dict.values():
#         print(f"URL Pattern: {value}")


pwo = PasswordGenerator()
random_password = pwo.generate()

SECRET_KEY = 'The-secret-key'  # Replace with a strong, secret key
ALGORITHM = 'HS256'

@method_decorator(csrf_exempt, name='dispatch')
class CreateUserAPI(View):
    def post(self, request, *args, **kwargs):
        try:
            # Get data from the frontend
            data = json.loads(request.body)
            # username = data.get('user_name', '')
            first_name = data.get('first_name', '')
            last_name = data.get('last_name')
            email = data.get('email', '')
            # roles = data.get('roles', '')
            password = random_password
            phone_no = data.get("phone_number", '')
            organization = data.get("organization", '')
            random_no = random.randint(100, 999)
            user_name = first_name+'_'+last_name+str(random_no)
            # if phone_no.startswith('0'):
            #     if len(phone_no)>11:
            #         return JsonResponse({'success': False, 'message': 'Kindly enter a valid phone number'}, status=400)
            # else:
            #     if len(phone_no)>10 or len(phone_no)<10:
            #         return JsonResponse({'success': False, 'message': 'Kindly enter a valid phone number'}, status=400)

            # Check if the username already exists
            with connection.cursor() as cursor:
                cursor.execute("SELECT COUNT(*) FROM user_management WHERE Username = %s", [user_name])
                result_username = cursor.fetchone()
                if result_username[0] > 0:
                    # Username already exists, return error message
                    return JsonResponse({'success': False, 'message': 'Username already exists'}, status=400)

            # Check if the email already exists
            with connection.cursor() as cursor:
                cursor.execute("SELECT COUNT(*) FROM user_management WHERE Email = %s", [email])
                result_email = cursor.fetchone()
                if result_email[0] > 0:
                    # Email already exists, return error message
                    return JsonResponse({'success': False, 'message': 'Email already exists'}, status=400)

            # Generate token
            token_data = {
                'email': email,
                'exp': datetime.utcnow() + timedelta(hours=1)  # Token expiration time
            }
            token = jwt.encode(token_data, SECRET_KEY, algorithm=ALGORITHM)

            # Insert user data into the database
            query = f'''INSERT INTO user_management (Username, Password, remember_token, FirstName, LastName, Email, PhoneNumber, CreatedAt, inserted_by, updated_by, LastLogin, IsActive, Roles, Organization)
                        VALUES ('{user_name}', '{password}', '{token}', '{first_name}', '{last_name}', '{email}', '{phone_no}', GETDATE(), 'Super Admin', '', '', 1, 'User', '{organization}');'''
            with connection.cursor() as cursor:
                cursor.execute(query)
            name = data["first_name"]+' '+data["last_name"]
            message = f"Welcome {name}. Your account has been created. Your username is {user_name} and password is {password}."

# Prepare the HTML content
            html_message = render_to_string('email_template2.html', {'name': name, 'email': email, 'password': password})

            # Send the email
            send_mail(
                f"Welcome {name}",
                message,
                "anshumankumar271123@gmail.com",
                [data["email"]],
                html_message=html_message,  # Provide HTML content here
                fail_silently=False,
            )      
            # Format the response JSON
            response_data = {
                "success": True,
                "data": {
                    "token": token,
                    "name": first_name + ' ' + last_name,
                    "roles": 'User'
                },
                "message": "User created successfully."
            }

            return JsonResponse(response_data, status=200)

        except Exception as e:
            # Handle other exceptions
            return JsonResponse({'success': False, 'message': str(e)}, status=500)
