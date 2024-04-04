from django.http import JsonResponse
from django.views.decorators.csrf import csrf_exempt
from django.utils.decorators import method_decorator
from django.views import View
import json
from django.db import connection
from django.http import JsonResponse
from datetime import datetime
from dateutil.relativedelta import relativedelta
from collections import defaultdict
import pdb
from django.views.decorators.cache import cache_page

@method_decorator(csrf_exempt, name='dispatch')
class MV_Products(View):
    def post(self, request, *args, **kwargs):
        try:
            where_conditions = []
            params = []
            data = json.loads(request.body)
            page_number = data.get('page_number', 1)
            filters = data.get('filters', {})
            sort_column = data.get('sort_column')
            sort_type = data.get('sort_type')
            email = data.get('email')
            records_per_page = 50
            offset = (page_number - 1) * records_per_page

            filter_mappings = {
                        "Timescales": "Period",
                        "Market_Segment": "Segments",
                        "Competitive_Set": "Brand",
                        "Category": "Category",
                        "Protein_Type": "ProteinType",
                        "Item":"Product",
                        "Chain":"Brand",
                        "Product":"Product",
                        "ProteinType":"ProteinType",
                        "Price":"Price"
                    }
            for filter_name, filter_values in filters.items():
                if filter_values:
                    column_name = filter_mappings.get(filter_name)
                    if column_name:
                        if filter_name == "Timescales":
                            if len(filter_values) == 2:
                                # Handle range of dates
                                from_date = datetime.strptime(filter_values[0].strip(), "%Y-%m-%d")
                                to_date = datetime.strptime(filter_values[1].strip(), "%Y-%m-%d")
                                from_date = (from_date - relativedelta(months=1)).strftime("%Y-%m-%d")  # Adjust to previous month
                                where_conditions.append(f"((CONVERT(datetime, {column_name}, 5) >= %s AND CONVERT(datetime, {column_name}, 5) <= %s))")
                                params.extend([from_date, to_date])
                        else:
                            where_conditions.append(f"{column_name} IN ({', '.join(['%s' for _ in range(len(filter_values))])})")
                            params.extend(filter_values)
                else:
                    column_name = filter_mappings.get(filter_name)
                    if filters["Competitive_Set"]==[] and column_name =="Brand":
                                with connection.cursor() as cursor:
                                    cursor.execute(f'''
                                        SELECT mo.Chains
                                            FROM MetaOrganization mo
                                            JOIN user_management um ON mo.Organization = um.Organization 
                                            WHERE um.Email = '{email}'
                                        ''',
                                        )
                                    user_data = cursor.fetchall()
                                    user_data_list = [brand.strip() for brand in user_data[0][0].split(',')]
                                where_conditions.append(f"{column_name} IN ({', '.join(['%s' for _ in range(len(user_data_list))])})")
                                params.extend(user_data_list)
                            

            where_clause = ''
            if where_conditions:
                where_clause = 'WHERE ' + ' AND '.join(where_conditions)

            order_by_clause = ''
            if sort_column and sort_type:
                order_by_clause = f"ORDER BY {filter_mappings[sort_column]} {sort_type}"

            with connection.cursor() as cursor:
                cursor.execute(f'''
                    SELECT Chain, Category, ProteinType, Item, Price, Picture
                    FROM MVProduct 
                    {where_clause}
                    {order_by_clause}
                    OFFSET %s ROWS FETCH NEXT %s ROWS ONLY ''',
                    params+ [offset, records_per_page])

                user_data = cursor.fetchall()

                cursor.execute('SELECT COUNT(*) FROM MVProduct')
                total_count = cursor.fetchone()[0]

                keys = ['Chain', 'Category', 'ProteinType', 'Item', 'Price', 'Picture']
                result = []

                for row in user_data:
                    obj = dict(zip(keys, row))
                    result.append(obj)
                response_data = {
                    "success": True,
                    "data": result,
                    "total_count": total_count
                }
            return JsonResponse(response_data, status=200)
        except Exception as e:
            return JsonResponse({'success': False, 'message': str(e)}, status=500)