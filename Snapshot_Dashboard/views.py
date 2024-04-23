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
import jwt

SECRET_KEY = 'Razor@0666!!!' 
ALGORITHM = 'HS256'

@method_decorator(csrf_exempt, name='dispatch')
class SnapshotRegionAPI(View):
    def post(self, request, *args, **kwargs):
        try:
            # header_dict = request.headers
            # token = header_dict["Authorization"].replace('Bearer ','') 
            
            # decoded_data = jwt.decode(token, SECRET_KEY, algorithms=[ALGORITHM])
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
                "Product1" : "Product",
                "City":"City",
                "Product" : "Product",
                "Brand":"Brand",
                "Belfast":"Belfast",
                "Birmingham":"Birmingham",
                "Cardiff":"Cardiff",
                "Glasgow":"Glasgow",
                "Liverpool":"Liverpool",
                "Leeds":"Leeds",
                "Manchester":"Manchester",
                "London":"London",
                "Bristol":"Bristol"
            }

            for filter_name, filter_values in filters.items():
                if filter_values:
                    column_name = filter_mappings.get(filter_name)
                    if column_name !="City":
                        if column_name =="ProteinType" and filter_values==["All"]:
                            pass
                        else:
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
            if sort_column and sort_type and sort_column not in ["Product1","Brand","Product"]:

                order_by_clause = f'ORDER BY cast({filter_mappings[sort_column]} As float) {sort_type}'
            else:
                order_by_clause = f'ORDER BY {filter_mappings[sort_column]} {sort_type}'
            

            with connection.cursor() as cursor:
                if any(filters.values()):
                    # Query to get total count after applying filters
                    cursor.execute(f'SELECT COUNT(*) FROM SnapshotByRegionView sbrv {where_clause}', params)
                    total_count = cursor.fetchone()[0]
                else:
                    # Query to get total count without applying filters
                    cursor.execute('SELECT COUNT(*) FROM SnapshotByRegionView')
                    total_count = cursor.fetchone()[0]
                query = f'''
                    SELECT Product, Brand, Birmingham, Belfast, Cardiff, Glasgow, Liverpool, Leeds, Manchester, London, Bristol
                    FROM SnapshotByRegionView sbrv 
                    {where_clause}
                    {order_by_clause}
                    OFFSET %s ROWS FETCH NEXT %s ROWS ONLY '''
            
                cursor.execute(query,
                    params + [offset, records_per_page])

                user_data = cursor.fetchall()
                keys = [
                    "Product1", "Brand","Birmingham", "Belfast", "Cardiff", "Glasgow", "Liverpool", 
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

                
                return JsonResponse(response_data, status=200)
        # except jwt.ExpiredSignatureError:
        #     # Token has expired
        #     return JsonResponse({'success': False, 'message': 'Token has expired'}, status=401)

        # except jwt.InvalidTokenError:
        #     # Invalid token
        #     return JsonResponse({'success': False, 'message': 'Invalid token'}, status=401)     
        except Exception as e:

            return JsonResponse({'success': False, 'message': str(e)}, status=500)



@method_decorator(csrf_exempt, name='dispatch')
class SnapshotChannelAPI(View):
    def post(self, request, *args, **kwargs):
        try:
            # header_dict = request.headers
            # token = header_dict["Authorization"].replace('Bearer ','') 
            
            # decoded_data = jwt.decode(token, SECRET_KEY, algorithms=[ALGORITHM])
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
                "Uber Eats":"UberEats",
                "Product1" : "Product",
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
                        if column_name =="ProteinType" and value==["All"]:
                            pass
                        else:
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
            if sort_column and sort_type and sort_column not in ["Item","Brand","Product1","Product"]:
                order_by_clause = f'ORDER BY cast("{filter_mappings[sort_column]}" As float) {sort_type}'
            
            else:
                order_by_clause = f'ORDER BY {filter_mappings[sort_column]} {sort_type}'
            
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
        # except jwt.ExpiredSignatureError:
        #     # Token has expired
        #     return JsonResponse({'success': False, 'message': 'Token has expired'}, status=401)

        # except jwt.InvalidTokenError:
        #     # Invalid token
        #     return JsonResponse({'success': False, 'message': 'Invalid token'}, status=401)
        except Exception as e:
            return JsonResponse({'success': False, 'message': str(e)}, status=500)




@method_decorator(csrf_exempt, name='dispatch')
class SnapshotVariationAPI(View):
    def post(self, request, *args, **kwargs):
        try:
            # header_dict = request.headers
            # token = header_dict["Authorization"].replace('Bearer ','') 
            
            # decoded_data = jwt.decode(token, SECRET_KEY, algorithms=[ALGORITHM])
            last_month = (datetime.today().replace(day=1) - timedelta(days=1)).strftime('%b-%y')
            print(last_month)

            filter_mappings = {
                "Timescale": "FormattedDate",
                "Market_Segment": "Segments",
                "Competitive_Set": "Brand",
                "Channel": "ChannelName",
                "Protein_Type": "ProteinType",
                "Category": "Category",
                "Item": "Product",
                "Product1" : "Product",
                "Product" : "Product",
                "Brand":"Brand",
                "MinPrice":"MinPrice",
                "MaxPrice":"MaxPrice",
                "AvgPrice":"AvgPrice",
                "ModePrice":"ModePrice",
                "Variation":"Variation"

            }

            data = json.loads(request.body)
            page_number = data.get('page_number', 0)
            filters = data.get('filters', {})
            sort_column = data.get('sort_column', '')
            sort_type = data.get('sort_type', '')
            email = data.get("email", "")
            page_size = 20
            
            # Define base query
            where_conditions = []
            params = []
            for key, value in filters.items():
                if value:
                    column_name = filter_mappings.get(key)
                    if column_name:
                        if column_name =="ProteinType" and value==["All"]:
                            pass
                        else:
                            where_conditions.append(f"{column_name} IN ({', '.join(['%s' for _ in value])})")
                            params.extend(value)

                elif filters["Competitive_Set"] == [] and filter_mappings.get(key) == "Brand":
                    # Retrieve user's chain brands if Competitive_Set filter is empty
                    with connection.cursor() as cursor:
                        cursor.execute(f'''
                            SELECT mo.Chains
                                FROM MetaOrganization mo
                                JOIN user_management um ON mo.Organization = um.Organization 
                                WHERE um.Email = '{email}'
                        ''')
                        user_data = cursor.fetchall()
                        user_data_list = [brand.strip() for brand in user_data[0][0].split(',')]
                    where_conditions.append(f"{filter_mappings.get(key)} IN ({', '.join(['%s' for _ in range(len(user_data_list))])})")
                    params.extend(user_data_list)

            order_by_clause = ''
            if sort_column and sort_type and sort_column not in ["Item","Product1","Product","Brand"]:
                order_by_clause = f'ORDER BY cast("{filter_mappings[sort_column]}" As float) {sort_type}'
            else:
                order_by_clause = f'ORDER BY {filter_mappings[sort_column]} {sort_type}'

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
            
            keys = ["Product", "Brand", "MinPrice", "MaxPrice", "AvgPrice", "ModePrice", "Variation"]

            # Append "%" to Variation values and create response data
            result = [dict(zip(keys, [*row[:-1], f"{row[-1]}%"])) for row in user_data]

            # Get total count
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
        # except jwt.ExpiredSignatureError:
        #     # Token has expired
        #     return JsonResponse({'success': False, 'message': 'Token has expired'}, status=401)

        # except jwt.InvalidTokenError:
        #     # Invalid token
        #     return JsonResponse({'success': False, 'message': 'Invalid token'}, status=401)
        except Exception as e:
            return JsonResponse({'success': False, 'message': str(e)}, status=500)
