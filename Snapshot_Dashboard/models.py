from django.db import models

class SnapshotByRegionView(models.Model):
    Product = models.CharField(max_length=255, primary_key=True)
    Birmingham = models.DecimalField(max_digits=10, decimal_places=2, null=True)
    Belfast = models.DecimalField(max_digits=10, decimal_places=2, null=True)
    Cardiff = models.DecimalField(max_digits=10, decimal_places=2, null=True)
    Glasgow = models.DecimalField(max_digits=10, decimal_places=2, null=True)
    Liverpool = models.DecimalField(max_digits=10, decimal_places=2, null=True)
    Leeds = models.DecimalField(max_digits=10, decimal_places=2, null=True)
    Manchester = models.DecimalField(max_digits=10, decimal_places=2, null=True)
    London = models.DecimalField(max_digits=10, decimal_places=2, null=True)
    Bristol = models.DecimalField(max_digits=10, decimal_places=2, null=True)
    Period = models.DateField()
    FormattedDate = models.CharField(max_length=255)
    Segments = models.CharField(max_length=255)
    Brand = models.CharField(max_length=255)
    Category = models.CharField(max_length=255)
    ProteinType = models.CharField(max_length=255)
    ChannelName = models.CharField(max_length=255)

    class Meta:
        managed = False
        db_table = 'SnapshotByRegionView'

class SnapshotByChannelView(models.Model):
    Product = models.CharField(max_length=255,primary_key=True)
    Dine_In = models.DecimalField(max_digits=10, decimal_places=2,db_column='Dine In')
    UberEats = models.DecimalField(max_digits=10, decimal_places=2)
    Deliveroo = models.DecimalField(max_digits=10, decimal_places=2)
    JustEat = models.DecimalField(max_digits=10, decimal_places=2)
    City = models.CharField(max_length=255)
    Period = models.DateField()
    FormattedDate = models.CharField(max_length=255)
    Segments = models.CharField(max_length=255)
    Brand = models.CharField(max_length=255)
    Category = models.CharField(max_length=255)
    ProteinType = models.CharField(max_length=255)
    DeliverySum = models.DecimalField(max_digits=10, decimal_places=2)
    NonNullCounter = models.DecimalField(max_digits=10, decimal_places=2)
    Delivery_Average = models.DecimalField(max_digits=10, decimal_places=2,db_column='Delivery Average')
    DineIn_Delivery = models.DecimalField(max_digits=10, decimal_places=2,db_column='DineIn/Delivery')

    class Meta:
        managed = False
        db_table = 'SnapshotByChannelView'




class SnapshotByVariation(models.Model):
    Product = models.CharField(max_length=255, primary_key=True)
    MinPrice = models.DecimalField(max_digits=12, decimal_places=2)
    MaxPrice = models.DecimalField(max_digits=12, decimal_places=2)
    AvgPrice = models.DecimalField(max_digits=12, decimal_places=2)
    ModePrice = models.DecimalField(max_digits=12, decimal_places=2)
    Period = models.DateField()
    FormattedDate = models.CharField(max_length=255)
    Segments = models.CharField(max_length=255)
    Brand = models.CharField(max_length=255)
    Category = models.CharField(max_length=255)
    ProteinType = models.CharField(max_length=255)
    ChannelName = models.CharField(max_length=255)

    class Meta:
        managed = False
        db_table = 'SnapshotByVariation'

    @property
    def Variation(self):
        if self.MinPrice == 0:
            return None
        return round(((self.MaxPrice / self.MinPrice) - 1) * 100, 2)

