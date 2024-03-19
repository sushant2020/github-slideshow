from django.urls import path
from .views import SnapshotRegionAPI, SnapshotChannelAPI,SnapshotVariationAPI

urlpatterns = [
    path('region', SnapshotRegionAPI.as_view(), name='snapshot-region-api'),
    path('variation', SnapshotVariationAPI.as_view(), name='snapshot-variation-api'),
    path('channel', SnapshotChannelAPI.as_view(), name='snapshot-channel-api'),
]

