from django.http import JsonResponse
from django.views.decorators.csrf import csrf_exempt
from django.utils.decorators import method_decorator
from django.views import View
from django.db import connection
import pdb
from django.http import JsonResponse
import json
from calendar import month_abbr

SECRET_KEY = 'Razor@0666!!!'  
ALGORITHM = 'HS256'

def custom_sort_item(item):
    # Check if the label starts with a digit
    if item['label'][0].isdigit():
        # If it starts with a digit, return a tuple with (0, item['label'])
        # The tuple (0, item['label']) ensures that items starting with digits appear first
        return (0, item['label'])
    else:
        # If it doesn't start with a digit, return a tuple with (1, item['label'])
        # The tuple (1, item['label']) ensures that items not starting with digits appear later and are sorted alphabetically
        return (1, item['label'])

def custom_sort(item):
    month, year = item['label'].split('-')
    return int(year), list(month_abbr).index(month)

@method_decorator(csrf_exempt, name='dispatch')
class Timescalefitler(View):
    def post(self, request, *args, **kwargs):
        try:
            data =json.loads(request.body)
            dashboard_type = data.get('dashboard_type')
            main_dashboard = data.get("main_dashboard")

            if main_dashboard == 'Snapshot':
                if dashboard_type == "Region":
                    with connection.cursor() as cursor:
                        cursor.execute(
                            "SELECT DISTINCT FormattedDate FROM SnapshotByRegionView;"
                        )
                        filters = cursor.fetchall()
                        result_array = [{"value": item[0], "label": item[0]} for item in filters]
                        result_array = sorted(result_array, key=custom_sort)
                    
                    with connection.cursor() as cursor:
                        cursor.execute(
                            "SELECT DISTINCT Product FROM SnapshotByRegionView;"
                        )
                        filter_item = cursor.fetchall()
                        result_item_array = [{"value": item[0], "label": item[0]} for item in filter_item]    
                        result_item_array = sorted(result_item_array, key=custom_sort_item)
                        response_data = {
                            "success": "true",
                            "filters": result_array,
                            "item_filter": result_item_array,
                            "message": "Filter value fetched successfully"
                        }
                        return JsonResponse(response_data, status=200)
                    
                elif dashboard_type == 'Channel':
                    with connection.cursor() as cursor:
                        cursor.execute(
                            "SELECT DISTINCT FormattedDate FROM SnapshotByChannelView;"
                        )
                        filters = cursor.fetchall()
                        result_array = [{"value": item[0], "label": item[0]} for item in filters]
                        result_array = sorted(result_array, key=custom_sort)

                    with connection.cursor() as cursor:
                        cursor.execute(
                            "SELECT DISTINCT Product FROM SnapshotByChannelView;"
                        )
                        filter_item = cursor.fetchall()
                        result_item_array = [{"value": item[0], "label": item[0]} for item in filter_item]    
                        result_item_array = sorted(result_item_array, key=custom_sort_item)
                        response_data = {
                            "success": "true",
                            "filters": result_array,
                            "item_filter": result_item_array,
                            "message": "Filter value fetched successfully"
                        }
                        return JsonResponse(response_data, status=200)
                    
                elif dashboard_type =='Variation':
                    with connection.cursor() as cursor:
                        cursor.execute(
                            "SELECT DISTINCT FormattedDate FROM SnapshotByVariation;"
                        )
                        filters = cursor.fetchall()
                        result_array = [{"value": item[0], "label": item[0]} for item in filters]
                        result_array = sorted(result_array, key=custom_sort)
                    
                    with connection.cursor() as cursor:
                        cursor.execute(
                            "SELECT DISTINCT Product FROM SnapshotByVariation;"
                        )
                        filter_item = cursor.fetchall()
                        result_item_array = [{"value": item[0], "label": item[0]} for item in filter_item]    
                        result_item_array = sorted(result_item_array, key=custom_sort_item)
                        response_data = {
                            "success": "true",
                            "filters": result_array,
                            "item_filter": result_item_array,
                            "message": "Filter value fetched successfully"
                        }
                        return JsonResponse(response_data, status=200)
            else:
                if dashboard_type == "Region":
                    with connection.cursor() as cursor:
                        cursor.execute(
                            "SELECT DISTINCT FormattedDate FROM SnapshotByRegionView;"
                        )
                        filters = cursor.fetchall()
                        result_array = [{"value": item[0], "label": item[0]} for item in filters]
                        result_array = sorted(result_array, key=custom_sort)
                    
                    with connection.cursor() as cursor:
                        cursor.execute(
                            "SELECT DISTINCT Product FROM SnapshotByRegionView;"
                        )
                        filter_item = cursor.fetchall()
                        result_item_array = [{"value": item[0], "label": item[0]} for item in filter_item]    
                        result_item_array = sorted(result_item_array, key=custom_sort_item)
                        response_data = {
                            "success": "true",
                            "filters": result_array,
                            "item_filter": result_item_array,
                            "message": "Filter value fetched successfully"
                        }
                        return JsonResponse(response_data, status=200)
                    
                elif dashboard_type == 'Channel':
                    with connection.cursor() as cursor:
                        cursor.execute(
                            "SELECT DISTINCT FormattedDate FROM SnapshotByChannelView;"
                        )
                        filters = cursor.fetchall()
                        result_array = [{"value": item[0], "label": item[0]} for item in filters]
                        result_array = sorted(result_array, key=custom_sort)

                    with connection.cursor() as cursor:
                        cursor.execute(
                            "SELECT DISTINCT Product FROM SnapshotByChannelView;"
                        )
                        filter_item = cursor.fetchall()
                        result_item_array = [{"value": item[0], "label": item[0]} for item in filter_item]    
                        result_item_array = sorted(result_item_array, key=custom_sort_item)
                        response_data = {
                            "success": "true",
                            "filters": result_array,
                            "item_filter": result_item_array,
                            "message": "Filter value fetched successfully"
                        }
                        return JsonResponse(response_data, status=200)
                    
                elif dashboard_type =='Variation':
                    with connection.cursor() as cursor:
                        cursor.execute(
                            "SELECT DISTINCT FormattedDate FROM SnapshotByVariation;"
                        )
                        filters = cursor.fetchall()
                        result_array = [{"value": item[0], "label": item[0]} for item in filters]
                        result_array = sorted(result_array, key=custom_sort)
                    
                    with connection.cursor() as cursor:
                        cursor.execute(
                            "SELECT DISTINCT Product FROM SnapshotByVariation;"
                        )
                        filter_item = cursor.fetchall()
                        result_item_array = [{"value": item[0], "label": item[0]} for item in filter_item]    
                        result_item_array = sorted(result_item_array, key=custom_sort_item)
                        response_data = {
                            "success": "true",
                            "filters": result_array,
                            "item_filter": result_item_array,
                            "message": "Filter value fetched successfully"
                        }
                        return JsonResponse(response_data, status=200)

        except Exception as err:
            return JsonResponse({'success': False, 'message': str(err)}, status=500)
        

# @method_decorator(csrf_exempt, name='dispatch')
# class TimescaleChannel(View):
#     def get(self, request, *args, **kwargs):
#         try:
#             with connection.cursor() as cursor:
#                 cursor.execute(
#                     "SELECT DISTINCT FormattedDate FROM SnapshotByChannelView;"
#                 )
#                 filters = cursor.fetchall()
#                 result_array = [{"value": item[0], "label": item[0]} for item in filters]

#                 response_data = {
#                     "success": "true",
#                     "filters": result_array,
#                     "message": "Filter value fetched successfully"
#                 }
#                 return JsonResponse(response_data, status=200)
#         except Exception as err:
#             return JsonResponse({'success': False, 'message': str(err)}, status=500)

# @method_decorator(csrf_exempt, name='dispatch')
# class TimescaleVariation(View):
#     def get(self, request, *args, **kwargs):
#         try:
#             with connection.cursor() as cursor:
#                 cursor.execute(
#                     "SELECT DISTINCT FormattedDate FROM SnapshotByVariation;"
#                 )
#                 filters = cursor.fetchall()
#                 result_array = [{"value": item[0], "label": item[0]} for item in filters]

#                 response_data = {
#                     "success": "true",
#                     "filters": result_array,
#                     "message": "Filter value fetched successfully"
#                 }
#                 return JsonResponse(response_data, status=200)
#         except Exception as err:
#             return JsonResponse({'success': False, 'message': str(err)}, status=500)

@method_decorator(csrf_exempt, name='dispatch')
class OrganizationDropdown(View):
    def get(self, request, *args, **kwargs):
        try:
            with connection.cursor() as cursor_competitive:
                cursor_competitive.execute(
                    "SELECT DISTINCT BrandName FROM Brands b"
                )
                filters = cursor_competitive.fetchall()
                result_array_competitive_set = [{"value": item[0], "label": item[0]} for item in filters]
                response_data = {
                    "success": "true",
                    "Brand": result_array_competitive_set
                }
                return JsonResponse(response_data, status=200)
        
        except Exception as err:
            return JsonResponse({'success': False, 'message': str(err)}, status=500)                


@method_decorator(csrf_exempt, name='dispatch')
class UserOrganizationDropdown(View):
    def get(self, request, *args, **kwargs):
        try:
            with connection.cursor() as cursor_competitive:
                cursor_competitive.execute(
                    "select DISTINCT Organization from MetaOrganization mo "
                )
                filters = cursor_competitive.fetchall()
                result_array_competitive_set = [{"value": item[0], "label": item[0]} for item in filters]
                response_data = {
                    "success": "true",
                    "Brand": result_array_competitive_set
                }
                return JsonResponse(response_data, status=200)
        
        except Exception as err:
            return JsonResponse({'success': False, 'message': str(err)}, status=500)                



@method_decorator(csrf_exempt, name='dispatch')
class CommonFilter(View):
    def post(self, request, *args, **kwargs):
        data = json.loads(request.body)
        email = data.get('email', '')
        try:
            with connection.cursor() as cursor:
    

                cursor.execute(f'''
                    SELECT mo.Chains
                        FROM MetaOrganization mo
                        JOIN user_management um ON mo.Organization = um.Organization 
                        WHERE um.Email = '{email}'
                    ''',
                    )

                user_data = cursor.fetchall()
                user_data_list = user_data[0][0].split(',')
                result_array = [{"value": item, "label": item} for item in user_data_list]
                query = f'''
                    select DISTINCT Segment from Brands b where BrandName  in {tuple(user_data_list)}
                    '''
                
                cursor.execute(query)

                segment_data = cursor.fetchall()
                result = [item[0] for item in segment_data]
                segment_result_array = [{"value": str(item), "label": str(item)} for item in result]


            # with connection.cursor() as cursor_competitive:
            #     cursor_competitive.execute(
            #         "SELECT DISTINCT BrandName FROM Brands b"
            #     )
            #     filters = cursor_competitive.fetchall()
            #     result_array_competitive_set = [{"value": item[0], "label": item[0]} for item in filters]

            # with connection.cursor() as cursor_segment:
            #     cursor_segment.execute(
            #         "SELECT DISTINCT Segments FROM MetaSegmentCodes msc ;"
            #     )
            #     filters = cursor_segment.fetchall()
            #     result_array_segment = [{"value": item[0], "label": item[0]} for item in filters]

            with connection.cursor() as cursor_category:
                cursor_category.execute(
                    "SELECT DISTINCT Category FROM MetaProductCategory mpc;"
                )
                filters = cursor_category.fetchall()
                result_array_category = [{"value": item[0], "label": item[0]} for item in filters]

            with connection.cursor() as cursor_protein:
                cursor_protein.execute(
                    "SELECT DISTINCT ProteinType FROM MetaProtienType mpt"
                )
                filters = cursor_protein.fetchall()
                result_array_protein = [{"value": item[0], "label": item[0]} for item in filters]

            with connection.cursor() as cursor_channel:
                cursor_channel.execute(
                    "SELECT DISTINCT ChannelName FROM MetaPriceChannel mpc "
                )
                filters = cursor_channel.fetchall()
                result_array_channel = [{"value": item[0], "label": item[0]} for item in filters]

            with connection.cursor() as cursor_city:
                cursor_city.execute(
                    "SELECT DISTINCT City FROM MetaCityCodes mcc"
                )
                filters = cursor_city.fetchall()
                result_array_city = [{"value": item[0], "label": item[0]} for item in filters]

                cursor_city.execute(''' SELECT Size
                    FROM MetaSize ms 
                    ORDER BY SizeId  
                    OFFSET 14 ROWS FETCH NEXT 44 ROWS ONLY;
                                ''')
                size = cursor_city.fetchall()
                size = [{"value": item[0], "label": item[0]} for item in size]

                response_data = {
                    "success": "true",
                    "competitive_set": result_array,
                    "segments" : segment_result_array ,
                    "category" : result_array_category,
                    "protein_type": result_array_protein,
                    "channel" : result_array_channel,
                    "city":result_array_city,
                    "size":size
                }
                return JsonResponse(response_data, status=200)
        
        except Exception as err:
            return JsonResponse({'success': False, 'message': str(err)}, status=500)
        


@method_decorator(csrf_exempt, name='dispatch')
class Brand_SegmentFilterAPI(View):
    def get(self, request, *args, **kwargs):
        try:
            data = json.loads(request.body)
            email = data.get('email', '')
            initial_load = data.get("initial_load",'')
            segments = data.get("segments",'')

            if initial_load == 1:
                with connection.cursor() as cursor:
        

                    cursor.execute(f'''
                        SELECT mo.Chains
                            FROM MetaOrganization mo
                            JOIN user_management um ON mo.Organization = um.Organization 
                            WHERE um.Email = '{email}'
                        ''',
                        )

                    user_data = cursor.fetchall()
                    user_data_list = user_data[0][0].split(',')
                    result_array = [{"value": item, "label": item} for item in user_data_list]
                    query = f'''
                        select DISTINCT Segment from Brands b where BrandName  in {tuple(user_data_list)}
                        '''
                    
                    cursor.execute(query)

                    segment_data = cursor.fetchall()
                    result = [item[0] for item in segment_data]
                    segment_result_array = [{"value": str(item), "label": str(item)} for item in result]
                    
        
                    response_data = {
                        "success": True,
                        "brand": result_array,
                        "segment":segment_result_array
                    }

                    return JsonResponse(response_data, status=200)
            else:
                with connection.cursor() as cursor:    
                    cursor.execute(f'''select DISTINCT BrandName from Brands b where Segment  in {tuple(segments)}''')
                    seg_user_data = cursor.fetchall()
                    seg_user_data_list = [item[0] for item in seg_user_data] 
                    cursor.execute(f'''
                        SELECT mo.Chains
                            FROM MetaOrganization mo
                            JOIN user_management um ON mo.Organization = um.Organization 
                            WHERE um.Email = '{email}'
                        ''',
                        )
                    user_data = cursor.fetchall()
                    user_data_list = user_data[0][0].split(',')
                    common_elements = [elem for elem in seg_user_data_list if elem in user_data_list]
                response_data = {
                        "success": True,
                        "brand": common_elements
                    }

                return JsonResponse(response_data, status=200)

        except Exception as e:
            

            return JsonResponse({'success': False, 'message': str(e)}, status=500)     


