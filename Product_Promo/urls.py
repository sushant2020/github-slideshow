from django.urls import path
from .views import MV_Products

# urlpatterns = [
#     path('mv-products',MV_Products.as_view(),name ='mv-product-api')
# ]


urlpatterns = [
    path('',MV_Products.as_view(),name ='mv-product-api')
]
