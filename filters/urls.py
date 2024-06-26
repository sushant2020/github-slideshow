from django.urls import path
from .views import Timescalefitler,CommonFilter,Brand_SegmentFilterAPI,OrganizationDropdown,UserOrganizationDropdown,ItemFilterAPI,Competitive_SetAPI,InitialTimescalefilter,PromoTypeFilter,PromoFilter

urlpatterns = [
    path('timescale-filter' , Timescalefitler.as_view(), name='Timescalefilter-api'),
    path('common-filters',CommonFilter.as_view(),name = 'Commonfilters-api'),
    path('brand-filter',Brand_SegmentFilterAPI.as_view(),name = 'brandfitler-api'),
    path('organization',OrganizationDropdown.as_view(), name = 'organization'),
    path("user-organization",UserOrganizationDropdown.as_view(),name='user-organization'),
    path("items",ItemFilterAPI.as_view(), name = "items-api"),
    path("competitive-set",Competitive_SetAPI.as_view(),name="competitive-set"),
    path("initial-timescale",InitialTimescalefilter.as_view(),name="initial-timescale"),
    path("promotype-filter",PromoTypeFilter.as_view(),name="promotype-filter"),
    path("promo-filter",PromoFilter.as_view(),name="promo-filter")
]  

