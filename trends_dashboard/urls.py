from .views import Trends_API #,Trends_Items_Comparision_API,Trends_Items_year_comparision,Trends_Category_NC_API,Trends_Category_Comparision_API,Trends_Category_year_comparision
from django.urls import path


urlpatterns = [
    path('',Trends_API.as_view(), name='trends-api')
]