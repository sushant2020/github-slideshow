<?php

namespace App\Providers;

use App\Repositories\Activity\ActivityRepository;
use App\Repositories\Activity\EloquentActivity;
use App\Repositories\ClsfDefn\ClsfDefnRepository;
use App\Repositories\ClsfDefn\EloquentClsfDefn;
use App\Repositories\ClsfValue\ClsfValueRepository;
use App\Repositories\ClsfValue\EloquentClsfValue;
use App\Repositories\Comment\CommentRepository;
use App\Repositories\Comment\EloquentComment;
use App\Repositories\Feature\EloquentFeature;
use App\Repositories\Feature\FeatureRepository;
use App\Repositories\GRN\EloquentGRN;
use App\Repositories\GRN\GRNRepository;
use App\Repositories\ImportLogger\EloquentImportLogger;
use App\Repositories\ImportLogger\ImportLoggerRepository;
use App\Repositories\InternalSupplier\EloquentInternalSupplier;
use App\Repositories\InternalSupplier\InternalSupplierRepository;
use App\Repositories\Inventory\EloquentInventory;
use App\Repositories\Inventory\InventoryRepository;
use App\Repositories\Module\EloquentModule;
use App\Repositories\Module\ModuleRepository;
use App\Repositories\NewsFeed\EloquentNewsFeed;
use App\Repositories\NewsFeed\NewsFeedRepository;
use App\Repositories\Permission\EloquentPermission;
use App\Repositories\Permission\PermissionRepository;
use App\Repositories\PriceDatum\EloquentPriceDatum;
use App\Repositories\PriceDatum\PriceDatumRepository;
use App\Repositories\ProductClassification\EloquentProductClassification;
use App\Repositories\ProductClassification\ProductClassificationRepository;
use App\Repositories\ProductComment\EloquentProductComment;
use App\Repositories\ProductComment\ProductCommentRepository;
use App\Repositories\ProductFeature\EloquentProductFeature;
use App\Repositories\ProductFeature\ProductFeatureRepository;
use App\Repositories\Product\EloquentProduct;
use App\Repositories\Product\ProductRepository;
use App\Repositories\PurchaseOrder\EloquentPurchaseOrder;
use App\Repositories\PurchaseOrder\PurchaseOrderRepository;
use App\Repositories\Relationship\EloquentRelationship;
use App\Repositories\Relationship\RelationshipRepository;
use App\Repositories\RolePermission\EloquentRolePermission;
use App\Repositories\RolePermission\RolePermissionRepository;
use App\Repositories\Role\EloquentRole;
use App\Repositories\Role\RoleRepository;
use App\Repositories\SearchHistory\EloquentSearchHistory;
use App\Repositories\SearchHistory\SearchHistoryRepository;
use App\Repositories\Setting\EloquentSetting;
use App\Repositories\Setting\SettingRepository;
use App\Repositories\Source\EloquentSource;
use App\Repositories\Source\SourceRepository;
use App\Repositories\Supplier\EloquentSupplier;
use App\Repositories\Supplier\SupplierRepository;
use App\Repositories\Tag\EloquentTag;
use App\Repositories\Tag\TagRepository;
use App\Repositories\Task\EloquentTask;
use App\Repositories\Task\TaskRepository;
use App\Repositories\Tier\EloquentTier;
use App\Repositories\Tier\TierRepository;
use App\Repositories\UsageDatum\EloquentUsageDatum;
use App\Repositories\UsageDatum\UsageDatumRepository;
use App\Repositories\UserActivity\EloquentUserActivity;
use App\Repositories\UserActivity\UserActivityRepository;
use App\Repositories\UserRole\EloquentUserRole;
use App\Repositories\UserRole\UserRoleRepository;
use App\Repositories\Users\EloquentUsers;
use App\Repositories\Users\UsersRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(ActivityRepository::class, EloquentActivity::class);
        $this->app->bind(ClsfDefnRepository::class, EloquentClsfDefn::class);
        $this->app->bind(ClsfValueRepository::class, EloquentClsfValue::class);
        $this->app->bind(CommentRepository::class, EloquentComment::class);
        $this->app->bind(FeatureRepository::class, EloquentFeature::class);
        $this->app->bind(GRNRepository::class, EloquentGRN::class);
        $this->app->bind(ImportLoggerRepository::class, EloquentImportLogger::class);
        $this->app->bind(InternalSupplierRepository::class, EloquentInternalSupplier::class);
        $this->app->bind(InventoryRepository::class, EloquentInventory::class);
        $this->app->bind(ModuleRepository::class, EloquentModule::class);
        $this->app->bind(NewsFeedRepository::class, EloquentNewsFeed::class);
        $this->app->bind(PermissionRepository::class, EloquentPermission::class);
        $this->app->bind(PriceDatumRepository::class, EloquentPriceDatum::class);
        $this->app->bind(ProductRepository::class, EloquentProduct::class);
        $this->app->bind(ProductClassificationRepository::class, EloquentProductClassification::class);
        $this->app->bind(ProductCommentRepository::class, EloquentProductComment::class);
        $this->app->bind(ProductFeatureRepository::class, EloquentProductFeature::class);
        $this->app->bind(PurchaseOrderRepository::class, EloquentPurchaseOrder::class);
        $this->app->bind(RelationshipRepository::class, EloquentRelationship::class);
        $this->app->bind(RoleRepository::class, EloquentRole::class);
        $this->app->bind(RolePermissionRepository::class, EloquentRolePermission::class);
        $this->app->bind(SearchHistoryRepository::class, EloquentSearchHistory::class);
        $this->app->bind(SettingRepository::class, EloquentSetting::class);
        $this->app->bind(SourceRepository::class, EloquentSource::class);
        $this->app->bind(SupplierRepository::class, EloquentSupplier::class);
        $this->app->bind(TagRepository::class, EloquentTag::class);
        $this->app->bind(TaskRepository::class, EloquentTask::class);
        $this->app->bind(TierRepository::class, EloquentTier::class);
        $this->app->bind(UsageDatumRepository::class, EloquentUsageDatum::class);
        $this->app->bind(UserActivityRepository::class, EloquentUserActivity::class);
        $this->app->bind(UserRoleRepository::class, EloquentUserRole::class);
        $this->app->bind(UsersRepository::class, EloquentUsers::class);
    }
}
