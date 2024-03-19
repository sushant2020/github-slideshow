from django.urls import path
from .views import GetUserAPI

urlpatterns = [
    path('',GetUserAPI.as_view(), name='get-user')
]