# urls.py

from django.urls import path
from .views import LogoutAPI

urlpatterns = [
    path('', LogoutAPI.as_view(), name='logout-api'),
]
