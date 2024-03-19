from django.http import JsonResponse
from django.views.decorators.csrf import csrf_exempt
from django.utils.decorators import method_decorator
from django.views import View
import json
from django.db import connection
from django.http import JsonResponse

import csv
from django.http import HttpResponse, JsonResponse

# Create your views here.

@method_decorator(csrf_exempt, name='dispatch')
class SnapshotRegionExport(View):
    def get(self, request, *args, **kwargs):
        try:
            with connection.cursor() as cursor:

                cursor.execute(f'''
                    SELECT Product, Brand, Birmingham, Belfast, Cardiff, Glasgow, Liverpool, Leeds, Manchester, London, Bristol
                    FROM SnapshotByRegionView sbrv
                    ORDER BY Product ASC
                    OFFSET %s ROWS FETCH NEXT %s ROWS ONLY ''',
                    [1, 50])

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
