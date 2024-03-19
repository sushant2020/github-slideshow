from django.urls import path
from .views import CreateUserAPI

urlpatterns = [
    path('', CreateUserAPI.as_view(), name = 'createuser-api'),
]




