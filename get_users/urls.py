from django.urls import path
from .views import GetUserAPI,GetUserProfileAPI

urlpatterns = [
    path('',GetUserAPI.as_view(), name='get-user'),
    path('user_detail', GetUserProfileAPI.as_view(),name = 'user-details')
]