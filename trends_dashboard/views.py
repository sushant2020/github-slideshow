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


def extract_year_month(date_str):
    # Function to extract the year and month from a date string
    return datetime.strptime(date_str, '%Y-%m-%d').strftime('%b-%y')

def extract_month(date_str):
    return datetime.strptime(date_str, '%b-%y')

@method_decorator(csrf_exempt, name='dispatch')
class Trends_API(View):
    def post(self, request, *args, **kwargs):
        try:
            data = json.loads(request.body)
            page_number = data.get('page_number', 1)
            filters = data.get('filters', {})
            sort_column = data.get('sort_column')
            sort_type = data.get('sort_type')
            table_type = data.get('table_type')
            dashboard_type = data.get('dashboard_type')
            email = data.get("email","")
            
            if table_type == 1:
                if dashboard_type ==1:

                    records_per_page = 100

                    offset = (page_number - 1) * records_per_page

                    where_conditions = []
                    params = []

                    filter_mappings = {
                        "TimescalesTrend": "Period",
                        "Market_Segment": "Segments",
                        "Competitive_Set": "Brand",
                        "Category": "Category",
                        "Protein_Type": "ProteinType",
                        "Channel": "ChannelName",
                        "City" : "City",
                        "Product_size":"Size",
                        "Item":"Product",
                        "Price_range":"PriceSegment",
                        "Brand":"Brand",
                        "Product":"Product"
                    }
                    
                    for filter_name, filter_values in filters.items():
                        if filter_values:
                            column_name = filter_mappings.get(filter_name)
                            if column_name:
                                
                                    #pdb.set_trace()
                                if filter_name == "TimescalesTrend":
                                    # Parse month-year strings to datetime objects
                                    start_date = datetime.strptime(filters["TimescalesTrend"][0].strip(), "%Y-%m-%d")
                                    end_date = datetime.strptime(filters["TimescalesTrend"][1].strip(), "%Y-%m-%d")
                                    # Add condition to check if FormattedDate is between start_date and end_date
                                    where_conditions.append(f"(CONVERT(datetime, {column_name}, 5) >= %s AND CONVERT(datetime, {column_name}, 5) <= %s)")
                                    params.extend([start_date, end_date])
                                else:
                                    if filter_name == "Protein_Type" and filter_values == ["All"]:
                                        #pass
                                        where_conditions.append(f"{column_name} IN ('NotSet','Chicken','Beef','Plant-Based','Pork','Fish','Turkey','Sea Food','Duck','All','Lamb')")
                                        
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
                            # elif filter_name in filter_mappings:
                            #     column_name = filter_mappings[filter_name]
                            #     where_conditions.append(f"{column_name} IS NOT NULL")
                    
                    where_clause = ''
                    if where_conditions:
                        where_clause = 'WHERE ' + ' AND '.join(where_conditions)


                    order_by_clause = ''
                    if sort_column and sort_type:
                        order_by_clause = f"ORDER BY {filter_mappings[sort_column]} {sort_type}"

                   
                    with connection.cursor() as cursor:
                        query = f'''
                            SELECT Product, Brand, Category, Prices, FormattedDate, PriceSegment, Size, ProteinType
                            FROM PriceDetailed 
                            {where_clause}
                            {order_by_clause}'''

                        # Format query with params embedded
                        
                        
                        
                        # f'''
                        #     SELECT Product, Brand, Category, Prices, FormattedDate,PriceSegment,Size,ProteinType
                        #     FROM PriceDetailed 
                        #     {where_clause}
                        #     {order_by_clause}''',
                        #     params

                        cursor.execute(query,params)

                        user_data = cursor.fetchall()
                        # Restructure data
                        grouped_data = defaultdict(lambda: defaultdict(dict))
                        all_months = set()  # Set to store all months encountered
                        for row in user_data:
                            product = row[0]
                            brand = row[1]
                            category = row[2]
                            PriceSegment = row[5]
                            Size = row[6]
                            price = row[3]
                            ProteinType = row[7]
                            formatted_date = row[4]
                            grouped_data[(product, brand, category,PriceSegment,Size,ProteinType)][formatted_date] = price
                            # Extract month from the formatted_date and add it to the set
                            month = formatted_date[:6]  # Extract month-year string
                            all_months.add(month)

                        # Convert set of months to a sorted list  
                        sorted_months = sorted(all_months, key=extract_month)
                        sorted_months.insert(0,"Item")
                        sorted_months.insert(1,"Brand")
                        sorted_months.insert(2,"Category")
                        
                        total_count = len(grouped_data)
                        start_index = (page_number - 1) * records_per_page
                        end_index = min(start_index + records_per_page, total_count)
                        limited_grouped_data = dict(list(grouped_data.items())[start_index:end_index])

                        result = []
                        for (product, brand, category,Size,ProteinType,PriceSegment), prices in limited_grouped_data.items():
                            item = {
                                "Item": product,
                                "Brand": brand,
                                "Category": category,
                                **prices
                            }
                            result.append(item)
                        
                        response_data = {
                            "success": True,
                            "data": result,
                            "total_count": total_count,
                            "months": sorted_months  # Include the sorted list of months in the response
                        }

                        return JsonResponse(response_data, status=200)
                elif dashboard_type ==2:
                    records_per_page = 100
                
                    offset = (page_number - 1) * records_per_page

                    where_conditions = []
                    params = []

                    filter_mappings = {
                        "TimescalesTrend": "Period",
                        "Market_Segment": "Segments",
                        "Competitive_Set": "Brand",
                        "Category": "Category",
                        "Protein_Type": "ProteinType",
                        "Channel": "ChannelName",
                        "City": "City",
                        "Product_size": "Size",
                        "Item": "Product",
                        "Price_range":"PriceSegment",
                        "Brand":"Brand",
                        "Product":"Product"
                    }

                    for filter_name, filter_values in filters.items():
                        if filter_values:
                            column_name = filter_mappings.get(filter_name)
                            if column_name:
                                
                                if filter_name == "TimescalesTrend":
                                    if len(filter_values) == 2:
                                        # Handle range of dates
                                        from_date = datetime.strptime(filter_values[0].strip(), "%Y-%m-%d")
                                        to_date = datetime.strptime(filter_values[1].strip(), "%Y-%m-%d")
                                        from_date = (from_date - relativedelta(months=1)).strftime("%Y-%m-%d")  # Adjust to previous month
                                        where_conditions.append(f"((CONVERT(datetime, {column_name}, 5) >= %s AND CONVERT(datetime, {column_name}, 5) <= %s))")
                                        params.extend([from_date, to_date])
                                else:
                                    if filter_name == "Protein_Type" and filter_values == ["All"]:
                                    #pass
                                     
                                        where_conditions.append(f"{column_name} IN ('NotSet','Chicken','Beef','Plant-Based','Pork','Fish','Turkey','Sea Food','Duck','All','Lamb')")
                                    
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
                            SELECT Product, Brand, Category, Prices, FormattedDate, PriceSegment,Size,ProteinType
                            FROM PriceDetailed 
                            {where_clause}
                            {order_by_clause}''',
                            params)

                        user_data = cursor.fetchall()
                        # Restructure data
                        grouped_data = defaultdict(lambda: defaultdict(dict))
                        all_months = set()  # Set to store all months encountered
                        for row in user_data:
                            product = row[0]
                            brand = row[1]
                            category = row[2]
                            price = row[3]
                            PriceSegment = row[5]
                            Size = row[6]
                            ProteinType = row[7]
                            formatted_date = row[4]
                            grouped_data[(product, brand, category,PriceSegment,Size,ProteinType)][formatted_date] = price
                            # Extract month from the formatted_date and add it to the set
                            month = formatted_date[:6]  # Extract month-year string
                            all_months.add(month)

                        # Convert set of months to a sorted list
                        sorted_months = sorted(all_months, key=extract_month)
                        total_count = len(grouped_data)
                        start_index = (page_number - 1) * records_per_page
                        end_index = min(start_index + records_per_page, total_count)
                        limited_grouped_data = dict(list(grouped_data.items())[start_index:end_index])
                        
                        result = []
                        for (product, brand, category,Size,ProteinType,PriceSegment), prices in limited_grouped_data.items():
                            item = {
                                "Item": product,
                                "Brand": brand,
                                "Category": category,
                                
                            }
                            # Calculate variations
                            prev_month_key = None
                            prev_month_price = None
                            for month in sorted_months:
                                if month in prices:
                                    if prev_month_key is not None:
                                        variation_key = f"{month}"
                                        var = round(((prices[month] / prev_month_price) - 1) * 100,2)
                                        var_formated = f"{var:.1f}"
                                        item[variation_key] = str(var_formated)+' '+'%'
                                    prev_month_key = month
                                    prev_month_price = prices[month]
                                    if item not in result:    
                                        result.append(item)
                                else:
                                    pass
                                    # # If data for a month is missing, set variation to None
                                    # variation_key = f"variation_{prev_month_key}-{month}"
                                    # item[variation_key] = None
                            
                        sorted_months.insert(0,"Item")
                        sorted_months.insert(1,"Brand")
                        sorted_months.insert(2,"Category")
                        sorted_months.insert(3,"Price_range")
                        response_data = {
                            "success": True,
                            "data": result,
                            "total_count": total_count,
                            "months": sorted_months  # Include the sorted list of months in the response
                        }

                        return JsonResponse(response_data, status=200)
                else:
                    records_per_page = 100

                    offset = (page_number - 1) * records_per_page

                    where_conditions = []
                    params = []

                    filter_mappings = {
                        "TimescalesTrend": "Period",
                        "Market_Segment": "Segments",
                        "Competitive_Set": "Brand",
                        "Category": "Category",
                        "Protein_Type": "ProteinType",
                        "Channel": "ChannelName",
                        "City": "City",
                        "Product_size": "Size",
                        "Item": "Product",
                        "Price_range": "PriceSegment",
                        "Brand": "Brand",
                        "Product":"Product"
                    }

                    for filter_name, filter_values in filters.items():
                        if filter_values:
                            column_name = filter_mappings.get(filter_name)
                            if column_name:
                        
                                    if filter_name == "TimescalesTrend":
                                        if len(filter_values) == 2:
                                            from_date = datetime.strptime(filter_values[0].strip(), "%Y-%m-%d")
                                            to_date = datetime.strptime(filter_values[1].strip(), "%Y-%m-%d")
                                            prev_year_from_date = (from_date - relativedelta(years=1)).strftime("%Y-%m-%d")
                                            prev_year_to_date = (to_date - relativedelta(years=1)).strftime("%Y-%m-%d")
                                            where_conditions.append(f"(({column_name} >= %s AND {column_name} <= %s) OR ({column_name} >= %s AND {column_name} <= %s))")
                                            params.extend([from_date, to_date, prev_year_from_date, prev_year_to_date])
                                        # Handle date range
                                    elif filter_name == "City" or filter_name == "Channel":
                                        where_conditions.append(f"{column_name} IN ({', '.join(['%s' for _ in range(len(filter_values))])})")
                                        params.extend(filter_values)
                                    else:
                                        if filter_name == "Protein_Type" and filter_values == ["All"]:
                                        #pass
                                            where_conditions.append(f"{column_name} IN ('NotSet','Chicken','Beef','Plant-Based','Pork','Fish','Turkey','Sea Food','Duck','All','Lamb')")
                                            
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

                    where_clause = 'WHERE ' + ' AND '.join(where_conditions) if where_conditions else ''
                    order_by_clause = f"ORDER BY {filter_mappings[sort_column]} {sort_type}" if sort_column and sort_type else ''
                    #pdb.set_trace()
                    with connection.cursor() as cursor:
                        cursor.execute(f'''
                            SELECT Product, Brand, Category, Prices, FormattedDate, PriceSegment,Size,ProteinType
                            FROM PriceDetailed 
                            {where_clause}
                            {order_by_clause}''',
                            params)

                        user_data = cursor.fetchall()

                        grouped_data = defaultdict(lambda: defaultdict(dict))
                        all_months = set()  # Set to store all months encountered
    
                        for row in user_data:
                            product = row[0]
                            brand = row[1]
                            category = row[2]
                            price = row[3]
                            PriceSegment = row[5]
                            Size = row[6]
                            formatted_date = row[4]
                            ProteinType = row[7]
                            grouped_data[(product, brand, category, PriceSegment,Size,ProteinType)][formatted_date] = price
                            month = formatted_date[:6]  # Extract month-year string
                            all_months.add(month)

                        
                        
                        sorted_months = sorted(all_months, key=extract_month)
                        
                        total_count = len(grouped_data)
                        start_index = (page_number - 1) * records_per_page
                        end_index = min(start_index + records_per_page, total_count)
                        limited_grouped_data = dict(list(grouped_data.items())[start_index:end_index])
                        result = []

                        for (product, brand, category,Size,ProteinType,PriceSegment), prices in limited_grouped_data.items():
                            item = {
                                "Item": product,
                                "Brand": brand,
                                "Category": category,
                            }

                            prev_year_prices = defaultdict(float)
                            for month in sorted_months:
                                if month in prices:
                                    prev_year_month = (datetime.strptime(month, "%b-%y") - relativedelta(years=1)).strftime("%b-%y")
                                    if prev_year_month in prev_year_prices:
                                        variation_key = f"{month}"
                                        var = round(((prices[month] / prev_year_prices[prev_year_month]) - 1) * 100, 2)
                                        var_formated = f"{var:.1f}"
                                        item[variation_key] = str(var_formated)+' '+'%'
                                    prev_year_prices[month] = prices[month]
                                    if item not in result:
                                        result.append(item)

                        
                        from_date = datetime.strptime(filters["TimescalesTrend"][0].strip(), "%Y-%m-%d")
                        to_date = datetime.strptime(filters["TimescalesTrend"][1].strip(), "%Y-%m-%d")

                        # Generating a list of months between the start and end dates
                        months_between_dates = [from_date.strftime("%b-%y")]
                        while from_date < to_date:
                            from_date += relativedelta(months=1)
                            months_between_dates.append(from_date.strftime("%b-%y"))

                        # Sorting the list of months and filtering out those not within the timescale trend
                        filter_sorted_months = sorted(set(months_between_dates).intersection(all_months), key=extract_month)
                        filter_sorted_months.insert(0, "Item")
                        filter_sorted_months.insert(1, "Brand")
                        filter_sorted_months.insert(2, "Category")

                        response_data = {
                            "success": True,
                            "data": result,
                            "total_count": total_count,
                            "months": filter_sorted_months  # Include the sorted list of months in the response
                        }

                        return JsonResponse(response_data, status=200)       

            else:
                if dashboard_type==1:
                    records_per_page = 100

                    offset = (page_number - 1) * records_per_page

                    where_conditions = []
                    params = []

                    filter_mappings = {
                        "TimescalesTrend": "Period",
                        "Market_Segment": "Segments",
                        "Competitive_Set": "BrandName",
                        "Category": "Category",
                        "Protein_Type": "ProteinType",
                        "Channel": "ChannelName",
                        "City" : "City",
                        "Brand":"BrandName"
                    }

                    for filter_name, filter_values in filters.items():
                        if filter_values:
                            column_name = filter_mappings.get(filter_name)
                            if column_name:
                               
                                    if filter_name == "TimescalesTrend":
                                        # Parse month-year strings to datetime objects
                                        start_date = datetime.strptime(filters["TimescalesTrend"][0].strip(), "%Y-%m-%d")
                                        end_date = datetime.strptime(filters["TimescalesTrend"][1].strip(), "%Y-%m-%d")
                                        # Add condition to check if FormattedDate is between start_date and end_date
                                        where_conditions.append(f"(CONVERT(datetime, {column_name}, 5) >= %s AND CONVERT(datetime, {column_name}, 5) <= %s)")
                                        params.extend([start_date, end_date])
                                    else:
                                        if filter_name == "Protein_Type" and filter_values == ["All"]:
                                        #pass
                                            where_conditions.append(f"{column_name} IN ('NotSet','Chicken','Beef','Plant-Based','Pork','Fish','Turkey','Sea Food','Duck','All','Lamb')")
                                            
                                        else:
                                            where_conditions.append(f"{column_name} IN ({', '.join(['%s' for _ in range(len(filter_values))])})")
                                            params.extend(filter_values)
                        else:
                            column_name = filter_mappings.get(filter_name)
                            if filters["Competitive_Set"]==[] and column_name =="BrandName":
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
                            SELECT BrandName, Category, ProteinType, Price, FormattedDate
                            FROM PricesCategoryDetailed 
                            {where_clause}
                            {order_by_clause} ''',
                            params)

                        user_data = cursor.fetchall()
                        # Restructure data
                        grouped_data = defaultdict(lambda: defaultdict(dict))
                        all_months = set()  # Set to store all months encountered
                        for row in user_data:
                            brand = row[0]
                            category = row[1]
                            ProteinType = row[2] 
                            price = row[3]
                            formatted_date = row[4]
                            grouped_data[(brand, category,ProteinType)][formatted_date] = round(price,2)
                            # Extract month from the formatted_date and add it to the set
                            month = formatted_date[:6]  # Extract month-year string
                            all_months.add(month)

                        # Convert set of months to a sorted list  
                        sorted_months = sorted(all_months, key=extract_month)
                        sorted_months.insert(0,"Brand")
                        sorted_months.insert(1,"Category")
                        sorted_months.insert(2,"Protein_Type")
                        total_count = len(grouped_data)
                        start_index = (page_number - 1) * records_per_page
                        end_index = min(start_index + records_per_page, total_count)
                        limited_grouped_data = dict(list(grouped_data.items())[start_index:end_index])

                        result = []
                        for (brand, category,ProteinType), prices in limited_grouped_data.items():
                            item = {
                                "Brand": brand,
                                "Category": category,
                                "Protein_Type":ProteinType,
                                **prices
                            }
                            result.append(item)

                        response_data = {
                            "success": True,
                            "data": result,
                            "total_count": total_count,
                            "months": sorted_months  # Include the sorted list of months in the response
                        }

                        return JsonResponse(response_data, status=200)

                elif dashboard_type==2:
                    records_per_page = 100

                    offset = (page_number - 1) * records_per_page

                    where_conditions = []
                    params = []

                    filter_mappings = {
                        "TimescalesTrend": "Period",
                        "Market_Segment": "Segments",
                        "Competitive_Set": "BrandName",
                        "Category": "Category",
                        "Protein_Type": "ProteinType",
                        "Channel": "ChannelName",
                        "City" : "City",
                        "Brand":"BrandName"
                    }

                    for filter_name, filter_values in filters.items():
                        if filter_values:
                            column_name = filter_mappings.get(filter_name)
                            if column_name:
                                if filter_name == "TimescalesTrend":
                                    if len(filter_values) == 2:
                                        # Handle range of dates
                                        from_date = datetime.strptime(filter_values[0].strip(), "%Y-%m-%d")
                                        to_date = datetime.strptime(filter_values[1].strip(), "%Y-%m-%d")
                                        from_date = (from_date - relativedelta(months=1)).strftime("%Y-%m-%d")  # Adjust to previous month
                                        where_conditions.append(f"((CONVERT(datetime, {column_name}, 5) >= %s AND CONVERT(datetime, {column_name}, 5) <= %s))")
                                        params.extend([from_date, to_date])
                                else:
                                    if filter_name == "Protein_Type" and filter_values == ["All"]:
                                        #pass
                                        where_conditions.append(f"{column_name} IN ('NotSet','Chicken','Beef','Plant-Based','Pork','Fish','Turkey','Sea Food','Duck','All','Lamb')")
                                        
                                    else:
                                        if filter_name == "Protein_Type" and filter_values == ["All"]:
                                        #pass
                                            where_conditions.append(f"{column_name} IN ('NotSet','Chicken','Beef','Plant-Based','Pork','Fish','Turkey','Sea Food','Duck','All','Lamb')")
                                            
                                        else:
                                            where_conditions.append(f"{column_name} IN ({', '.join(['%s' for _ in range(len(filter_values))])})")
                                            params.extend(filter_values)
                        else:
                            column_name = filter_mappings.get(filter_name)
                            if filters["Competitive_Set"]==[] and column_name =="BrandName":
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
                            SELECT BrandName, Category, ProteinType, Price, FormattedDate
                            FROM PricesCategoryDetailed 
                            {where_clause}
                            {order_by_clause}''',
                            params)

                        user_data = cursor.fetchall()
                        # Restructure data
                        grouped_data = defaultdict(lambda: defaultdict(dict))
                        all_months = set()  # Set to store all months encountered
                        for row in user_data:
                            brand = row[0]
                            category = row[1]
                            ProteinType = row[2] 
                            price = row[3]
                            formatted_date = row[4]
                            grouped_data[(brand, category,ProteinType)][formatted_date] = price
                            # Extract month from the formatted_date and add it to the set
                            month = formatted_date[:6]  # Extract month-year string
                            all_months.add(month)

                        # Convert set of months to a sorted list
                        sorted_months = sorted(all_months, key=extract_month)
                        total_count = len(grouped_data)
                        start_index = (page_number - 1) * records_per_page
                        end_index = min(start_index + records_per_page, total_count)
                        limited_grouped_data = dict(list(grouped_data.items())[start_index:end_index])

                        result = []
                        for (brand, category,ProteinType), prices in grouped_data.items():
                            item = {
                                "Brand": brand,
                                "Category": category,
                                "Protein_Type":ProteinType,
                            }
                            # Calculate variations
                            prev_month_key = None
                            prev_month_price = None
                            for month in sorted_months:
                                if month in prices:
                                    if prev_month_key is not None:
                                        variation_key = f"{month}"
                                        var = round(((prices[month] / prev_month_price) - 1) * 100,2)
                                        var_formated = f"{var:.1f}"
                                        item[variation_key] = str(var_formated)+' '+'%'
                            
                                    prev_month_key = month
                                    prev_month_price = prices[month]
                                    if item not in result:    
                                        result.append(item)
                                else:
                                    pass
                                    # # If data for a month is missing, set variation to None
                                    # variation_key = f"variation_{prev_month_key}-{month}"
                                    # item[variation_key] = None
                        
                        sorted_months.insert(0,"Brand")
                        sorted_months.insert(1,"Category")
                        sorted_months.insert(2,"Protein_Type")    
                        response_data = {
                            "success": True,
                            "data": result,
                            "total_count":total_count,
                            "months": sorted_months  # Include the sorted list of months in the response
                        }

                        return JsonResponse(response_data, status=200)    
                
                else:
                    records_per_page = 100

                    offset = (page_number - 1) * records_per_page

                    where_conditions = []
                    params = []

                    filter_mappings = {
                        "TimescalesTrend": "Period",
                        "Market_Segment": "Segments",
                        "Competitive_Set": "BrandName",
                        "Category": "Category",
                        "Protein_Type": "ProteinType",
                        "Channel": "ChannelName",
                        "City" : "City",
                        "Brand":"BrandName"
                    }

                    for filter_name, filter_values in filters.items():
                        if filter_values:
                            column_name = filter_mappings.get(filter_name)
                            if column_name:
                                if filter_name == "TimescalesTrend":
                                    if len(filter_values) == 2:
                                        # Handle range of dates
                                        from_date = datetime.strptime(filter_values[0].strip(), "%Y-%m-%d")
                                        to_date = datetime.strptime(filter_values[1].strip(), "%Y-%m-%d")
                                        prev_year_from_date = (from_date - relativedelta(years=1)).strftime("%Y-%m-%d")
                                        prev_year_to_date = (to_date - relativedelta(years=1)).strftime("%Y-%m-%d")
                                        where_conditions.append(f"(({column_name} >= %s AND {column_name} <= %s) OR ({column_name} >= %s AND {column_name} <= %s))")
                                        params.extend([from_date, to_date, prev_year_from_date, prev_year_to_date])
                                    # Handle date range
                                elif filter_name == "City" or filter_name == "Channel":
                                    where_conditions.append(f"{column_name} IN ({', '.join(['%s' for _ in range(len(filter_values))])})")
                                    params.extend(filter_values)
                                else:
                                    if filter_name == "Protein_Type" and filter_values == ["All"]:
                                        #pass
                                        where_conditions.append(f"{column_name} IN ('NotSet','Chicken','Beef','Plant-Based','Pork','Fish','Turkey','Sea Food','Duck','All','Lamb')")
                                        
                                    else:
                                        where_conditions.append(f"{column_name} IN ({', '.join(['%s' for _ in range(len(filter_values))])})")
                                        params.extend(filter_values)
                        else:
                            column_name = filter_mappings.get(filter_name)
                            if filters["Competitive_Set"]==[] and column_name =="BrandName":
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

                    # Construct the WHERE clause
                    where_clause = ''
                    if where_conditions:
                        where_clause = 'WHERE ' + ' AND '.join(where_conditions)

                    order_by_clause = ''
                    if sort_column and sort_type:
                        order_by_clause = f"ORDER BY {filter_mappings[sort_column]} {sort_type}"

                    with connection.cursor() as cursor:
                        cursor.execute(f'''
                            SELECT BrandName, Category, ProteinType, Price, FormattedDate
                            FROM PricesCategoryDetailed 
                            {where_clause}
                            {order_by_clause}
                            ''',
                            params)

                        user_data = cursor.fetchall()
                        # Restructure data
                        grouped_data = defaultdict(lambda: defaultdict(dict))
                        all_months = set()  # Set to store all months encountered
                        for row in user_data:
                                brand = row[0]
                                category = row[1]
                                ProteinType = row[2] 
                                price = row[3]
                                formatted_date = row[4]
                                grouped_data[(brand, category, ProteinType)][formatted_date] = round(price,2)
                                # Extract month from the formatted_date and add it to the set
                                month = formatted_date[:6]  # Extract month-year string
                                all_months.add(month)

                        # Convert set of months to a sorted list
                        sorted_months = sorted(all_months, key=extract_month)
                        
                        total_count = len(grouped_data)
                        start_index = (page_number - 1) * records_per_page
                        end_index = min(start_index + records_per_page, total_count)
                        limited_grouped_data = dict(list(grouped_data.items())[start_index:end_index])

                        result = []
                        for (brand, category,ProteinType), prices in limited_grouped_data.items():
                            item = {
                                "Brand": brand,
                                "Category": category,
                                "Protein_Type":ProteinType,
                            }
                            # Calculate variations
                            prev_year_prices = defaultdict(float)
                            for month in sorted_months:
                                if month in prices:
                                    prev_year_month = (datetime.strptime(month, "%b-%y") - relativedelta(years=1)).strftime("%b-%y")
                                    if prev_year_month in prev_year_prices:
                                        variation_key = f"{month}"
                                        var = round(((prices[month] / prev_year_prices[prev_year_month]) - 1) * 100,2)
                                        var_formated = f"{var:.1f}"
                                        item[variation_key] = str(var_formated)+' '+'%'
                                     
                                    prev_year_prices[month] = prices[month]
                                    if item not in result:
                                        result.append(item)
                                    
                                else:
                                    pass
                                    # If data for a month is missing, set variation to None
                                    # variation_key = f"variation_{prev_year_month}_{month}"
                                    # item[variation_key] = None
                        from_date = datetime.strptime(filters["TimescalesTrend"][0].strip(), "%Y-%m-%d")
                        to_date = datetime.strptime(filters["TimescalesTrend"][1].strip(), "%Y-%m-%d")

                        # Generating a list of months between the start and end dates
                        months_between_dates = [from_date.strftime("%b-%y")]
                        while from_date < to_date:
                            from_date += relativedelta(months=1)
                            months_between_dates.append(from_date.strftime("%b-%y"))

                        # Sorting the list of months and filtering out those not within the timescale trend
                        filter_sorted_months = sorted(set(months_between_dates).intersection(all_months), key=extract_month)
                        filter_sorted_months.insert(0, "Brand")
                        filter_sorted_months.insert(1, "Category") 
                        response_data = {
                            "success": True,
                            "data": result,
                            "total_count":total_count,
                            "months": filter_sorted_months  # Include the sorted list of months in the response
                        }

                        return JsonResponse(response_data, status=200)

                
        except Exception as e:
            return JsonResponse({'success': False, 'message': str(e)}, status=500)
        

