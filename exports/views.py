from django.http import JsonResponse
from django.views.decorators.csrf import csrf_exempt
from django.utils.decorators import method_decorator
from django.views import View
import json
from django.db import connection
from django.http import JsonResponse
import pdb
import csv
from django.http import HttpResponse, JsonResponse

# Create your views here.

@method_decorator(csrf_exempt, name='dispatch')
class SnapshotRegionExport(View):
    def get(self, request, *args, **kwargs):
        try:
            data = json.loads(request.body)
            filters = data.get('filters', {})
            sort_column = data.get('sort_column')
            sort_type = data.get('sort_type')
            email = data.get("email","")
            dashboard_type = data.get("dashboard_type","")
            where_conditions = []
            params = []
            order_by_clause = ""

            if dashboard_type == "Region":
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
            elif dashboard_type == "Channel":
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
            else:
                filter_mappings = {
                    "Timescale": "FormattedDate",
                    "Market_Segment": "Segments",
                    "Competitive_Set": "Brand",
                    "Channel": "ChannelName",
                    "Protein_Type": "ProteinType",
                    "Category": "Category",
                    "Item":"Product"
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

            with connection.cursor() as cursor:
                if sort_column and sort_type:
                    order_by_clause = f"ORDER BY {sort_column} {sort_type}"
                
                if dashboard_type == "Region":
                    query = f'''
                        SELECT Product, Brand, Birmingham, Belfast, Cardiff, Glasgow, Liverpool, Leeds, Manchester, London, Bristol
                        FROM SnapshotByRegionView sbrv 
                        {where_clause}
                        {order_by_clause} '''
                elif dashboard_type == "Channel":
                    query = '''
                        SELECT Product, Brand, "Dine In", "Delivery Average", "Delivery/DineIn", "UberEats", "Deliveroo", "JustEat"
                        FROM SnapshotByChannelView
                        '''
                    if where_conditions:
                        query += ' WHERE ' + ' AND '.join(where_conditions)
                    if order_by_clause:
                        query += ' ' + order_by_clause
                else:
                    query = '''
                        SELECT Product, Brand, MinPrice, MaxPrice, AvgPrice, ModePrice, Variation 
                        FROM SnapshotByVariation
                        '''
                    if where_conditions:
                        query += ' WHERE ' + ' AND '.join(where_conditions)
                    if order_by_clause:
                        query += ' ' + order_by_clause

                cursor.execute(query, params)
                user_data = cursor.fetchall()

                if request.GET.get('format') == 'csv':
                    csv_response = HttpResponse(content_type='text/csv')
                    csv_response['Content-Disposition'] = 'attachment; filename="data.csv"'

                    csv_writer = csv.writer(csv_response)
                    csv_writer.writerow([desc[0] for desc in cursor.description])  # Write header
                    for row in user_data:
                        csv_writer.writerow(row)

                    return csv_response
                else:
                    keys = [desc[0] for desc in cursor.description]
                    result = [dict(zip(keys, row)) for row in user_data]
                    response_data = {
                        "success": True,
                        "data": result
                    }
                    return JsonResponse(response_data, status=200)

        except Exception as e:
            return JsonResponse({'success': False, 'message': str(e)}, status=500)