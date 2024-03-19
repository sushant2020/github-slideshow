# login/urls.py

from django.urls import path
from .views import LoginAPI

urlpatterns = [
    path('', LoginAPI.as_view(), name='login-api'),
]
