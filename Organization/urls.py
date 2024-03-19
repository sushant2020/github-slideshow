from .views import AddOrganization,EditOrganization,DeleteOrganization,CommonOrganizationDropdown
from django.urls import path

urlpatterns = [
    path('add',AddOrganization.as_view(), name='add-organization'),
    path('edit',EditOrganization.as_view(), name='edit-organization'),
    path('delete',DeleteOrganization.as_view(),name = 'delete-organization'),
    path('organization-dropdown',CommonOrganizationDropdown.as_view(), name = 'get-organization')
]