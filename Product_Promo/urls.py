from django.urls import path
from .views import MV_Products,MV_Promotions

# urlpatterns = [
#     path('mv-products',MV_Products.as_view(),name ='mv-product-api')
# ]


urlpatterns = [
    path('product/',MV_Products.as_view(),name ='mv-product-api'),
    path('promotion/', MV_Promotions.as_view(), name ='mv-promotions-api')
]
