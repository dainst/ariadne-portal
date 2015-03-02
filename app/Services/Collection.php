<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Services\Provider;

class Collection {

    /**
     * Get all collections
     *
     * @return Array of all collections
     */
    public static function all($provider = null) {

        $query = DB::table('DataResource')
                ->select('id', 'name', 'cr_uid')
                ->orderBy('id')
                ->where('type', Utils::getDataResourceType('collection'));
        if($provider){
            $query->where('cr_uid', $provider);
        }

        $collections = $query->paginate(15);
        
        foreach ($collections as &$collection) {
            $collection->provider = Provider::getName($collection->cr_uid);
        }
        return $collections;
    }
    
    /**
     * Get a specific collection
     *
     * @param int $id ID of collection
     * @return A collection object
     */
    public static function get($id){
        
        $collection = DB::table('DataResource')
                ->where('id', $id)
                ->first();
        return $collection;
    }
}