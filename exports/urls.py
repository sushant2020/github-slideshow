from django.urls import path
from .views import SnapshotRegionExport

urlpatterns = [
    path('snapshotregion',SnapshotRegionExport.as_view(),name='region-export')
]