from django.urls import path
from .views import UpdatetePasswordrAPI, ForgotPasswordAPI,ForgotPasswordRedirectAPI

urlpatterns = [
    path('update-password/', UpdatetePasswordrAPI.as_view(), name = 'update-password'),
    path('forgot-password/', ForgotPasswordAPI.as_view(), name = 'forgot-paassword'),
    path('forget-password-redirect',ForgotPasswordRedirectAPI.as_view(),name = 'forget-password-redirect')
]