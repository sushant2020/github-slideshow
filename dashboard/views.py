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

@method_decorator(csrf_exempt,name='dispatch')
class Dashboard(View):
    def post(self, request, *args, **kwargs):
        try:
            data = json.loads(request.body)
            email = data.get('email','')
            filters = data.get('filters', {})
            variations = {}
            variations_mychain = {}
            if filters["Timescale"] == "Vs Last Month":
                from_date = filters["Select_Date"]
                given_date = datetime.strptime(from_date, "%b-%y")
                to_date = given_date - timedelta(days=given_date.day)
                to_date = to_date.strftime("%b-%y")

            elif filters["Timescale"] == "Vs Last Year":
                from_date = filters["Select_Date"]
                given_date = datetime.strptime(from_date, "%b-%y")
                one_year_before = given_date - relativedelta(years=1)
                to_date = one_year_before.strftime("%b-%y")

            with connection.cursor() as cursor:
                                            cursor.execute(f'''
                                                SELECT mo.OrganizationChain
                                                    FROM MetaOrganization mo
                                                    JOIN user_management um ON mo.Organization = um.Organization 
                                                    WHERE um.Email = '{email}'
                                                ''',
                                                )
                                            user_data = cursor.fetchone()

            query = f'''select AsOfDate,DataType, sum(Value) from dbo.vw_MVDashboard vm
                        where vm.AsOfDate in ('{from_date}', '{to_date}')
                        and BrandName in {tuple(filters["Competitive_Set"])} 
                        and BrandName not in ('{user_data[0]}') 
                        group by AsOfDate,DataType'''
            
            with connection.cursor() as cursor:
                    cursor.execute(query)
                    dashboard_data = cursor.fetchall()
            data_dict = {(month, datatype): value for month, datatype, value in dashboard_data}

        # Calculate variation
            for (current_month, datatype), current_value in data_dict.items():
                if current_month == from_date:
                    prev_month = to_date
                    prev_value = data_dict.get((prev_month, datatype))
                    if prev_value is not None:
                        variation = ((current_value / prev_value) - 1) * 100
                        variations[datatype] = f"{variation:.2f}"

            query2 = f'''select AsOfDate,DataType, sum(Value) from dbo.vw_MVDashboard vm
                        where vm.AsOfDate in ('{from_date}', '{to_date}')
                        and BrandName in ('{user_data[0]}') 
                        group by AsOfDate,DataType'''

            with connection.cursor() as cursor:
                   cursor.execute(query2)
                   my_dashboard_data = cursor.fetchall()
            my_data_dict = {(month, datatype): value for month, datatype, value in my_dashboard_data}
            for (current_month, datatype), current_value in my_data_dict.items():
                if current_month == from_date:
                    prev_month = to_date
                    prev_value = my_data_dict.get((prev_month, datatype))
                    if prev_value is not None:
                        variation = ((current_value / prev_value) - 1) * 100
                        variations_mychain[datatype] = f"{variation:.2f}"
            response_data = {
                            "success": True,
                            "data": variations,
                            "my_data":variations_mychain}
            
            return JsonResponse(response_data, status=200)
        except Exception as e:
                return JsonResponse({'success': False, 'message': str(e)}, status=500)