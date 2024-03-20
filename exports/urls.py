from django.urls import path
from .views import SnapshotRegionExport, TrendsExport

urlpatterns = [
    path('snapshotregion',SnapshotRegionExport.as_view(),name='region-export'),
    path('trends',TrendsExport.as_view(),name='trends-export')
]