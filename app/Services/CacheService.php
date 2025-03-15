<?php

namespace App\Services;

use App\Booking;
use App\Country;
use App\Customer;
use App\Franchisee;
use App\Http\Helpers\AppHelper;
use App\Pincode;
use App\Subscription;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    /**
     * Cache expiration time in minutes
     */
    const CACHE_EXPIRATION = 60;

    /**
     * Get subscriptions list from cache
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSubscriptionsList()
    {
        return Cache::remember('subscriptions_list', self::CACHE_EXPIRATION, function() {
            return Subscription::select(['id', 'name'])->get();
        });
    }

    /**
     * Get subscriptions with price from cache
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSubscriptionsWithPrice()
    {
        return Cache::remember('subscriptions_with_price', self::CACHE_EXPIRATION, function() {
            return Subscription::select(['id', 'name', 'price'])->get();
        });
    }

    /**
     * Get booking statuses from cache
     *
     * @return \Illuminate\Support\Collection
     */
    public function getBookingStatuses()
    {
        return Cache::remember('booking_statuses', self::CACHE_EXPIRATION, function() {
            return Booking::distinct('status')->pluck('status');
        });
    }

    /**
     * Get franchisee by ID
     *
     * @param int $id
     * @return \App\Franchisee|null
     */
    public function getFranchisee($id)
    {
        return Cache::remember('franchisee_' . $id, self::CACHE_EXPIRATION, function() use ($id) {
            return Franchisee::select(['id', 'code'])->find($id);
        });
    }

    /**
     * Get countries list from cache
     *
     * @param int|null $selectedCountryId
     * @return string
     */
    public function getCountriesList($selectedCountryId = null)
    {
        return Cache::remember('countries_list_' . ($selectedCountryId ?? 'default'), self::CACHE_EXPIRATION, function() use ($selectedCountryId) {
            return AppHelper::countriesOptionList($selectedCountryId);
        });
    }

    /**
     * Get pincodes list from cache
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPincodesList()
    {
        return Cache::remember('pincodes_list', self::CACHE_EXPIRATION, function() {
            return Pincode::get();
        });
    }

    /**
     * Get customers list from cache
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCustomersList()
    {
        return Cache::remember('customers_list', self::CACHE_EXPIRATION, function() {
            return Customer::get();
        });
    }

    /**
     * Get specific customer from cache
     *
     * @param int $id
     * @return \App\Customer|null
     */
    public function getCustomer($id)
    {
        return Cache::remember('customer_' . $id, self::CACHE_EXPIRATION, function() use ($id) {
            return Customer::find($id);
        });
    }

    /**
     * Get specific country from cache
     *
     * @param int $id
     * @return \App\Country|null
     */
    public function getCountry($id)
    {
        return Cache::remember('country_' . $id, self::CACHE_EXPIRATION, function() use ($id) {
            return Country::find($id);
        });
    }

    /**
     * Clear booking related cache
     *
     * @return void
     */
    public function clearBookingCache()
    {
        Cache::forget('subscriptions_list');
        Cache::forget('booking_statuses');
        Cache::forget('subscriptions_with_price');

        // Clear all booking list cache entries
        $cacheKeys = Cache::get('booking_cache_keys', []);
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        // Reset the cache keys
        Cache::put('booking_cache_keys', [], self::CACHE_EXPIRATION);

        // Clear dashboard stats cache
        $dashboardCacheKeys = Cache::get('dashboard_cache_keys', []);
        foreach ($dashboardCacheKeys as $key) {
            Cache::forget($key);
        }

        // Reset dashboard cache keys
        Cache::put('dashboard_cache_keys', [], self::CACHE_EXPIRATION);
    }

    /**
     * Clear report related cache
     *
     * @return void
     */
    public function clearReportCache()
    {
        // Clear customer sales report cache
        $reportCacheKeys = Cache::get('report_cache_keys', []);
        foreach ($reportCacheKeys as $key) {
            Cache::forget($key);
        }

        // Reset report cache keys
        Cache::put('report_cache_keys', [], self::CACHE_EXPIRATION);
    }

    /**
     * Register dashboard cache key
     *
     * @param string $key
     * @return void
     */
    public function registerDashboardCacheKey($key)
    {
        $cacheKeys = Cache::get('dashboard_cache_keys', []);
        $cacheKeys[] = $key;
        Cache::put('dashboard_cache_keys', array_unique($cacheKeys), self::CACHE_EXPIRATION);
    }

    /**
     * Register report cache key
     *
     * @param string $key
     * @return void
     */
    public function registerReportCacheKey($key)
    {
        $cacheKeys = Cache::get('report_cache_keys', []);
        $cacheKeys[] = $key;
        Cache::put('report_cache_keys', array_unique($cacheKeys), self::CACHE_EXPIRATION);
    }

    /**
     * Register booking cache key
     *
     * @param string $key
     * @return void
     */
    public function registerBookingCacheKey($key)
    {
        $cacheKeys = Cache::get('booking_cache_keys', []);
        $cacheKeys[] = $key;
        Cache::put('booking_cache_keys', array_unique($cacheKeys), self::CACHE_EXPIRATION);
    }

    /**
     * Get branch list from cache
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBranchList()
    {
        return Cache::remember('branch_list', self::CACHE_EXPIRATION, function() {
            return \App\Branch::select(['id', 'name', 'code'])->get();
        });
    }

    /**
     * Get specific branch from cache
     *
     * @param int $id
     * @return \App\Branch|null
     */
    public function getBranch($id)
    {
        return Cache::remember('branch_' . $id, self::CACHE_EXPIRATION, function() use ($id) {
            return \App\Branch::find($id);
        });
    }

    /**
     * Get delivery statuses from cache
     *
     * @return \Illuminate\Support\Collection
     */
    public function getDeliveryStatuses()
    {
        return Cache::remember('delivery_statuses', self::CACHE_EXPIRATION, function() {
            return \App\Delivery::distinct('delivery_status')->pluck('delivery_status');
        });
    }

    /**
     * Get pincode details from cache
     *
     * @param int $id
     * @return \App\Pincode|null
     */
    public function getPincode($id)
    {
        return Cache::remember('pincode_' . $id, self::CACHE_EXPIRATION, function() use ($id) {
            return Pincode::find($id);
        });
    }

    /**
     * Get subscription by type from cache
     *
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSubscriptionsByType($type)
    {
        return Cache::remember('subscriptions_by_type_' . $type, self::CACHE_EXPIRATION, function() use ($type) {
            return Subscription::where('consg_type', $type)->select(['id', 'name', 'price'])->get();
        });
    }

    /**
     * Get user details from cache
     *
     * @param int $id
     * @return array
     */
    public function getUserDetails($id)
    {
        return Cache::remember('user_details_' . $id, self::CACHE_EXPIRATION, function() use ($id) {
            $user = \App\User::select(['id', 'username', 'first_name', 'last_name'])
                ->where('id', $id)
                ->first();

            if (!$user) {
                return [
                    'id' => '',
                    'username' => 'Unknown',
                    'first_name' => '',
                    'last_name' => ''
                ];
            }

            return $user->toArray();
        });
    }

    /**
     * Get user permissions from cache
     *
     * @param int $userId
     * @param int $moduleId
     * @return array
     */
    public function getUserPermissions($userId, $moduleId)
    {
        return Cache::remember('user_permissions_' . $userId . '_' . $moduleId, self::CACHE_EXPIRATION, function() use ($userId, $moduleId) {
            $user = \App\User::find($userId);

            if (!$user) {
                return [
                    'view' => false,
                    'add' => false,
                    'edit' => false,
                    'delete' => false
                ];
            }

            $permissions = [
                'view' => false,
                'add' => false,
                'edit' => false,
                'delete' => false
            ];

            foreach ($user->roles as $role) {
                if ($role->hasViewPermission($moduleId)) {
                    $permissions['view'] = true;
                }

                if ($role->hasAddPermission($moduleId)) {
                    $permissions['add'] = true;
                }

                if ($role->hasEditPermission($moduleId)) {
                    $permissions['edit'] = true;
                }

                if ($role->hasDeletePermission($moduleId)) {
                    $permissions['delete'] = true;
                }
            }

            return $permissions;
        });
    }

    /**
     * Clear user permissions cache
     *
     * @param int $userId
     * @return void
     */
    public function clearUserPermissionsCache($userId)
    {
        // Get all module IDs
        $moduleIds = \App\Module::pluck('id')->toArray();

        // Clear cache for each module
        foreach ($moduleIds as $moduleId) {
            Cache::forget('user_permissions_' . $userId . '_' . $moduleId);
        }
    }

    /**
     * Clear all cache
     *
     * @return void
     */
    public function clearAllCache()
    {
        $this->clearBookingCache();
        $this->clearReportCache();

        // Clear specific caches
        Cache::forget('branch_list');
        Cache::forget('delivery_statuses');
        Cache::forget('countries_list_default');
        Cache::forget('pincodes_list');
        Cache::forget('customers_list');

        // Clear specific types by pattern matching is not directly supported in Laravel cache
        // We need to maintain list of keys
    }
}
