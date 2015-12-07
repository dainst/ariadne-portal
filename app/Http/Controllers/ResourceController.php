<?php

namespace App\Http\Controllers;
use DebugBar;

use Illuminate\Support\Facades\Config;
use App\Services\Resource;
use App\Services\Utils;
use Request;


class ResourceController extends Controller {


    public function index() {
        return view('resource.search');
    }

    /**
     * Filters out the spatial items which can be shown on a map.
     *
     * @param $resource
     * @return array of item from the spatial array which have
     *   a location property.
     */
    private function getValidGeoItems($resource) {
        $spatialItems = array();

        if (!array_key_exists('spatial',$resource['_source']))
            return $spatialItems;

        foreach ($resource['_source']['spatial'] as $spatialItem){
            if (!array_key_exists('location',$spatialItem))
                continue;
            array_push($spatialItems,$spatialItem);
        }

        return $spatialItems;
    }

    private function getNearbySpatialItems($spatialItem) {

        $nearbySpatialItems = array();

        foreach (Resource::geoDistanceQuery($spatialItem['location'])
                 as $nearbyResource) {

            foreach ($this->getValidGeoItems($nearbyResource)
                     as $validNearbySpatialItem) {
                array_push($nearbySpatialItems,$validNearbySpatialItem);
            }
        }

        return $nearbySpatialItems;
    }

    private function getCitationLink($resource) {

        // Check if a persistent identifier is specified
        $identifiers = ['doi:', 'hdl:', 'urn:', 'http://', 'https://'];

        if (array_key_exists('originalId', $resource['_source'])) {
            $originalId = $resource['_source']['originalId'];

            foreach ($identifiers as $identifier) {
                if (substr($originalId, 0, strlen($identifier)) === $identifier)
                    return $originalId;
            }
        }

        // Return landing page url if no persistent identifier is given
        if (array_key_exists('landingPage', $resource['_source']))
            return $resource['_source']['landingPage'];

        // Return portal url if no landing page url is given
        return 'http://' . $_SERVER['HTTP_HOST'] . '/' . $resource['_type'] . '/' . $resource['_id'];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return View
     */
    public function show($id) {

        $resource = Resource::get($id);

        $spatial_items = $this->getValidGeoItems($resource);
        $nearby_spatial_items = null;
        if (!empty($spatial_items)) {
            $nearby_spatial_items = $this->getNearbySpatialItems($spatial_items[0]);
        }

        $similar_resources = Resource::thematicallySimilarQuery($resource);

        $citationLink = $this->getCitationLink($resource);

        $parts_count = null;
        if($resource['_source']['resourceType'] == 'collection') {
            $parts_count = Resource::getPartsCountQuery($resource);
        }
        
        return [
            'resource' => $resource,
            'geo_items' => $spatial_items,
            'nearby_geo_items' => $nearby_spatial_items,
            'similar_resources' => $similar_resources,
            'citationLink' => $citationLink,
            'parts_count' => $parts_count
        ];
    }

    /**
     * Performs a faceted search depending on the GET-values
     * Eg ?q=dig&keyword=england does a free text search for dig in
     * resources where the keyword england exists
     *
     * @return View rendered pagination for search results
     */
    public function search() {

        $query = ['aggregations' => Config::get('app.elastic_search_aggregations')];

        // add geogrid aggregation
        $ghp = Request::has('ghp') ? Request::input('ghp') : 2;
        $query['aggregations']['geogrid'] = ['geohash_grid' => [
            'field' => 'spatial.location', 'precision' => intval($ghp) 
        ]];


        $nrBuckets= 10;
        $thisYear= 2016; // WARNING: ES INDEX MUST NOT CONTAIN DATES AFTER THIS YEAR. TODO FIX
        $logBase= 10;

        $startYear = Request::has("start") ? intval(Request::get("start")) : -600000;
        $endYear = Request::has("end") ? intval(Request::get("end")) : 2010;

        $r= log($thisYear-$endYear,$logBase);
        $l= log($thisYear-$startYear,$logBase);
        $d=($l-$r)/$nrBuckets;

        $selectedRanges=array();

        for ($i=0;$i<$nrBuckets;$i++) {

            array_push($selectedRanges,
                ['to'=>sprintf('%06d',$thisYear-pow($logBase,$r+$i*$d)),
                    'from'=>sprintf('%06d',$thisYear-pow($logBase,$r+$i*$d+$d))]);
        }

        $query['aggregations']['date_ranges'] = ['date_range' => [
            'field' => 'temporal.from',
            'format' => 'yyyyyy',
            'ranges'  => $selectedRanges
        ]];

        $q = ['match_all' => []];

        if (Request::has('q')) {
            $field_groups = Config::get('app.elastic_search_field_groups');
            
            if(Request::has('fields') && array_key_exists(Request::get('fields'), $field_groups)){
                foreach ($field_groups[Request::get('fields')] as $field){
                    $q = ['match' => [$field => Request::get('q')]];
                    $query['query']['bool']['should'][] = $q;
                }
            }else {
                $q = ['query_string' => ['query' => Request::get('q')]];
                $query['query']['bool']['must'][] = $q;
            }
        } else {
            $query['query']['bool']['must'][] = ['query_string' => ['query' => '*']];
        }

        foreach ($query['aggregations'] as $key => $aggregation) {
            if (Request::has($key)) {
                $values = Utils::getArgumentValues($key);

                $field = $aggregation['terms']['field'];

                foreach ($values as $value) {
                    $fieldQuery = [];
                    $fieldQuery[$field] = $value;
                    $query['query']['bool']['must'][] = ['match' => $fieldQuery];
                }
            }
        }        
        
        
        // TODO: refactor so that ES service takes care of bbox parsing
        if (Request::has('bbox')) {
            $bbox = explode(',', Request::input('bbox'));
            $query['query'] = [
                'filtered' => [
                    'query' => $query['query'],
                    'filter' => [
                        'geo_bounding_box' => [
                            'spatial.location' => [
                                'top_left' => [
                                    'lat' => floatval($bbox[3]),
                                    'lon' => floatval($bbox[0])
                                ],                                
                                'bottom_right' => [
                                    'lat' => floatval($bbox[1]),
                                    'lon' => floatval($bbox[2])
                                ]
                            ]
                        ]
                    ]
                ]
            ];
        }
        
        $hits = Resource::search($query, 'resource');

        return [
            'aggregations' => $query['aggregations'],
            'translateAggregations' => Config::get('app.translate_aggregations'),
            'hits' => $hits
        ];
    }
}
