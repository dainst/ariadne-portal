<?php
/**
 * Service for accessing resources in the catalog
 *
 * Wraps access to the elasticsearch index representing the ariadne catalog.
 */

namespace app\Services;
use App\Services\ElasticSearch;
use Config;


class Resource
{

    const RESOURCE_TYPE = 'resource';

    /**
     * Get a document for a single resource from the catalog index
     *
     * @param type $id id of the document (e.g. dataset id)
     * @return Array all the values in _source from Elastic Search
     * @throws Exception if document is not found
     */
    public static function get($id){
        return ElasticSearch::get($id, Config::get('app.elastic_search_catalog_index'), self::RESOURCE_TYPE);
    }

    /**
     * Performs a paginated search against Elastic Search
     *
     * @param Array $query Array containing the elastic search query
     * @return LengthAwarePaginator paginated result of the search
     */
    public static function search($query) {
        return ElasticSearch::search($query, Config::get('app.elastic_search_catalog_index'), self::RESOURCE_TYPE);
    }

    /**
     * @param $index
     * @param $type
     * @param $location must have keys and values for lat and lon.
     * @return resources within a range of x km of $location.
     */
    public static function geoDistanceQuery($location) {

        $json = '{
            "query" : {

                "filtered" : {
                  "query" : {
                      "match_all" : {}
                  },
                  "filter" : {
                      "geo_distance" : {
                          "distance" : "20km",
                          "spatial.location" : {
                              "lat" : '.$location['lat'].',
                              "lon" : '.$location['lon'].'
                          }
                      }
                  }
              }
            }
        }';

        $params = [
            'index' => Config::get('app.elastic_search_catalog_index'),
            'type' => self::RESOURCE_TYPE,
            'body' => $json
        ];

        $result = ElasticSearch::getClient()->search($params);
        return $result['hits']['hits'];
    }


    /**
     * @param $resource
     * @return the seven most similar resources based on subjects & time periods
     */
    public static function thematicallySimilarQuery($resource) {

        $json = '{
        "query": {
          "bool": {
            "must_not": {
              "match": {
                "_id": "' . $resource['_id'] .  '"
              }
            },
            "should": [';

        $firstMatch = true;

        if (array_key_exists('nativeSubject', $resource['_source'])) {
            foreach ($resource['_source']['nativeSubject'] as $subject) {
                if (!array_key_exists('prefLabel', $subject))
                    continue;

                if ($firstMatch)
                    $firstMatch = false;
                else
                    $json .= ',';

                $json .= '{
              "match": {
                "nativeSubject.prefLabel": "' . $subject['prefLabel'] . '"
              }
            }';
            }
        }

        if (array_key_exists('temporal', $resource['_source'])) {
            foreach ($resource['_source']['temporal'] as $temporal) {
                if (!array_key_exists('periodName', $temporal))
                    continue;

                if ($firstMatch)
                    $firstMatch = false;
                else
                    $json .= ',';

                $json .= '{
              "match": {
                "temporal.periodName": "' . $temporal['periodName'] . '"
              }
            }';
            }
        }

        $json .= '],
              "minimum_should_match" : 1
            }
          }
        }';

        $params = [
            'index' => Config::get('app.elastic_search_catalog_index'),
            'type' => self::RESOURCE_TYPE,
            'size' => 7,
            'body' => $json
        ];

        $result = ElasticSearch::getClient()->search($params);
        return $result['hits']['hits'];
    }

    /**
     * @param $resource
     * @return count of parts in a collection
     */
    public static function getPartsCountQuery($resource) {

        $json = '{ "query": { "match" : { "isPartOf": ' . $resource['_id'] . ' } } }';

        $params = [
            'index' => Config::get('app.elastic_search_catalog_index'),
            'type' => self::RESOURCE_TYPE,
            'body' => $json
        ];

        $result = ElasticSearch::getClient()->search($params);
        return $result['hits']['total'];
    }


    /**
     * Creates an aggregation partial with pre-calculated date
     * ranges of non-equal length. The ranges are calculated on
     * the basis of an algorithm which creates buckets spanning
     * each time more years in an exponential manner, the more
     * far away they are, looking backwards in time from the next year
     * of the current date.
     *
     * @param $startYear int|string denoting one margin of the data range to divide up into buckets.
     * @param $endYear int|string denoting one margin of the date range to divide up into bucktes.
     *   Can be before or after $startYear.
     * @param $nrBuckets
     * @return array with a date_range aggregation object ready for querying
     *   elasticsearch. It contains $nrBuckets of ranges which span each at least one year.
     *   If the difference of $startYear and $endYear is to low for that, one of them gets
     *   adjusted. If at least one of the dates has a year which is in the future, the whole range
     *   gets shifted so that one of the dates will be the current year. Their difference will remain
     *   the same though.
     */
    public static function prepareDateRangesAggregation($startYear,$endYear,$nrBuckets) {

        self::switchIfNeccessary($startYear,$endYear);
        self::shiftRangeIfNecessary($startYear,$endYear);
        self::extendRangeIfNecessary($startYear,$endYear,$nrBuckets);

        return ['filters' => [
            'filters' => self::calculateRanges($startYear,$endYear,$nrBuckets)
        ]];

    }

    /**
     * @param $startYear
     * @param $endYear must be greater or equal $endYear
     */
    private static function shiftRangeIfNecessary(&$startYear,&$endYear) {
        if ($endYear>date("Y")) {
            $shiftWidth=$endYear-date("Y");
            $endYear=$endYear-$shiftWidth;
            $startYear=$startYear-$shiftWidth;
        }
    }

    private static function extendRangeIfNecessary(&$startYear,$endYear,$nrBuckets) {
        if (($endYear-$startYear)<$nrBuckets) {
            $startYear=$endYear-$nrBuckets;
        }
    }

    private static function switchIfNeccessary(&$startYear,&$endYear) {
        if ($endYear<$startYear) {
            $temp=$startYear;
            $startYear=$endYear;
            $endYear=$temp;
        }
    }


    /**
     * We arrange the years on the y-axis of a graph whose x-axis serves
     * us to map them to something linear. This allows for dividing any
     * given ranges from $startYear to $endYear into a $nrBuckets of equals
     * sub-ranges. Mapping back the start and end points of these ranges
     * gives us the corresponding years again.
     *
     * The mapping function is a plain exponential function. The x-axis value
     * is used as the exponent which results in ever growing year values on the
     * y-axis. Arranging the years in this way accounts for an intuition where
     * the resources we have are distributed more sparsely the more we
     * move backwards in time.
     *
     * To use the exponential function, $startYear and $endYear are mirrored on and
     * arranged relatively to a reference year, which is the next year seen from now.
     * This year acts the null point of the y-axis of the the graph.
     *
     * @param $startYear
     * @param $endYear
     * @param $nrBuckets
     * @return array with range items like
     *   [ 'from' => string, - year with six digits.
     *   'to' => string ]    - year with six digits.
     */
    private static function calculateRanges($startYear,$endYear,$nrBuckets) {

        $xStartingPoint= self::getXVal($endYear);
        $xDelta=(self::getXVal($startYear)-$xStartingPoint)/$nrBuckets;

        $ranges=array();
        for ($i=0;$i<$nrBuckets;$i++) {
            self::addRange($ranges,
                self::getYear($xStartingPoint+$i*$xDelta+$xDelta), // mirrored, right margin becomes start year
                self::getYear($xStartingPoint+$i*$xDelta)          // mirrored, left margin becomes end year
            );
        }
        return array_reverse($ranges);
    }

    /**
     * Generates a meaningful key for the range and places
     * a newly generated elasticsearch range agg item to ranges[key]
     *
     * @param $ranges
     * @param $rangeStartYear
     * @param $rangeEndYear
     */
    private static function addRange(&$ranges,$rangeStartYear,$rangeEndYear) {
        $ranges[$rangeStartYear.":".$rangeEndYear]=self::makeRange(
            $rangeStartYear,
            $rangeEndYear);
    }

    /**
     * ElasticSearch aggregation partial.
     *
     * @param $rangeStartYear
     * @param $rangeEndYear
     * @return array
     */
    private static function makeRange($rangeStartYear, $rangeEndYear) {
        return
            [
                'bool' => [
                    'must' => [
                        [
                            'range' => [
                                'temporal.until' => [
                                    'gte' => $rangeStartYear,
                                    'format' => 'yyyyyy'
                                ]
                            ]
                        ],
                        [
                            'range' => [
                                'temporal.from' => [
                                    'lte' => $rangeEndYear,
                                    'format' => 'yyyyyy'
                                ]
                            ]
                        ]
                    ]
                ]
            ];
    }

    private static function getXVal($year) {
        return log(self::referenceYear()-$year,self::LOG_BASE);
    }

    /**
     * @param $xVal
     * @return string year
     */
    private static function getYear($xVal) {
        return "".round(self::referenceYear()-pow(self::LOG_BASE,$xVal));
    }

    const LOG_BASE = 10;

    private static function referenceYear() {
        return date("Y")+1;
    }

}