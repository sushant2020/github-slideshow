from django.shortcuts import render
import hashlib
import string
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
import random
import string

# def show_urls():
#     urlconf = get_resolver()
#     for value in urlconf.reverse_dict.values():
#         print(f"URL Pattern: {value}")




SECRET_KEY = 'The-secret-key'  # Replace with a strong, secret key
ALGORITHM = 'HS256'

@method_decorator(csrf_exempt, name='dispatch')
class CreateUserAPI(View):
    def post(self, request, *args, **kwargs):
        try:
            # Get data from the frontend
            data = json.loads(request.body)
            first_name = data.get('first_name', '')
            last_name = data.get('last_name')
            email = data.get('email', '')
            phone_no = data.get("phone_number", '')
            organization = data.get("organization", '')
            random_no = random.randint(100, 999)
            user_name = first_name + '_' + last_name + str(random_no)

            # Generate a random password
            pwo = PasswordGenerator()
            random_password = pwo.generate()
            # password_length = 10
            # random_password = ''.join(random.choice(string.ascii_letters + string.digits) for _ in range(password_length))

            # Hash the password using SHA-256
            password_hash = hashlib.sha256(random_password.encode()).hexdigest()

            # Check if the username already exists
            with connection.cursor() as cursor:
                cursor.execute("SELECT COUNT(*) FROM user_management WHERE Username = %s", [user_name])
                result_username = cursor.fetchone()
                if result_username[0] > 0:
                    return JsonResponse({'success': False, 'message': 'Username already exists'}, status=400)

            # Check if the email already exists
            with connection.cursor() as cursor:
                cursor.execute("SELECT COUNT(*) FROM user_management WHERE Email = %s", [email])
                result_email = cursor.fetchone()
                if result_email[0] > 0:
                    return JsonResponse({'success': False, 'message': 'Email already exists'}, status=400)

            # Generate token
            token_data = {
                'email': email,
                'exp': datetime.utcnow() + timedelta(hours=1)
            }
            token = jwt.encode(token_data, SECRET_KEY, algorithm=ALGORITHM)

            # Insert user data into the database
            query = '''INSERT INTO user_management (Username, PasswordHash, remember_token, FirstName, LastName, Email, PhoneNumber, CreatedAt, inserted_by, updated_by, LastLogin, IsActive, Roles, Organization,Password)
                        VALUES (%s, %s, %s, %s, %s, %s, %s, GETDATE(), 'Super Admin', '', '', 1, 'User', %s,%s);'''
            with connection.cursor() as cursor:
                cursor.execute(query, (user_name, password_hash, token, first_name, last_name, email, phone_no, organization,random_password))

            name = data["first_name"] + ' ' + data["last_name"]
            message = f"Welcome {name}. Your account has been created. Your username is {user_name} and password is {random_password}."

            html_message = render_to_string('email_template2.html', {'name': name, 'email': email, 'password': random_password})

            send_mail(
                f"Welcome {name}",
                message,
                "anshumankumar271123@gmail.com",
                [data["email"]],
                html_message=html_message,
                fail_silently=False,
            )

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
            return JsonResponse({'success': False, 'message': str(e)}, status=500)
