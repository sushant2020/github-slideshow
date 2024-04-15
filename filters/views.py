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
    if item['label'][0].isdigit():
    
        return (0, item['label'])
    else:
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
            email = data.get("email")

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
                        cursor.execute(f'''
                        SELECT mo.Chains
                            FROM MetaOrganization mo
                            JOIN user_management um ON mo.Organization = um.Organization 
                            WHERE um.Email = '{email}'
                        ''',
                        )

                        user_data = cursor.fetchall()
                        user_data_list = user_data[0][0].split(',')
                        cursor.execute(
                            f"SELECT DISTINCT Product FROM SnapshotByRegionView where Brand in {tuple(user_data_list)};"
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
                        cursor.execute(f'''
                        SELECT mo.Chains
                            FROM MetaOrganization mo
                            JOIN user_management um ON mo.Organization = um.Organization 
                            WHERE um.Email = '{email}'
                        ''',
                        )
                        user_data = cursor.fetchall()
                        user_data_list = user_data[0][0].split(',')
                        cursor.execute(
                            f"SELECT DISTINCT Product FROM SnapshotByChannelView where Brand in {tuple(user_data_list)};"
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
                        cursor.execute(f'''
                        SELECT mo.Chains
                            FROM MetaOrganization mo
                            JOIN user_management um ON mo.Organization = um.Organization 
                            WHERE um.Email = '{email}'
                        ''',
                        )

                        user_data = cursor.fetchall()
                        user_data_list = user_data[0][0].split(',')
                        cursor.execute(
                            f"SELECT DISTINCT Product FROM SnapshotByVariation where Brand in {tuple(user_data_list)};"
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
                        cursor.execute(f'''
                        SELECT mo.Chains
                            FROM MetaOrganization mo
                            JOIN user_management um ON mo.Organization = um.Organization 
                            WHERE um.Email = '{email}'
                        ''',
                        )

                        user_data = cursor.fetchall()
                        user_data_list = user_data[0][0].split(',')
                        cursor.execute(
                            f"SELECT DISTINCT Product FROM SnapshotByRegionView where Brand in {tuple(user_data_list)};"
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
                        cursor.execute(f'''
                        SELECT mo.Chains
                            FROM MetaOrganization mo
                            JOIN user_management um ON mo.Organization = um.Organization 
                            WHERE um.Email = '{email}'
                        ''',
                        )

                        user_data = cursor.fetchall()
                        user_data_list = user_data[0][0].split(',')
                        cursor.execute(
                            f"SELECT DISTINCT Product FROM SnapshotByChannelView where Brand in {tuple(user_data_list)};"
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
                        cursor.execute(f'''
                        SELECT mo.Chains
                            FROM MetaOrganization mo
                            JOIN user_management um ON mo.Organization = um.Organization 
                            WHERE um.Email = '{email}'
                        ''',
                        )

                        user_data = cursor.fetchall()
                        user_data_list = user_data[0][0].split(',')
                        cursor.execute(
                            f"SELECT DISTINCT Product FROM SnapshotByVariation where Brand in {tuple(user_data_list)};"
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
        
@method_decorator(csrf_exempt, name='dispatch')
class InitialTimescalefilter(View):
    def post(self, request, *args, **kwargs):
        try:
            data =json.loads(request.body)
            dashboard_type = data.get('dashboard_type')
            if dashboard_type == "Region":
                query = '''SELECT 
                                CONCAT(LEFT(FORMAT(MAX(CONVERT(DATE, '01-' + FormattedDate, 106)), 'MMM-yyyy'), 3), '-', RIGHT(FORMAT(MAX(CONVERT(DATE, '01-' + FormattedDate, 106)), 'yy'), 2)) AS LatestDate
                            FROM 
                                SnapshotByRegionView;'''
            elif dashboard_type == "Variation":
                query = '''SELECT 
                                CONCAT(LEFT(FORMAT(MAX(CONVERT(DATE, '01-' + FormattedDate, 106)), 'MMM-yyyy'), 3), '-', RIGHT(FORMAT(MAX(CONVERT(DATE, '01-' + FormattedDate, 106)), 'yy'), 2)) AS LatestDate
                            FROM 
                                SnapshotByVariation;'''
            else:
                query = '''SELECT 
                                CONCAT(LEFT(FORMAT(MAX(CONVERT(DATE, '01-' + FormattedDate, 106)), 'MMM-yyyy'), 3), '-', RIGHT(FORMAT(MAX(CONVERT(DATE, '01-' + FormattedDate, 106)), 'yy'), 2)) AS LatestDate
                            FROM 
                                SnapshotByChannelView;'''
            
            with connection.cursor() as cursor:
                cursor.execute(query)
                date = cursor.fetchone()[0]
                date_array =  [{"value": date, "label": date}]
                response_data = {
                    "success": "true",
                    "date": date_array
                }
                return JsonResponse(response_data, status=200)
        except Exception as e:
            return JsonResponse({'success': False, 'message': str(e)}, status=500)


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
        filters = data.get("filters",{})
        try:

            with connection.cursor() as cursor_protein:
                cursor_protein.execute(
                    "SELECT DISTINCT ProteinType FROM MetaProtienType mpt"
                )
                filters_protein = cursor_protein.fetchall()
                result_array_protein = [{"value": item[0], "label": item[0]} for item in filters_protein]

            with connection.cursor() as cursor_channel:
                cursor_channel.execute(
                    "SELECT DISTINCT ChannelName FROM MetaPriceChannel mpc "
                )
                filters_Channel = cursor_channel.fetchall()
                result_array_channel = [{"value": item[0], "label": item[0]} for item in filters_Channel]

            with connection.cursor() as cursor_city:
                cursor_city.execute(
                    "SELECT DISTINCT City FROM MetaCityCodes mcc"
                )
                filters_city = cursor_city.fetchall()
                result_array_city = [{"value": item[0], "label": item[0]} for item in filters_city]

                cursor_city.execute(''' SELECT Size
                    FROM MetaSize ms 
                    ORDER BY SizeId  
                    OFFSET 14 ROWS FETCH NEXT 44 ROWS ONLY;
                                ''')
                size = cursor_city.fetchall()
                size = [{"value": item[0], "label": item[0]} for item in size]

            if filters["Market_Segment"] ==[] and filters["Category"] ==[] and filters["Competitive_Set"] ==[]:
                with connection.cursor() as cursor:
                    cursor.execute(f'''
                    SELECT mo.Chains
                        FROM MetaOrganization mo
                        JOIN user_management um ON mo.Organization = um.Organization 
                        WHERE um.Email = '{email}'
                    '''
                    )

                    user_data = cursor.fetchall()
                    user_data_list = user_data[0][0].split(',')
                    result_array = [{"value": item, "label": item} for item in user_data_list]
                    query = f'''
                        select DISTINCT Segment from dynamicFilterDetailed b where BrandName  in {tuple(user_data_list)}
                        '''
                
                    cursor.execute(query)
                    
                    segment_data = cursor.fetchall()
                    result = [item[0] for item in segment_data]
                    segment_result_array = [{"value": str(item), "label": str(item)} for item in result]
                    

                    result_values = ', '.join(f"'{item['value']}'" for item in result_array)
                    segment_values = ', '.join(f"'{item['value']}'" for item in segment_result_array)

                    query = f'''
                        select Distinct Category from dynamicFilterDetailed 
                        where BrandName in ({result_values}) 
                        and Segment in ({segment_values})
                    '''

                    cursor.execute(query)
                    default_category = cursor.fetchall()
                    category_result = [item[0] for item in default_category]
                    category_array = [{"value": str(item), "label": str(item)} for item in category_result]
                
                    cursor.execute(f'''select Distinct Item from dynamicFilterDetailed
                                where BrandName in ({result_values})
                                ''')
                    items = cursor.fetchall()
                    item_list = [item[0] for item in items]
                    items_array = [{"value": str(item), "label": str(item)} for item in item_list]
                    
                    
                    cursor.execute(f'''
                    SELECT mo.Chains
                        FROM MetaOrganization mo
                        JOIN user_management um ON mo.Organization = um.Organization 
                        WHERE um.Email = '{email}'
                    ''',
                    )
                    user_data = cursor.fetchall()
                    user_chain_list = user_data[0][0].split(',')

                    # main_brand = []
                    # for i in all_brand_result:
                    #     if i in user_chain_list:
                    #         if i not in main_brand:
                    #             main_brand.append(i)

                    brand_result_array = [{"value": str(item), "label": str(item)} for item in user_chain_list]
                    brand_values = ', '.join(f"'{item['value']}'" for item in brand_result_array)

                    cursor.execute(f'''
                    select Distinct  Segment from dynamicFilterDetailed
                    where BrandName in ({brand_values})
                        ''')
                    default_segment = cursor.fetchall()
                    segment_result = [item[0] for item in default_segment]
                    segment_array = [{"value": str(item), "label": str(item)} for item in segment_result]
                    
                    
                    response_data = {
                    "success": True,
                    "brand": result_array,
                    "segment":segment_array,
                    "category":category_array,
                    "item":items_array,
                    "protein_type": result_array_protein,
                    "channel" : result_array_channel,
                    "city":result_array_city,
                    "size":size}


                return JsonResponse(response_data, status=200)                
            
        
            elif filters["Market_Segment"] ==[] and filters["Competitive_Set"] !=[] and filters["Category"] ==[]:
                with connection.cursor() as cursor:
                    # cursor.execute(f'''
                    # SELECT mo.Chains
                    #     FROM MetaOrganization mo
                    #     JOIN user_management um ON mo.Organization = um.Organization 
                    #     WHERE um.Email = '{email}'
                    # ''',
                    # )

                    # user_data = cursor.fetchall()
                    # user_data_list = user_data[0][0].split(',')
                    result_array = [{"value": item, "label": item} for item in filters["Competitive_Set"]]
                    

                    if len(filters["Competitive_Set"]) ==1:
                        query = f'''
                            select DISTINCT Segment from dynamicFilterDetailed b where BrandName  = '{filters["Competitive_Set"][0]}'

                            '''
                    else:    
                    
                        query = f'''
                            select DISTINCT Segment from dynamicFilterDetailed b where BrandName  in {tuple(filters["Competitive_Set"])}
                            '''
                    
                    cursor.execute(query)
                    
                    segment_data = cursor.fetchall()
                    result = [item[0] for item in segment_data]
                    segment_result_array = [{"value": str(item), "label": str(item)} for item in result]
                    

                    result_values = ', '.join(f"'{item['value']}'" for item in result_array)
                    segment_values = ', '.join(f"'{item['value']}'" for item in segment_result_array)

                    query = f'''
                        select DISTINCT Category from dynamicFilterDetailed 
                        where BrandName in ({result_values}) 
                        and Segment in ({segment_values})
                    '''
               
                    cursor.execute(query)
                    default_category = cursor.fetchall()
                    category_result = [item[0] for item in default_category]
                    category_array = [{"value": str(item), "label": str(item)} for item in category_result]
                    
                    cursor.execute(f'''select Distinct Item from dynamicFilterDetailed
                                where BrandName in ({result_values})
                                ''')
                    items = cursor.fetchall()
                    item_list = [item[0] for item in items]
                    items_array = [{"value": str(item), "label": str(item)} for item in item_list]
                    
                    
                    
                    cursor.execute(f'''
                    SELECT mo.Chains
                        FROM MetaOrganization mo
                        JOIN user_management um ON mo.Organization = um.Organization 
                        WHERE um.Email = '{email}'
                    ''',
                    )
                    user_data = cursor.fetchall()
                    user_chain_list = user_data[0][0].split(',')

                    # main_brand = []
                    # for i in all_brand_result:
                    #     if i in user_chain_list:
                    #         if i not in main_brand:
                    #             main_brand.append(i)

                    brand_result_array = [{"value": str(item), "label": str(item)} for item in user_chain_list]
                    brand_values = ', '.join(f"'{item['value']}'" for item in brand_result_array)

                    cursor.execute(f'''
                    select Distinct  Segment from dynamicFilterDetailed
                    where BrandName in ({brand_values})
                        ''')
                    default_segment = cursor.fetchall()
                    segment_result = [item[0] for item in default_segment]
                    segment_array = [{"value": str(item), "label": str(item)} for item in segment_result]
                    
                    
                    response_data = {
                    "success": True,
                    "brand": brand_result_array,
                    "segment":segment_array,
                    "category":category_array,
                    "item":items_array,
                    "protein_type": result_array_protein,
                    "channel" : result_array_channel,
                    "city":result_array_city,
                    "size":size}

                    return JsonResponse(response_data, status=200)

            elif filters["Market_Segment"] !=[] and filters["Category"] ==[] and filters["Competitive_Set"] ==[]:

                with connection.cursor() as cursor:   
                    if len(filters["Market_Segment"])==1:
                        cursor.execute(f'''select DISTINCT BrandName from dynamicFilterDetailed b where Segment = '{filters["Market_Segment"][0]}'
                                    ''')
                    else:     
                        cursor.execute(f'''select DISTINCT BrandName from dynamicFilterDetailed b where Segment in {tuple(filters["Market_Segment"])}''')
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
                    result_array = [{"value": item, "label": item} for item in common_elements]
                    segment_result_array = [{"value": str(item), "label": str(item)} for item in filters["Market_Segment"]]
                    result_values = ', '.join(f"'{item['value']}'" for item in result_array)
                    segment_values = ', '.join(f"'{item['value']}'" for item in segment_result_array)
                    
                    query = f'''
                        select Distinct Category from dynamicFilterDetailed 
                        where BrandName in ({result_values}) 
                        and Segment in ({segment_values})
                    '''

                    cursor.execute(query)
                    default_category = cursor.fetchall()
                    category_result = [item[0] for item in default_category]
                    category_array = [{"value": str(item), "label": str(item)} for item in category_result]
                    
                    cursor.execute(f'''select Distinct Item from dynamicFilterDetailed
                                where BrandName in ({result_values})
                                and Segment in ({segment_values})''')
                    items = cursor.fetchall()
                    item_list = [item[0] for item in items]
                    items_array = [{"value": str(item), "label": str(item)} for item in item_list]
                    
                    
                    cursor.execute(f'''
                    SELECT mo.Chains
                        FROM MetaOrganization mo
                        JOIN user_management um ON mo.Organization = um.Organization 
                        WHERE um.Email = '{email}'
                    ''',
                    )
                    user_data = cursor.fetchall()
                    user_chain_list = user_data[0][0].split(',')

                    # main_brand = []
                    # for i in all_brand_result:
                    #     if i in user_chain_list:
                    #         if i not in main_brand:
                    #             main_brand.append(i)

                    brand_result_array = [{"value": str(item), "label": str(item)} for item in user_chain_list]
                    brand_values = ', '.join(f"'{item['value']}'" for item in brand_result_array)

                    cursor.execute(f'''
                    select Distinct  Segment from dynamicFilterDetailed
                    where BrandName in ({brand_values})
                        ''')
                    default_segment = cursor.fetchall()
                    segment_result = [item[0] for item in default_segment]
                    segment_array = [{"value": str(item), "label": str(item)} for item in segment_result]
                    
                    
                    response_data = {
                    "success": True,
                    "brand": result_array,
                    "segment":segment_array,
                    "category":category_array,
                    "item":items_array,
                    "protein_type": result_array_protein,
                    "channel" : result_array_channel,
                    "city":result_array_city,
                    "size":size}

                return JsonResponse(response_data, status=200)

            elif filters["Market_Segment"] !=[] and filters["Competitive_Set"] !=[] and filters["Category"] !=[]:
                competitive_set_array = [{"value": item, "label": item} for item in filters["Competitive_Set"]]
                segments_array = [{"value": item, "label": item} for item in filters["Market_Segment"]]
                category_array = [{"value": item, "label": item} for item in filters["Category"]]
                competitive_set_values = ', '.join(f"'{item['value']}'" for item in competitive_set_array)
                segment_values = ', '.join(f"'{item['value']}'" for item in segments_array)
                category_value = ', '.join(f"'{item['value']}'" for item in category_array)
                with connection.cursor() as cursor:

                    cursor.execute(f''' select Distinct BrandName from dynamicFilterDetailed where Segment in ({segment_values})''')
                    user_brands = cursor.fetchall()
                    
                    
                    user_brands_list = [item[0] for item in user_brands]
                    
                    user_brand_array = [{"value": item, "label": item} for item in user_brands_list]
                    cursor.execute(f'''select Distinct Item from dynamicFilterDetailed
                                    where BrandName in ({competitive_set_values})
                                    and Segment in ({segment_values})
                                    and Category in ({category_value})
                                        ''')
                    items = cursor.fetchall()
                    item_list = [item[0] for item in items]
                    items_array = [{"value": str(item), "label": str(item)} for item in item_list]
                    
                    cursor.execute(f'''
                    SELECT mo.Chains
                        FROM MetaOrganization mo
                        JOIN user_management um ON mo.Organization = um.Organization 
                        WHERE um.Email = '{email}'
                    ''',
                    )
                    user_data = cursor.fetchall()
                    user_chain_list = user_data[0][0].split(',')

                    main_brand = []
                    for i in user_brands_list:
                        if i in user_chain_list:
                            if i not in main_brand:
                                main_brand.append(i)

                    brand_result_array = [{"value": str(item), "label": str(item)} for item in main_brand]
                    seg_brand_array = [{"value": str(item), "label": str(item)} for item in user_chain_list]
                    brand_values = ', '.join(f"'{item['value']}'" for item in seg_brand_array)

                    cursor.execute(f'''
                    select Distinct  Segment from dynamicFilterDetailed
                    where BrandName in ({brand_values})
                        ''')
                    default_segment = cursor.fetchall()
                    segment_result = [item[0] for item in default_segment]
                    segment_array = [{"value": str(item), "label": str(item)} for item in segment_result]
                    response_data = {
                            "success": True,
                            "brand": brand_result_array,
                            "segment":segment_array,
                            "category":category_array,
                            "item":items_array,
                            "protein_type": result_array_protein,
                            "channel" : result_array_channel,
                            "city":result_array_city,
                            "size":size}
                    return JsonResponse(response_data, status=200)
            elif filters["Market_Segment"] ==[] and filters["Competitive_Set"]!=[] and filters["Category"]!=[]:
                with connection.cursor() as cursor:
                    result_array = [{"value": item, "label": item} for item in filters["Competitive_Set"]]
                    category_result_array = [{"value": str(item), "label": str(item)} for item in filters["Category"]]
                    result_values = ', '.join(f"'{item['value']}'" for item in result_array)
                    category_values = ', '.join(f"'{item['value']}'" for item in category_result_array)
                    query = f'''
                        select Distinct Segment from dynamicFilterDetailed
                        where BrandName in ({result_values}) 
                        and Category in ({category_values})
                    '''
                    cursor.execute(query)
                    default_segment = cursor.fetchall()
                    segment_result = [item[0] for item in default_segment]
                    segment_array = [{"value": str(item), "label": str(item)} for item in segment_result]
                    cursor.execute(f'''select Distinct Item from dynamicFilterDetailed
                                where BrandName in ({result_values})
                                and Category in ({category_values})''')
                    items = cursor.fetchall()
                    item_list = [item[0] for item in items]
                    items_array = [{"value": str(item), "label": str(item)} for item in item_list]
                    
                    
                    cursor.execute(f'''
                    SELECT mo.Chains
                        FROM MetaOrganization mo
                        JOIN user_management um ON mo.Organization = um.Organization 
                        WHERE um.Email = '{email}'
                    ''',
                    )
                    user_data = cursor.fetchall()
                    user_chain_list = user_data[0][0].split(',')

                    # main_brand = []
                    # for i in all_brand_result:
                    #     if i in user_chain_list:
                    #         if i not in main_brand:
                    #             main_brand.append(i)

                    brand_result_array = [{"value": str(item), "label": str(item)} for item in user_chain_list]
                    brand_values = ', '.join(f"'{item['value']}'" for item in brand_result_array)

                    cursor.execute(f'''
                    select Distinct  Segment from dynamicFilterDetailed
                    where BrandName in ({brand_values})
                        ''')
                    default_segment = cursor.fetchall()
                    segment_result = [item[0] for item in default_segment]
                    segments_array = [{"value": str(item), "label": str(item)} for item in segment_result]
                    cursor.execute(f'''select Distinct Category from dynamicFilterDetailed
                                where BrandName in ({result_values})
                                ''')
                    categories = cursor.fetchall()
                    categories_list = [item[0] for item in categories]
                    categories_array = [{"value": str(item), "label": str(item)} for item in categories_list]
                    response_data = {
                    "success": True,
                    "brand": brand_result_array,
                    "segment":segments_array,
                    "category":categories_array,
                    "item":items_array,
                    "protein_type": result_array_protein,
                    "channel" : result_array_channel,
                    "city":result_array_city,
                    "size":size}
                return JsonResponse(response_data, status=200)
    
            elif filters["Market_Segment"] !=[] and filters["Competitive_Set"]==[] and filters["Category"]!=[]:
                with connection.cursor() as cursor:
                    result_array = [{"value": item, "label": item} for item in filters["Market_Segment"]]
                    category_result_array = [{"value": str(item), "label": str(item)} for item in filters["Category"]]
                    result_values = ', '.join(f"'{item['value']}'" for item in result_array)
                    category_values = ', '.join(f"'{item['value']}'" for item in category_result_array)
                    query = f'''
                        select Distinct  BrandName from dynamicFilterDetailed
                        where Segment in ({result_values}) 
                        and Category in ({category_values})
                    '''
                    cursor.execute(query)
                    default_brand = cursor.fetchall()
                    brand_result = [item[0] for item in default_brand]
                    cursor.execute(f'''
                        SELECT mo.Chains
                            FROM MetaOrganization mo
                            JOIN user_management um ON mo.Organization = um.Organization 
                            WHERE um.Email = '{email}'
                        ''',
                        )

                    user_data = cursor.fetchall()
                    user_data_brand = user_data[0][0].split(',')
                    common_elements = [elem for elem in brand_result if elem in user_data_brand]
                    
                    brand_array = [{"value": str(item), "label": str(item)} for item in common_elements]
                    brand_values = ', '.join(f"'{item['value']}'" for item in brand_array)
                    cursor.execute(f'''select Distinct Item from dynamicFilterDetailed
                                where Segment in ({result_values}) 
                                and Category in ({category_values})
                                and BrandName in ({brand_values})
                                ''')
                    #add condition to fetch items with brand also
                    items = cursor.fetchall()
                    item_list = [item[0] for item in items]
                    items_array = [{"value": str(item), "label": str(item)} for item in item_list]
                    
                    
                    cursor.execute(f'''
                    SELECT mo.Chains
                        FROM MetaOrganization mo
                        JOIN user_management um ON mo.Organization = um.Organization 
                        WHERE um.Email = '{email}'
                    ''',
                    )
                    user_data = cursor.fetchall()
                    user_chain_list = user_data[0][0].split(',')

                    # main_brand = []
                    # for i in all_brand_result:
                    #     if i in user_chain_list:
                    #         if i not in main_brand:
                    #             main_brand.append(i)

                    brand_result_array = [{"value": str(item), "label": str(item)} for item in user_chain_list]
                    brand_values = ', '.join(f"'{item['value']}'" for item in brand_result_array)

                    cursor.execute(f'''
                    select Distinct  Segment from dynamicFilterDetailed
                    where BrandName in ({brand_values})
                        ''')
                    default_segment = cursor.fetchall()
                    segment_result = [item[0] for item in default_segment]
                    segment_array = [{"value": str(item), "label": str(item)} for item in segment_result]
                    
                    
                    response_data = {
                        "success": True,
                        "brand": brand_array,
                        "segment":segment_array,
                        "category":category_result_array,
                        "item":items_array,
                        "protein_type": result_array_protein,
                        "channel" : result_array_channel,
                        "city":result_array_city,
                        "size":size
                                }
                    return JsonResponse(response_data, status=200)
            elif filters["Market_Segment"] !=[] and filters["Competitive_Set"]!=[] and filters["Category"]==[]:
                with connection.cursor() as cursor:
                    result_array = [{"value": item, "label": item} for item in filters["Market_Segment"]]
                    competitive_set_result_array = [{"value": str(item), "label": str(item)} for item in filters["Competitive_Set"]]
                    result_values = ', '.join(f"'{item['value']}'" for item in result_array)
                    competitive_set_values = ', '.join(f"'{item['value']}'" for item in competitive_set_result_array)
                    query = f'''
                        select Distinct Category from dynamicFilterDetailed
                        where Segment in ({result_values}) 
                        and BrandName in ({competitive_set_values})
                    '''
                    cursor.execute(query)
                    default_category = cursor.fetchall()
                    category_result = [item[0] for item in default_category]
                    category_array = [{"value": str(item), "label": str(item)} for item in category_result]
                    cursor.execute(f'''select Distinct Item from dynamicFilterDetailed
                                where Segment in ({result_values}) 
                                and BrandName in ({competitive_set_values})
                                ''')
                    items = cursor.fetchall()
                    item_list = [item[0] for item in items]
                    items_array = [{"value": str(item), "label": str(item)} for item in item_list]
                    query = f'''
                        select Distinct  BrandName from dynamicFilterDetailed
                        where Segment in ({result_values}) 
                    '''
                    cursor.execute(query)
                    default_brand = cursor.fetchall()
                    brand_result = [item[0] for item in default_brand]
                    cursor.execute(f'''
                        SELECT mo.Chains
                            FROM MetaOrganization mo
                            JOIN user_management um ON mo.Organization = um.Organization 
                            WHERE um.Email = '{email}'
                        ''',
                        )

                    user_data = cursor.fetchall()
                    user_data_brand = user_data[0][0].split(',')
                    common_elements = [elem for elem in brand_result if elem in user_data_brand]
                    brand_array = [{"value": str(item), "label": str(item)} for item in common_elements]
                    


                    cursor.execute(f'''
                    SELECT mo.Chains
                        FROM MetaOrganization mo
                        JOIN user_management um ON mo.Organization = um.Organization 
                        WHERE um.Email = '{email}'
                    ''',
                    )
                    user_data = cursor.fetchall()
                    user_chain_list = user_data[0][0].split(',')

                    # main_brand = []
                    # for i in all_brand_result:
                    #     if i in user_chain_list:
                    #         if i not in main_brand:
                    #             main_brand.append(i)

                    brand_result_array = [{"value": str(item), "label": str(item)} for item in user_chain_list]
                    brand_values = ', '.join(f"'{item['value']}'" for item in brand_result_array)

                    cursor.execute(f'''
                    select Distinct  Segment from dynamicFilterDetailed
                    where BrandName in ({brand_values})
                        ''')
                    default_segment = cursor.fetchall()
                    segment_result = [item[0] for item in default_segment]
                    segment_array = [{"value": str(item), "label": str(item)} for item in segment_result]


                    response_data = {
                        "success": True,
                        "brand": brand_array,
                        "segment":segment_array,
                        "category":category_array,
                        "item":items_array,
                        "protein_type": result_array_protein,
                        "channel" : result_array_channel,
                        "city":result_array_city,
                        "size":size
                    }
                    return JsonResponse(response_data, status=200)
            
            elif filters["Market_Segment"] ==[] and filters["Competitive_Set"] ==[] and filters["Category"] !=[]:
                with connection.cursor() as cursor:
                    category_result_array = [{"value": str(item), "label": str(item)} for item in filters["Category"]]
                    category_values = ', '.join(f"'{item['value']}'" for item in category_result_array)
                    
                    if len(filters["Category"])==1:
                        category = filters["Category"][0]
                        query = f"select BrandName from dynamicFilterDetailed where Category = '{category}'"
                        cursor.execute(query)
                    else:
                        cursor.execute(f"select BrandName from dynamicFilterDetailed where Category in {tuple(category_values)}")
                    all_brands = cursor.fetchall()
                    all_brand_result = [item[0] for item in all_brands]

                    cursor.execute(f'''
                    SELECT mo.Chains
                        FROM MetaOrganization mo
                        JOIN user_management um ON mo.Organization = um.Organization 
                        WHERE um.Email = '{email}'
                    ''',
                    )
                    user_data = cursor.fetchall()
                    user_chain_list = user_data[0][0].split(',')

                    main_brand = []
                    for i in all_brand_result:
                        if i in user_chain_list:
                            if i not in main_brand:
                                main_brand.append(i)

                    brand_result_array = [{"value": str(item), "label": str(item)} for item in main_brand]
                    brand_values = ', '.join(f"'{item['value']}'" for item in brand_result_array)

                    cursor.execute(f'''
                    select Distinct  Segment from dynamicFilterDetailed
                    where BrandName in ({brand_values}) 
                    and Category in ({category_values})
                        ''')
                    default_segment = cursor.fetchall()
                    segment_result = [item[0] for item in default_segment]
                    segment_array = [{"value": str(item), "label": str(item)} for item in segment_result]
                    cursor.execute(f'''select Distinct Item from dynamicFilterDetailed
                                    where BrandName in ({brand_values}) 
                                    and Category in ({category_values})
                                    ''')
                    items = cursor.fetchall()
                    item_list = [item[0] for item in items]
                    items_array = [{"value": str(item), "label": str(item)} for item in item_list]
                
                
                    cursor.execute(f'''
                    SELECT mo.Chains
                        FROM MetaOrganization mo
                        JOIN user_management um ON mo.Organization = um.Organization 
                        WHERE um.Email = '{email}'
                    ''',
                    )
                    user_data = cursor.fetchall()
                    user_chain_list = user_data[0][0].split(',')

                    # main_brand = []
                    # for i in all_brand_result:
                    #     if i in user_chain_list:
                    #         if i not in main_brand:
                    #             main_brand.append(i)

                    brand_result_array = [{"value": str(item), "label": str(item)} for item in user_chain_list]
                    brand_values = ', '.join(f"'{item['value']}'" for item in brand_result_array)

                    cursor.execute(f'''
                    select Distinct  Segment from dynamicFilterDetailed
                    where BrandName in ({brand_values})
                        ''')
                    default_segment = cursor.fetchall()
                    segment_result = [item[0] for item in default_segment]
                    segments_array = [{"value": str(item), "label": str(item)} for item in segment_result]
                
                
                response_data = {
                "success": True,
                "brand": brand_result_array,
                "segment":segments_array,
                "category":category_result_array,
                "item":items_array,
                "protein_type": result_array_protein,
                "channel" : result_array_channel,
                "city":result_array_city,
                "size":size
                }
                return JsonResponse(response_data, status=200)
        
        except Exception as err:
            return JsonResponse({'success': False, 'message': str(err)}, status=500)


@method_decorator(csrf_exempt, name='dispatch')
class Competitive_SetAPI(View):
 def post(self, request, *args, **kwargs):
        try:
            data = json.loads(request.body)
            email = data.get('email', '')
            segments = data.get("segments",'')
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
                    
                    if len(segments)==1:
                        cursor.execute(f'''select DISTINCT BrandName from dynamicFilterDetailed b where Segment = '{segments[0]}'
                                       ''')
                    elif segments ==[]:
                        response_data = {
                            "success": True,
                            "brand": result_array, 
                            }
                        return JsonResponse(response_data, status=200)
                        
                    else:     
                        cursor.execute(f'''select DISTINCT BrandName from dynamicFilterDetailed b where Segment in {tuple(segments)}''')
                    seg_user_data = cursor.fetchall()
                    seg_user_data_list = [item[0] for item in seg_user_data] 

                    main_result = []

                    for i in seg_user_data_list:
                        if i in user_data_list and i not in main_result:
                                main_result.append(i)
                    main_result_array = [{"value": item, "label": item} for item in main_result]
                    response_data = {
                    "success": True,
                    "brand": main_result_array, 
                    }
            return JsonResponse(response_data, status=200) 
        except Exception as e:
            

            return JsonResponse({'success': False, 'message': str(e)}, status=500)     

@method_decorator(csrf_exempt, name='dispatch')
class Brand_SegmentFilterAPI(View):
    def post(self, request, *args, **kwargs):
        try:
            data = json.loads(request.body)
            email = data.get('email', '')
            initial_load = data.get("initial_load",'')
            segments = data.get("segments",'')
            competitive_set = data.get("competitive_set",'')
            category = data.get("category",'')
            
            
            if segments ==[] and category ==[] and competitive_set ==[]:
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
                        select DISTINCT Segment from dynamicFilterDetailed b where BrandName  in {tuple(user_data_list)}
                        '''
                   
                    cursor.execute(query)
                    
                    segment_data = cursor.fetchall()
                    result = [item[0] for item in segment_data]
                    segment_result_array = [{"value": str(item), "label": str(item)} for item in result]
                    

                    result_values = ', '.join(f"'{item['value']}'" for item in result_array)
                    segment_values = ', '.join(f"'{item['value']}'" for item in segment_result_array)

                    query = f'''
                        select Distinct Category from dynamicFilterDetailed 
                        where BrandName in ({result_values}) 
                        and Segment in ({segment_values})
                    '''

                    cursor.execute(query)
                    default_category = cursor.fetchall()
                    category_result = [item[0] for item in default_category]
                    category_array = [{"value": str(item), "label": str(item)} for item in category_result]
                
                    cursor.execute(f'''select Distinct Item from dynamicFilterDetailed
                                   where BrandName in ({result_values})
                                   ''')
                    items = cursor.fetchall()
                    item_list = [item[0] for item in items]
                    items_array = [{"value": str(item), "label": str(item)} for item in item_list]
                    response_data = {
                    "success": True,
                    "brand": result_array,
                    "segment":segment_result_array,
                    "category":category_array,
                    "item":items_array}


                return JsonResponse(response_data, status=200)                
            
            
            elif segments ==[] and competitive_set !=[] and category ==[]:
                with connection.cursor() as cursor:
                    # cursor.execute(f'''
                    # SELECT mo.Chains
                    #     FROM MetaOrganization mo
                    #     JOIN user_management um ON mo.Organization = um.Organization 
                    #     WHERE um.Email = '{email}'
                    # ''',
                    # )

                    # user_data = cursor.fetchall()
                    # user_data_list = user_data[0][0].split(',')
                    result_array = [{"value": item, "label": item} for item in competitive_set]
                    

                    if len(competitive_set) ==1:
                        query = f'''
                            select DISTINCT Segment from dynamicFilterDetailed b where BrandName  = '{competitive_set[0]}'

                            '''
                    else:    
                    
                        query = f'''
                            select DISTINCT Segment from dynamicFilterDetailed b where BrandName  in {tuple(competitive_set)}
                            '''
                    cursor.execute(query)
                    
                    segment_data = cursor.fetchall()
                    result = [item[0] for item in segment_data]
                    segment_result_array = [{"value": str(item), "label": str(item)} for item in result]
                    

                    result_values = ', '.join(f"'{item['value']}'" for item in result_array)
                    segment_values = ', '.join(f"'{item['value']}'" for item in segment_result_array)

                    query = f'''
                        select DISTINCT Category from dynamicFilterDetailed 
                        where BrandName in ({result_values}) 
                        and Segment in ({segment_values})
                    '''

                    cursor.execute(query)
                    default_category = cursor.fetchall()
                    category_result = [item[0] for item in default_category]
                    category_array = [{"value": str(item), "label": str(item)} for item in category_result]
                    
                    cursor.execute(f'''select Distinct Item from dynamicFilterDetailed
                                   where BrandName in ({result_values})
                                   ''')
                    items = cursor.fetchall()
                    item_list = [item[0] for item in items]
                    items_array = [{"value": str(item), "label": str(item)} for item in item_list]
                    response_data = {
                    "success": True,
                    "brand": result_array,
                    "segment":segment_result_array,
                    "category":category_array,
                    "item":items_array}

                    return JsonResponse(response_data, status=200)

            elif segments !=[] and category ==[] and competitive_set ==[]:

                with connection.cursor() as cursor:   
                    if len(segments)==1:
                        cursor.execute(f'''select DISTINCT BrandName from dynamicFilterDetailed b where Segment = '{segments[0]}'
                                       ''')
                    else:     
                        cursor.execute(f'''select DISTINCT BrandName from dynamicFilterDetailed b where Segment in {tuple(segments)}''')
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
                    result_array = [{"value": item, "label": item} for item in common_elements]
                    segment_result_array = [{"value": str(item), "label": str(item)} for item in segments]
                    result_values = ', '.join(f"'{item['value']}'" for item in result_array)
                    segment_values = ', '.join(f"'{item['value']}'" for item in segment_result_array)
                    
                    query = f'''
                        select Distinct Category from dynamicFilterDetailed 
                        where BrandName in ({result_values}) 
                        and Segment in ({segment_values})
                    '''

                    cursor.execute(query)
                    default_category = cursor.fetchall()
                    category_result = [item[0] for item in default_category]
                    category_array = [{"value": str(item), "label": str(item)} for item in category_result]
                    
                    cursor.execute(f'''select Distinct Item from dynamicFilterDetailed
                                   where BrandName in ({result_values})
                                   and Segment in ({segment_values})''')
                    items = cursor.fetchall()
                    item_list = [item[0] for item in items]
                    items_array = [{"value": str(item), "label": str(item)} for item in item_list]
                    response_data = {
                    "success": True,
                    "brand": result_array,
                    "segment":segment_result_array,
                    "category":category_array,
                    "item":items_array}

                return JsonResponse(response_data, status=200)

            elif segments !=[] and competitive_set !=[] and category !=[]:
                competitive_set_array = [{"value": item, "label": item} for item in competitive_set]
                segments_array = [{"value": item, "label": item} for item in segments]
                category_array = [{"value": item, "label": item} for item in category]
                competitive_set_values = ', '.join(f"'{item['value']}'" for item in competitive_set_array)
                segment_values = ', '.join(f"'{item['value']}'" for item in segments_array)
                category_value = ', '.join(f"'{item['value']}'" for item in category_array)
                with connection.cursor() as cursor:
                    cursor.execute(f'''select Distinct Item from dynamicFilterDetailed
                                    where BrandName in ({competitive_set_values})
                                    and Segment in ({segment_values})
                                    and Category in ({category_value})
                                        ''')
                    items = cursor.fetchall()
                    item_list = [item[0] for item in items]
                    items_array = [{"value": str(item), "label": str(item)} for item in item_list]
                    response_data = {
                            "success": True,
                            "brand": competitive_set_array,
                            "segment":segments_array,
                            "category":category_array,
                            "item":items_array
                        }
                    return JsonResponse(response_data, status=200)
            elif segments ==[] and competitive_set!=[] and category!=[]:
                with connection.cursor() as cursor:
                    result_array = [{"value": item, "label": item} for item in competitive_set]
                    category_result_array = [{"value": str(item), "label": str(item)} for item in category]
                    result_values = ', '.join(f"'{item['value']}'" for item in result_array)
                    category_values = ', '.join(f"'{item['value']}'" for item in category_result_array)
                    query = f'''
                        select Distinct Segment from dynamicFilterDetailed
                        where BrandName in ({result_values}) 
                        and Category in ({category_values})
                    '''
                    cursor.execute(query)
                    default_segment = cursor.fetchall()
                    segment_result = [item[0] for item in default_segment]
                    segment_array = [{"value": str(item), "label": str(item)} for item in segment_result]
                    cursor.execute(f'''select Distinct Item from dynamicFilterDetailed
                                   where BrandName in ({result_values})
                                   and Category in ({category_values})''')
                    items = cursor.fetchall()
                    item_list = [item[0] for item in items]
                    items_array = [{"value": str(item), "label": str(item)} for item in item_list]
                    response_data = {
                    "success": True,
                    "brand": result_array,
                    "segment":segment_array,
                    "category":category_result_array,
                    "item":items_array}
                return JsonResponse(response_data, status=200)
    
            elif segments !=[] and competitive_set==[] and category!=[]:
                with connection.cursor() as cursor:
                    result_array = [{"value": item, "label": item} for item in segments]
                    category_result_array = [{"value": str(item), "label": str(item)} for item in category]
                    result_values = ', '.join(f"'{item['value']}'" for item in result_array)
                    category_values = ', '.join(f"'{item['value']}'" for item in category_result_array)
                    query = f'''
                        select Distinct  BrandName from dynamicFilterDetailed
                        where Segment in ({result_values}) 
                        and Category in ({category_values})
                    '''
                    cursor.execute(query)
                    default_brand = cursor.fetchall()
                    brand_result = [item[0] for item in default_brand]
                    cursor.execute(f'''
                        SELECT mo.Chains
                            FROM MetaOrganization mo
                            JOIN user_management um ON mo.Organization = um.Organization 
                            WHERE um.Email = '{email}'
                        ''',
                        )

                    user_data = cursor.fetchall()
                    user_data_brand = user_data[0][0].split(',')
                    common_elements = [elem for elem in brand_result if elem in user_data_brand]
                    brand_array = [{"value": str(item), "label": str(item)} for item in common_elements]
                    cursor.execute(f'''select Distinct Item from dynamicFilterDetailed
                                   where Segment in ({result_values}) 
                                   and Category in ({category_values})
                                   ''')
                    items = cursor.fetchall()
                    item_list = [item[0] for item in items]
                    items_array = [{"value": str(item), "label": str(item)} for item in item_list]
                    response_data = {
                        "success": True,
                        "brand": brand_array,
                        "segment":result_array,
                        "category":category_result_array,
                        "item":items_array
                    }
                    return JsonResponse(response_data, status=200)
            elif segments !=[] and competitive_set!=[] and category==[]:
                with connection.cursor() as cursor:
                    result_array = [{"value": item, "label": item} for item in segments]
                    competitive_set_result_array = [{"value": str(item), "label": str(item)} for item in competitive_set]
                    result_values = ', '.join(f"'{item['value']}'" for item in result_array)
                    competitive_set_values = ', '.join(f"'{item['value']}'" for item in competitive_set_result_array)
                    query = f'''
                        select Distinct Category from dynamicFilterDetailed
                        where Segment in ({result_values}) 
                        and BrandName in ({competitive_set_values})
                    '''
                    cursor.execute(query)
                    default_category = cursor.fetchall()
                    category_result = [item[0] for item in default_category]
                    category_array = [{"value": str(item), "label": str(item)} for item in category_result]
                    cursor.execute(f'''select Distinct Item from dynamicFilterDetailed
                                   where Segment in ({result_values}) 
                                   and BrandName in ({competitive_set_values})
                                   ''')
                    items = cursor.fetchall()
                    item_list = [item[0] for item in items]
                    items_array = [{"value": str(item), "label": str(item)} for item in item_list]
                    response_data = {
                        "success": True,
                        "brand": competitive_set_result_array,
                        "segment":result_array,
                        "category":category_array,
                        "item":items_array
                    }
                    return JsonResponse(response_data, status=200)
            
            elif segments ==[] and competitive_set ==[] and category !=[]:
                with connection.cursor() as cursor:
                    category_result_array = [{"value": str(item), "label": str(item)} for item in category]
                    category_values = ', '.join(f"'{item['value']}'" for item in category_result_array)
                    
                    if len(category)==1:
                        query = f"select BrandName from dynamicFilterDetailed where Category = '{category[0]}'"
                        cursor.execute(query)
                    else:
                        cursor.execute(f"select BrandName from dynamicFilterDetailed where Category in {tuple(category_values)}")
                    all_brands = cursor.fetchall()
                    all_brand_result = [item[0] for item in all_brands]

                    cursor.execute(f'''
                    SELECT mo.Chains
                        FROM MetaOrganization mo
                        JOIN user_management um ON mo.Organization = um.Organization 
                        WHERE um.Email = '{email}'
                    ''',
                    )
                    user_data = cursor.fetchall()
                    user_chain_list = user_data[0][0].split(',')

                    main_brand = []
                    for i in all_brand_result:
                        if i in user_chain_list:
                            if i not in main_brand:
                                main_brand.append(i)

                    brand_result_array = [{"value": str(item), "label": str(item)} for item in main_brand]
                    brand_values = ', '.join(f"'{item['value']}'" for item in brand_result_array)

                    cursor.execute(f'''
                    select Distinct  Segment from dynamicFilterDetailed
                    where BrandName in ({brand_values}) 
                    and Category in ({category_values})
                        ''')
                    default_segment = cursor.fetchall()
                    segment_result = [item[0] for item in default_segment]
                    segment_array = [{"value": str(item), "label": str(item)} for item in segment_result]
                    cursor.execute(f'''select Distinct Item from dynamicFilterDetailed
                                   where BrandName in ({brand_values}) 
                                   and Category in ({category_values})
                                   ''')
                    items = cursor.fetchall()
                    item_list = [item[0] for item in items]
                    items_array = [{"value": str(item), "label": str(item)} for item in item_list]
                    response_data = {
                    "success": True,
                    "brand": brand_result_array,
                    "segment":segment_array,
                    "category":category_result_array,
                    "item":items_array
                    }

                return JsonResponse(response_data, status=200)
        except Exception as e:
            

            return JsonResponse({'success': False, 'message': str(e)}, status=500)     

@method_decorator(csrf_exempt, name='dispatch')
class ItemFilterAPI(View):
    def post(self, request, *args, **kwargs):
        try:
            data = json.loads(request.body)
            email = data.get('email', '')
            initial_load = data.get("initial_load",'')
            segments = data.get("segments",'')
            competitive_set = data.get("competitive_set",'')
            category = data.get("category",'')
            with connection.cursor() as cursor:
                if category !=[]:
                    if len(segments)!=1 and len(competitive_set) !=1:
                        query = f''' 
                            select Distinct Item from MVProduct where Category in {str(tuple(category)).replace(',','')} and Segments in {tuple(segments)} and Chain in {tuple(competitive_set)}
                            '''
                    elif len(segments) !=1 and len(competitive_set) ==1:
                        query = f''' 
                            select Distinct Item from MVProduct where Category in {str(tuple(category)).replace(',','')} and Segments in {tuple(segments)} and Chain in {str(tuple(competitive_set)).replace(',','')}
                            '''
                    elif len(segments)==1 and len(competitive_set)!=1:
                        query = f''' 
                            select Distinct Item from MVProduct where Category in {str(tuple(category)).replace(',','')} and Segments in {str(tuple(segments)).replace(',','')} and Chain in {tuple(competitive_set)}
                            '''
                    elif len(segments)==1 and len(competitive_set)==1:
                        query = f''' 
                            select Distinct Item from MVProduct where Category in {str(tuple(category)).replace(',','')} and Segments in {str(tuple(segments)).replace(',','')} and Chain in {str(tuple(competitive_set)).replace(',','')}
                            '''

                    cursor.execute(query)
                    items = cursor.fetchall()
                    result = [item[0] for item in items]
                    item_result_array = [{"value": str(item), "label": str(item)} for item in result]
                    response_data = {
                            "success": True,
                            "item": item_result_array,
                        }
                    return JsonResponse(response_data, status=200)
                elif category ==[]:
                    if len(segments)!=1 and len(competitive_set) !=1:
                        query = f''' 
                            select Distinct Item from MVProduct where Segments in {tuple(segments)} and Chain in {tuple(competitive_set)}
                            '''
                    elif len(segments) !=1 and len(competitive_set) ==1:
                        query = f''' 
                            select Distinct Item from MVProduct where Segments in {tuple(segments)} and Chain in {str(tuple(competitive_set)).replace(',','')}
                            '''
                    elif len(segments)==1 and len(competitive_set)!=1:
                        query = f''' 
                            select Distinct Item from MVProduct where Segments in {str(tuple(segments)).replace(',','')} and Chain in {tuple(competitive_set)}
                            '''
                    elif len(segments)==1 and len(competitive_set)==1:
                        query = f''' 
                            select Distinct Item from MVProduct where Segments in {str(tuple(segments)).replace(',','')} and Chain in {str(tuple(competitive_set)).replace(',','')}
                            '''
                        

                    cursor.execute(query)
                    items = cursor.fetchall()
                    result = [item[0] for item in items]
                    item_result_array = [{"value": str(item), "label": str(item)} for item in result]
                    response_data = {
                            "success": True,
                            "item": item_result_array,
                        }
                    return JsonResponse(response_data, status=200)
        except Exception as e:
            return JsonResponse({'success': False, 'message': str(e)}, status=500)     