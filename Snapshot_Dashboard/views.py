from django.http import JsonResponse
from django.views.decorators.csrf import csrf_exempt
from django.utils.decorators import method_decorator
from django.views import View
import json
from django.db import connection
from django.http import JsonResponse
from datetime import datetime, timedelta
import pdb
import csv
from django.http import HttpResponse, JsonResponse

SECRET_KEY = 'Razor@0666!!!' 
ALGORITHM = 'HS256'

@method_decorator(csrf_exempt, name='dispatch')
class SnapshotRegionAPI(View):
    def post(self, request, *args, **kwargs):
        try:
            data = json.loads(request.body)
            page_number = data.get('page_number', 1)
            filters = data.get('filters', {})
            sort_column = data.get('sort_column')
            sort_type = data.get('sort_type')
            email = data.get("email","")
            records_per_page = 20 

            offset = (page_number - 1) * records_per_page

            where_conditions = []
            params = []

            filter_mappings = {
                "Timescale": "FormattedDate",
                "Market_Segment": "Segments",
                "Competitive_Set": "Brand",
                "Category": "Category",
                "Protein_Type": "ProteinType",
                "Channel": "ChannelName",
                "Item" : "Product",
                "City":"City"
            }

            for filter_name, filter_values in filters.items():
                if filter_values:
                    column_name = filter_mappings.get(filter_name)
                    if column_name !="City":
                        if column_name:
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
                    if filter_name != "City":
                        if filter_name in filter_mappings:
                            column_name = filter_mappings[filter_name]
                            where_conditions.append(f"{column_name} IS NOT NULL")

            where_clause = ''
            if where_conditions:
                where_clause = 'WHERE ' + ' AND '.join(where_conditions)


            order_by_clause = ''
            if sort_column and sort_type:
                order_by_clause = f"ORDER BY {sort_column} {sort_type}"



            with connection.cursor() as cursor:
                if any(filters.values()):
                    # Query to get total count after applying filters
                    cursor.execute(f'SELECT COUNT(*) FROM SnapshotByRegionView sbrv {where_clause}', params)
                    total_count = cursor.fetchone()[0]
                else:
                    # Query to get total count without applying filters
                    cursor.execute('SELECT COUNT(*) FROM SnapshotByRegionView')
                    total_count = cursor.fetchone()[0]

                cursor.execute(f'''
                    SELECT Product, Brand, Birmingham, Belfast, Cardiff, Glasgow, Liverpool, Leeds, Manchester, London, Bristol
                    FROM SnapshotByRegionView sbrv 
                    {where_clause}
                    {order_by_clause}
                    OFFSET %s ROWS FETCH NEXT %s ROWS ONLY ''',
                    params + [offset, records_per_page])

                user_data = cursor.fetchall()
                keys = [
                    "Product", "Brand","Birmingham", "Belfast", "Cardiff", "Glasgow", "Liverpool", 
                    "Leeds", "Manchester", "London", "Bristol"]

                result = []

                for row in user_data:
                    obj = dict(zip(keys, row))
                    result.append(obj)
                response_data = {
                    "success": True,
                    "data": result,
                    "total_count": total_count
                }

                if request.GET.get('format') == 'csv':
                    # If format=csv is provided in the query parameters, return CSV response
                    csv_response = HttpResponse(content_type='text/csv')
                    csv_response['Content-Disposition'] = 'attachment; filename="data.csv"'
    
                    csv_writer = csv.writer(csv_response)
                    csv_writer.writerow(keys)  # Write header
                    for row in user_data:
                        csv_writer.writerow(row)
    
                    return csv_response
                else:
                    return JsonResponse(response_data, status=200)
                
        except Exception as e:

            return JsonResponse({'success': False, 'message': str(e)}, status=500)


# class SnapshotRegionAPI(View):
#     def post(self, request, *args, **kwargs):
#         try:
#             data = json.loads(request.body)
#             page_number = data.get('page_number', 1)
#             filters = data.get('filters', {})
#             sort_column = data.get('sort_column')
#             sort_type = data.get('sort_type')
#             records_per_page = 20 

#             offset = (page_number - 1) * records_per_page

#             where_conditions = []
#             params = []

#             filter_mappings = {
#                 "Timescale": "FormattedDate",
#                 "Market_Segment": "Segments",
#                 "Competitive_Set": "Brand",
#                 "Category": "Category",
#                 "Protein_Type": "ProteinType",
#                 "Channel": "ChannelName",
#                 "Item" : "Product",
#                 "City" : "City"
#             }

#             for filter_name, filter_values in filters.items():
#                 if filter_values:
#                     column_name = filter_mappings.get(filter_name)
#                     if column_name !="City":
#                         if column_name:
#                             where_conditions.append(f"{column_name} IN ({', '.join(['%s' for _ in range(len(filter_values))])})")
#                             params.extend(filter_values)
#                 else:
#                     if filter_name != "City":
#                         if filter_name in filter_mappings:
#                             column_name = filter_mappings[filter_name]
#                             where_conditions.append(f"{column_name} IS NOT NULL")

#             where_clause = ''
#             if where_conditions:
#                 where_clause = 'WHERE ' + ' AND '.join(where_conditions)


#             order_by_clause = ''
#             if sort_column and sort_type:
#                 order_by_clause = f"ORDER BY {sort_column} {sort_type}"

#             cities = filters.get("City", [])
#             if cities == ["All"]:
#                 keys = [
#                     "Product", "Brand","Birmingham", "Belfast", "Cardiff", "Glasgow", "Liverpool", 
#                     "Leeds", "Manchester", "London", "Bristol"]
#             else:
#                 keys = ["Product", "Brand"]
#                 keys.extend(cities)
        
#             with connection.cursor() as cursor:
#                 if any(filters.values()):
#                     # Query to get total count after applying filters
#                     cursor.execute(f'SELECT COUNT(*) FROM SnapshotByRegionView sbrv {where_clause}', params)
#                     total_count = cursor.fetchone()[0]
#                 else:
#                     # Query to get total count without applying filters
#                     cursor.execute('SELECT COUNT(*) FROM SnapshotByRegionView')
#                     total_count = cursor.fetchone()[0]
#                 if cities == ["All"]:
#                     select_query = "SELECT Product, Brand, Birmingham, Belfast, Cardiff, Glasgow, Liverpool, Leeds, Manchester, London, Bristol FROM SnapshotByRegionView sbrv"
#                 else:
#                     select_query = f"SELECT Product, Brand, {', '.join(cities)} FROM SnapshotByRegionView sbrv"

#                 cursor.execute(f'''
#                     {select_query}
#                     {where_clause}
#                     {order_by_clause}
#                     OFFSET %s ROWS FETCH NEXT %s ROWS ONLY ''',
#                     params + [offset, records_per_page])

#                 user_data = cursor.fetchall()
                

#                 result = []

#                 for row in user_data:
#                     obj = dict(zip(keys, row))
#                     result.append(obj)

#                 response_data = {
#                     "success": True,
#                     "data": result,
#                     "total_count": total_count
#                 }

#                 return JsonResponse(response_data, status=200)
                
#         except Exception as e:

#             return JsonResponse({'success': False, 'message': str(e)}, status=500)







@method_decorator(csrf_exempt, name='dispatch')
class SnapshotChannelAPI(View):
    def post(self, request, *args, **kwargs):
        try:
            # Mapping of filter names to database column names
            filter_mappings = {
                "Timescale": "FormattedDate",
                "Market_Segment": "Segments",
                "Competitive_Set": "Brand",
                "City": "City",
                "Protein_Type": "ProteinType",
                "Category": "Category",
                "Dine_In" : "Dine In",
                "Delivery_Average":"Delivery Average",
                "DineIn_Delivery":"Delivery/DineIn",
                "Product":"Product",
                "UberEats":"UberEats",
                "Deliveroo":"Deliveroo",
                "JustEat":"JustEat",
                "Item":"Product",
                "Brand":"Brand",
                "Uber Eats":"UberEats"
            }
            data = json.loads(request.body)
            page_number = data.get('page_number', 1)
            filters = data.get('filters', {})
            sort_column = data.get('sort_column')
            sort_type = data.get('sort_type')
            page_size = 20
            email = data.get("email","")

            # Construct WHERE clause for filters
            where_conditions = []
            params = []
            for key, value in filters.items():
                if value:  # Check if the filter value is not empty
                    column_name = filter_mappings.get(key)
                    if column_name:
                        where_conditions.append(f"{column_name} IN ({', '.join(['%s' for _ in value])})")
                        params.extend(value)
                else:
                    column_name = filter_mappings.get(key)
                    if filters["Competitive_Set"]==[] and column_name =="Brand" :
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
                      


            # Construct ORDER BY clause for sorting
            order_by_clause = ''
            if sort_column and sort_type:
                order_by_clause = f'ORDER BY "{filter_mappings[sort_column]}" {sort_type}'

            # Construct LIMIT and OFFSET for pagination
            offset = (page_number - 1) * page_size
            limit_offset_clause = f'OFFSET {offset} ROWS FETCH NEXT {page_size} ROWS ONLY'

            # Construct the SQL query
            query = f'''
                SELECT Product, Brand, "Dine In", "Delivery Average", "Delivery/DineIn", "UberEats", "Deliveroo", "JustEat"
                FROM SnapshotByChannelView
            '''
            if where_conditions:
                where_clause = 'WHERE ' + ' AND '.join(where_conditions)
                query += ' WHERE ' + ' AND '.join(where_conditions)
            if order_by_clause:
                query += ' ' + order_by_clause
            query += ' ' + limit_offset_clause
            # Execute the SQL query
            with connection.cursor() as cursor:
                cursor.execute(query, params)
                user_data = cursor.fetchall()

            # Serialize data
            keys = ["Product", "Brand","Dine_In", "Delivery_Average", "DineIn_Delivery", "UberEats", "Deliveroo", "JustEat"]
            result = [dict(zip(keys, row)) for row in user_data]

            # Get total count for pagination
            with connection.cursor() as cursor:
                if any(filters.values()):
                    # Query to get total count after applying filters
                    cursor.execute(f'SELECT COUNT(*) FROM SnapshotByChannelView sbrv {where_clause}', params)
                    total_count = cursor.fetchone()[0]
                else:
                    # Query to get total count without applying filters
                    cursor.execute('SELECT COUNT(*) FROM SnapshotByChannelView')
                    total_count = cursor.fetchone()[0]

            response_data = {
                "success": True,
                "data": result,
                "total_count": total_count
            }
            return JsonResponse(response_data, status=200)

        except Exception as e:
            return JsonResponse({'success': False, 'message': str(e)}, status=500)




@method_decorator(csrf_exempt, name='dispatch')
class SnapshotVariationAPI(View):
    def post(self, request, *args, **kwargs):
        try:
            
            last_month = (datetime.today().replace(day=1) - timedelta(days=1)).strftime('%b-%y')
            print(last_month)

            filter_mappings = {
                "Timescale": "FormattedDate",
                "Market_Segment": "Segments",
                "Competitive_Set": "Brand",
                "Channel": "ChannelName",
                "Protein_Type": "ProteinType",
                "Category": "Category",
                "Item":"Product"
            }
            # Get page number, filters, sort_column, and sort_type from request data
            data = json.loads(request.body)
            page_number = data.get('page_number', 0)  # Start from page 0 or adjust as needed
            filters = data.get('filters', {})
            sort_column = data.get('sort_column', '')
            sort_type = data.get('sort_type', '')
            email = data.get("email","")
            page_size = 20
            # Define base query
            where_conditions = []
            params = []
            for key, value in filters.items():

                if value:  # Check if the filter value is not empty
                    column_name = filter_mappings.get(key)
                    if column_name:
                        where_conditions.append(f"{column_name} IN ({', '.join(['%s' for _ in value])})")
                        params.extend(value)
                else:
                    column_name = filter_mappings.get(key)
                    if filters["Competitive_Set"]==[] and column_name =="Brand" :
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
            
            order_by_clause = ''
            if sort_column and sort_type:
                order_by_clause = f'ORDER BY "{sort_column}" {sort_type}'
            
            offset = (page_number - 1) * page_size
            limit_offset_clause = f'OFFSET {offset} ROWS FETCH NEXT {page_size} ROWS ONLY'
            
            query = '''
                SELECT Product, Brand, MinPrice, MaxPrice, AvgPrice, ModePrice, Variation 
                FROM SnapshotByVariation
            '''
            if where_conditions:
                where_clause = 'WHERE ' + ' AND '.join(where_conditions)
                query += ' WHERE ' + ' AND '.join(where_conditions)
            if order_by_clause:
                query += ' ' + order_by_clause
            query += ' ' + limit_offset_clause
            


            with connection.cursor() as cursor:
                cursor.execute(query, params)
                user_data = cursor.fetchall()
            
            keys = ["Product", "Brand","MinPrice", "MaxPrice", "AvgPrice", "ModePrice", "Variation"]

            result = [dict(zip(keys, row)) for row in user_data]
           
            
            with connection.cursor() as cursor:
                if any(filters.values()):
                    # Query to get total count after applying filters
                    cursor.execute(f'SELECT COUNT(*) FROM SnapshotByVariation sbrv {where_clause}', params)
                    total_count = cursor.fetchone()[0]
                else:
                    # Query to get total count without applying filters
                    cursor.execute('SELECT COUNT(*) FROM SnapshotByVariation')
                    total_count = cursor.fetchone()[0]
            
            
            response_data = {
                    "success": True,
                    "data": result,
                    "total_count": total_count
                }
            return JsonResponse(response_data, status=200)
        except Exception as e:
            # Handle other exceptions
            return JsonResponse({'success': False, 'message': str(e)}, status=500)
