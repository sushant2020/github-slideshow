from django.urls import path
from .views import EditUserAPI

urlpatterns = [
    path('', EditUserAPI.as_view(), name = 'edituser-api'),
]