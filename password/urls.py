from django.urls import path
from .views import UpdatetePasswordrAPI, ForgotPasswordAPI

urlpatterns = [
    path('update-password/', UpdatetePasswordrAPI.as_view(), name = 'update-password'),
    path('forgot-password/', ForgotPasswordAPI.as_view(), name = 'forgot-paassword')
]