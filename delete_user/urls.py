from django.urls import path
from .views import DeleteteUserAPI

urlpatterns = [
    path('', DeleteteUserAPI.as_view(), name = 'deleteuser-api'),
]