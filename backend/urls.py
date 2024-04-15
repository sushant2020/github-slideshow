"""
URL configuration for backend project.

The `urlpatterns` list routes URLs to views. For more information please see:
    https://docs.djangoproject.com/en/5.0/topics/http/urls/
Examples:
Function views
    1. Add an import:  from my_app import views
    2. Add a URL to urlpatterns:  path('', views.home, name='home')
Class-based views
    1. Add an import:  from other_app.views import Home
    2. Add a URL to urlpatterns:  path('', Home.as_view(), name='home')
Including another URLconf
    1. Import the include() function: from django.urls import include, path
    2. Add a URL to urlpatterns:  path('blog/', include('blog.urls'))
"""
# backend/urls.py

from django.contrib import admin
from django.urls import path, include
from django.views.generic import TemplateView
from django.conf import settings
from django.conf.urls.static import static

urlpatterns = [
    path('admin/', admin.site.urls),
    path('login/', include('login.urls')),
    path('create-user/',include('create_user.urls')),
    path('logout/',include('logout.urls')),
    path('delete-user/',include('delete_user.urls')),
    path('password/', include('password.urls')),
    path('snapshot/',include('Snapshot_Dashboard.urls')),
    path('filter/',include('filters.urls')),
    path('trends/',include('trends_dashboard.urls')),
    path('organization/',include('Organization.urls')),
    path('',include('hello.urls')),
    path('get-user/',include("get_users.urls")),
    path('edit-user/',include("edit_user.urls")),
    path('exports/', include('exports.urls')),
    path('',include('Product_Promo.urls')),
    path('dashboard/', include("dashboard.urls"))
]



# urlpatterns += static(settings.STATIC_URL, document_root=settings.STATIC_ROOT)
# print(static(settings.STATIC_URL, document_root=settings.STATIC_ROOT))