from django.shortcuts import render
from django.http import JsonResponse
from django.views.decorators.csrf import csrf_exempt
from django.utils.decorators import method_decorator
from django.views import View
import json
from django.db import connection
import jwt

SECRET_KEY = 'Razor@0666!!!'  
ALGORITHM = 'HS256'

# Create your views here.
@method_decorator(csrf_exempt, name='dispatch')
class GetUserAPI(View):
    def post(self, request, *args, **kwargs):
        try:
            # header_dict = request.headers
            # token = header_dict["Authorization"].replace('Bearer ','') 
            
            # decoded_data = jwt.decode(token, SECRET_KEY, algorithms=[ALGORITHM])
            data = json.loads(request.body)
            board_type = data.get('board_type', '')
            page_number = data.get('page_number',1)
            
            # Get data from the frontend
            
            result =[]
            # Check if the username already exists
            if board_type==2:
                sort_column = data.get('sort_column')
                sort_type = data.get('sort_type')
                records_per_page = 20
                offset = (page_number - 1) * records_per_page
                order_by_clause = ''
                if sort_column and sort_type:
                    order_by_clause = f"ORDER BY {sort_column} {sort_type}"
                with connection.cursor() as cursor:
                    cursor.execute(f'''select Organization,Email,FirstName,LastName, Roles, PhoneNumber from user_management um where IsActive  = 1 {order_by_clause} 
                                OFFSET %s ROWS FETCH NEXT %s ROWS ONLY''',[offset, records_per_page])
                    result_username = cursor.fetchall()

                keys = ["Organization", "User","First_Name", "Last_Name","Roles","Phone_number"]

                for row in result_username:
                        obj = dict(zip(keys, row))
                        result.append(obj)
                # Generate token
                
                
                response_data = {
                    "success": True,
                    "data": result,
                    "message": "Users fetched successfully."
                }

                return JsonResponse(response_data, status=200)
            else:
                sort_column = data.get('sort_column')
                sort_type = data.get('sort_type')
                records_per_page = 20
                offset = (page_number - 1) * records_per_page
                order_by_clause = ''
                if sort_column and sort_type:
                    order_by_clause = f"ORDER BY {sort_column} {sort_type}"
                with connection.cursor() as cursor: 
                    cursor.execute(f"select count(*) from MetaOrganization")     
                    total_count = cursor.fetchone()[0]

                    cursor.execute(f'''select Organization , Chains , Country  from MetaOrganization where is_active = 1 {order_by_clause} 
                                    OFFSET %s ROWS FETCH NEXT %s ROWS ONLY''',[offset, records_per_page])
                    organization_data = cursor.fetchall()
                    result = []
                for row in organization_data:
                    obj ={
                        
                        "Organization":row[0],
                        "Chains":row[1],
                        "Country":row[2]
                    }
                    result.append(obj)

                response_data = {
                    "success" : True,
                    "data": result,
                    "total_count":total_count
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
class GetUserProfileAPI(View):
    def post(self, request, *args, **kwargs):
        try:
            # header_dict = request.headers
            # token = header_dict["Authorization"].replace('Bearer ','') 
            
            # decoded_data = jwt.decode(token, SECRET_KEY, algorithms=[ALGORITHM])
            data = json.loads(request.body)
            email = data.get("email",'')
            with connection.cursor() as cursor: 
                cursor.execute(f"select FirstName, LastName,PhoneNumber ,Organization ,Email  from user_management um where um.Email = '{email}'")     
                user_detail = cursor.fetchall()
                keys = ["First_Name", "Last_Name","Phone_number", "Organization", "Email"]
                result = []

                for row in user_detail:
                    obj = dict(zip(keys, row))
                    result.append(obj)
                response_data = {
                    "success": True,
                    "data": result,
                }
            return JsonResponse(response_data, status=200)
        # except jwt.ExpiredSignatureError:
        #     # Token has expired
        #     return JsonResponse({'success': False, 'message': 'Token has expired'}, status=401)

        # except jwt.InvalidTokenError:
        #     # Invalid token
        #     return JsonResponse({'success': False, 'message': 'Invalid token'}, status=401)        
        except Exception as e:

            return JsonResponse({'success': False, 'message': str(e)}, status=500)


           