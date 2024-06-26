from django.http import JsonResponse
from django.views.decorators.csrf import csrf_exempt
from django.utils.decorators import method_decorator
from django.views import View
import json
from django.db import connection
from django.http import JsonResponse
from datetime import datetime, timedelta
from dateutil.relativedelta import relativedelta
from django.http import JsonResponse
import pdb
import jwt

SECRET_KEY = 'Razor@0666!!!'  
ALGORITHM = 'HS256'

@method_decorator(csrf_exempt,name='dispatch')
class Dashboard(View):
    def post(self, request, *args, **kwargs):
        try:
            data = json.loads(request.body)
            email = data.get('email','')
            filters = data.get('filters', {})
            variations = {}
            variations_mychain = {}
       
            if filters["Timescale"]==[]:
                from_date = filters["Select_Date"][0]
                given_date = datetime.strptime(from_date, "%b-%y")
                to_date = given_date - timedelta(days=given_date.day)
                to_date = to_date.strftime("%b-%y")

            elif filters["Timescale"][0] == "Vs Last Year":
                from_date = filters["Select_Date"][0]
                given_date = datetime.strptime(from_date, "%b-%y")
                one_year_before = given_date - relativedelta(years=1)
                to_date = one_year_before.strftime("%b-%y")
            
            elif filters["Timescale"][0] == "Vs Last Month":
                from_date = filters["Select_Date"][0]
                given_date = datetime.strptime(from_date, "%b-%y")
                to_date = given_date - timedelta(days=given_date.day)
                to_date = to_date.strftime("%b-%y")

            with connection.cursor() as cursor:
                cursor.execute(f'''
                    SELECT mo.OrganizationChain
                    FROM MetaOrganization mo
                    JOIN user_management um ON mo.Organization = um.Organization 
                    WHERE um.Email = '{email}'
                ''')
                user_data = cursor.fetchone()
                cursor.execute(f'''
                    SELECT mo.Chains
                    FROM MetaOrganization mo
                    JOIN user_management um ON mo.Organization = um.Organization 
                    WHERE um.Email = '{email}'
                ''')
                user_chain_data = cursor.fetchall()
                user_data_list = [brand.strip() for brand in user_chain_data[0][0].split(',')]

            if len(filters["Competitive_Set"]) > 1:
                query = f'''select AsOfDate,DataType, sum(Value) from dbo.vw_MVDashboard vm
                            where vm.AsOfDate in ('{from_date}', '{to_date}')
                            and BrandName in {tuple(filters["Competitive_Set"])} 
                            and BrandName not in ('{user_data[0]}') 
                            group by AsOfDate,DataType'''
            elif len(filters["Competitive_Set"])==0:
                query = f'''select AsOfDate,DataType, sum(Value) from dbo.vw_MVDashboard vm
                            where vm.AsOfDate in ('{from_date}', '{to_date}')
                            and BrandName in {tuple(user_data_list)} 
                            and BrandName not in ('{user_data[0]}') 
                            group by AsOfDate,DataType'''
            else:
                query = f'''select AsOfDate,DataType, sum(Value) from dbo.vw_MVDashboard vm
                            where vm.AsOfDate in ('{from_date}', '{to_date}')
                            and BrandName =  '{filters["Competitive_Set"][0]}'
                            and BrandName not in ('{user_data[0]}') 
                            group by AsOfDate,DataType'''
            
            with connection.cursor() as cursor:
                cursor.execute(query)
                dashboard_data = cursor.fetchall()

            data_dict = {(month, datatype): value for month, datatype, value in dashboard_data}

            # Calculate variation for competitive set
            for (current_month, datatype), current_value in data_dict.items():
                if current_month == from_date:
                    prev_month = to_date
                    prev_value = data_dict.get((prev_month, datatype))
                    if prev_value is not None and prev_value != 0:
                        if datatype == "Product" or datatype == "Promo":
                            absolute_variation = int(current_value - prev_value)
                            variations[f"absolute_{datatype}"] = str(absolute_variation)
                            variation = ((current_value / prev_value) - 1) * 100
                            variations[datatype] = str(f"{variation:.1f}%")
                            variations[f"donut_{datatype}"] = str(f"{variation:.1f}")
                        else:
                            variation = ((current_value / prev_value) - 1) * 100
                            variations[datatype] = str(f"{variation:.1f}%")
                            variations[f"donut_{datatype}"] = str(f"{variation:.1f}")
                    else:
                        if datatype == "Product" or datatype == "Promo":
                            absolute_variation = int(current_value)
                            variations[f"absolute_{datatype}"] = str(absolute_variation)
                            variations[datatype] = "0.0%"
                        else:
                            variations[datatype] = "0.0%"

            # Query and calculate variation for user's chain
            query2 = f'''select AsOfDate,DataType, sum(Value) from dbo.vw_MVDashboard vm
                        where vm.AsOfDate in ('{from_date}', '{to_date}')
                        and BrandName in ('{user_data[0]}') 
                        group by AsOfDate,DataType'''

            with connection.cursor() as cursor:
                cursor.execute(query2)
                my_dashboard_data = cursor.fetchall()

            # Check if my_dashboard_data is empty
            if not my_dashboard_data:
                my_data_dict = {}
            else:
                my_data_dict = {(month, datatype): value for month, datatype, value in my_dashboard_data}

            # Calculate variation for user's chain
            for (current_month, datatype), current_value in my_data_dict.items():
                if current_month == from_date:
                    prev_month = to_date
                    prev_value = my_data_dict.get((prev_month, datatype))
                    if prev_value is not None and prev_value != 0:
                        if datatype == "Product" or datatype == "Promo":
                            absolute_variation = int(current_value - prev_value)
                            variations_mychain[f"absolute_{datatype}"] = str(absolute_variation)
                            variation = ((current_value / prev_value) - 1) * 100
                            variations_mychain[datatype] = str(f"{variation:.1f}%")
                            variations_mychain[f"donut_{datatype}"] = str(f"{variation:.1f}")
                        else:
                            variation = ((current_value / prev_value) - 1) * 100
                            variations_mychain[datatype] = str(f"{variation:.1f}%")
                            variations_mychain[f"donut_{datatype}"] = str(f"{variation:.1f}")
                    else:
                        if datatype == "Product" or datatype == "Promo":
                            absolute_variation = int(current_value)
                            variations_mychain[f"absolute_{datatype}"] = str(absolute_variation)
                            variations_mychain[datatype] = "0.0%"
                        else:
                            variations_mychain[datatype] = "0.0%"

            # Create response data
            response_data = {
                "success": True,
                "data": variations,
                "my_data": variations_mychain
            }

            return JsonResponse(response_data, status=200)
        except Exception as e:
            return JsonResponse({'success': False, 'message': str(e)}, status=500)