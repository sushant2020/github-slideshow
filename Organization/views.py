from django.http import JsonResponse
from django.views.decorators.csrf import csrf_exempt
from django.utils.decorators import method_decorator
from django.views import View
from django.db import connection
import pdb
from django.http import JsonResponse
from django.core.paginator import Paginator
import json
import random
from datetime import datetime, timedelta
import jwt
from password_generator import PasswordGenerator
from django.core.mail import send_mail


pwo = PasswordGenerator()
random_password = pwo.generate()

SECRET_KEY = 'The-secret-key'  # Replace with a strong, secret key
ALGORITHM = 'HS256'

@method_decorator(csrf_exempt, name='dispatch')
class AddOrganization(View):
    def post(self, request, *args, **kwargs):
        try:
            # header_dict = request.headers
            # token = header_dict["Authorization"].replace('Bearer ','') 
            
            # decoded_data = jwt.decode(token, SECRET_KEY, algorithms=[ALGORITHM])

            data = json.loads(request.body)
            organization = data.get('organization', '')
            chains = data.get('chains','')
            country = data.get('country','')
            add_chain = []
            for i in chains:
                if i not in add_chain:
                    add_chain.append(i)
                    
            with connection.cursor() as cursor:
    

                cursor.execute(f'''
                    insert into MetaOrganization (
                        Organization,
                        Chains,
                        Country,
                        added_date,
                        is_active) values ('{organization}','{chains}','{country}',GETDATE(),1)
                    ''',
                    )
                
            
                    # else:
                    #     query = f'''INSERT INTO user_management (Username, Password, remember_token, FirstName, LastName, Email, PhoneNumber, CreatedAt, inserted_by, updated_by, LastLogin, IsActive, Roles, Organization)
                    #     VALUES ('{user_name}', '{password}', '{token}', '{user["First_name"]}', '{user["Last_name"]}', '{user["email"]}', '', GETDATE(), 'Super Admin', '', '', 1, 'User', '{organization}');'''
                    #     user_cursor.execute(query)


                response_data = {
                    "success": True,
                    "message": "Organization data added succesfully" ,
                }

                return JsonResponse(response_data, status=200)
        # except jwt.ExpiredSignatureError:
        #     # Token has expired
        #     return JsonResponse({'success': False, 'message': 'Token has expired'}, status=401)

        # except jwt.InvalidTokenError:
        #     # Invalid token
        #     return JsonResponse({'success': False, 'message': 'Invalid token'}, status=401)   
        except Exception as e:

            return JsonResponse({'success': False, 'error': str(e), 'message':'Failed to add organization'}, status=500)  

@method_decorator(csrf_exempt, name='dispatch')
class EditOrganization(View):
    def post(self, request, *args, **kwargs):
        try:
            # header_dict = request.headers
            # token = header_dict["Authorization"].replace('Bearer ','') 
            
            # decoded_data = jwt.decode(token, SECRET_KEY, algorithms=[ALGORITHM])
            data = json.loads(request.body)
            organization = data.get('organization', '')
            chains = data.get('chains','')
            country = data.get('country','')
 
            if chains and country:

                with connection.cursor() as cursor:
        

                    cursor.execute(f'''
                        UPDATE MetaOrganization
                        SET Chains = '{chains}',
                            Country = '{country}',
                            updated_date = GETDATE()
                        WHERE Organization = '{organization}'
                        ''',
                        )
                
                    response_data = {
                        "success": True,
                        "message": "Organization information updated succesfully" ,
                    }

                    return JsonResponse(response_data, status=200)

            elif country:
                with connection.cursor() as cursor:
        

                    cursor.execute(f'''
                        UPDATE MetaOrganization
                        SET Country = '{country}',
                        updated_date = GETDATE()
                        WHERE Organization = '{organization}'
                        ''',
                        )
                
                    response_data = {
                        "success": True,
                        "message": "Organization information updated succesfully" ,
                    }

                    return JsonResponse(response_data, status=200)

            elif chains:

                with connection.cursor() as cursor:
        

                    cursor.execute(f'''
                        UPDATE MetaOrganization
                        SET Chains = '{chains}',
                        updated_date = GETDATE()
                        WHERE Organization = '{organization}'
                        ''',
                        )
                
                    response_data = {
                        "success": True,
                        "message": "Chains updated succesfully" ,
                    }

                    return JsonResponse(response_data, status=200)


        # except jwt.ExpiredSignatureError:
        #     # Token has expired
        #     return JsonResponse({'success': False, 'message': 'Token has expired'}, status=401)

        # except jwt.InvalidTokenError:
        #     # Invalid token
        #     return JsonResponse({'success': False, 'message': 'Invalid token'}, status=401)
        except Exception as e:

            return JsonResponse({'success': False, 'error': str(e), 'message':'Failed to add organization'}, status=500)  

@method_decorator(csrf_exempt, name='dispatch')
class DeleteOrganization(View):
    def post(self, request, *args, **kwargs):
        try:
            # header_dict = request.headers
            # token = header_dict["Authorization"].replace('Bearer ','') 
            
            # decoded_data = jwt.decode(token, SECRET_KEY, algorithms=[ALGORITHM])
            data = json.loads(request.body)
            organization = data.get('organization', '')
            with connection.cursor() as cursor:
        

                    cursor.execute(f'''
                        UPDATE MetaOrganization
                        SET is_active = 0,
                            deleted_date = GETDATE()
                        WHERE Organization = '{organization}'
                        ''',
                        )
                    
                    query = f'''UPDATE user_management
                        SET IsActive = 0
                        WHERE Organization = '{organization}';
                    ''' 
                    
                    cursor.execute(query)

                    response_data = {
                        "success": True,
                        "message": "Organization deleted succesfully" ,
                    }

                    return JsonResponse(response_data, status=200)
        # except jwt.ExpiredSignatureError:
        #     # Token has expired
        #     return JsonResponse({'success': False, 'message': 'Token has expired'}, status=401)

        # except jwt.InvalidTokenError:
        #     # Invalid token
        #     return JsonResponse({'success': False, 'message': 'Invalid token'}, status=401)
        except Exception as e:

            return JsonResponse({'success': False, 'error': str(e), 'message':'Failed to delete organization'}, status=500)  

@method_decorator(csrf_exempt, name='dispatch')
class CommonOrganizationDropdown(View):
    def get(self, request, *args, **kwargs):
        try:
            # header_dict = request.headers
            # token = header_dict["Authorization"].replace('Bearer ','') 
            
            # decoded_data = jwt.decode(token, SECRET_KEY, algorithms=[ALGORITHM])
            with connection.cursor() as cursor_competitive:
                cursor_competitive.execute(
                    "SELECT DISTINCT BrandName FROM Brands b"
                )
                filters = cursor_competitive.fetchall()
                result_array_competitive_set = [{"value": item[0], "label": item[0]} for item in filters]

            with connection.cursor() as cursor_segment:
                cursor_segment.execute(
                    "SELECT DISTINCT Segments FROM MetaSegmentCodes msc ;"
                )
                filters = cursor_segment.fetchall()
                result_array_segment = [{"value": item[0], "label": item[0]} for item in filters]

                cursor_segment.execute("SELECT DISTINCT Country FROM MetaOrganization")
                country = cursor_segment.fetchall()
                result_array_country = [{"value": item[0], "label": item[0]} for item in country]


            

                response_data = {
                    "success": "true",
                    "competitive_set": result_array_competitive_set,
                    "segments" : result_array_segment,
                    "country" : result_array_country
                }
                return JsonResponse(response_data, status=200)
        # except jwt.ExpiredSignatureError:
        #     # Token has expired
        #     return JsonResponse({'success': False, 'message': 'Token has expired'}, status=401)

        # except jwt.InvalidTokenError:
        #     # Invalid token
        #     return JsonResponse({'success': False, 'message': 'Invalid token'}, status=401)
        except Exception as err:
            return JsonResponse({'success': False, 'message': str(err)}, status=500)