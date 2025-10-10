<?php

namespace PaynetEasy\PaynetEasyApi\Util;

use RuntimeException;

class RegionFinder
{
    /**
     * Known coutry codes
     *
     * @var array
     */
    static protected $countryCodes = array
    (
        "AX", "AF", "AL", "DZ", "AS", "AD", "AO", "AI", "AQ", "AG", "AR", "AM", "AW", "AU", "AT",
        "AZ", "BS", "BH", "BD", "BB", "BY", "BE", "BZ", "BJ", "BM", "BT", "BO", "BA", "BW", "BV",
        "BR", "IO", "BN", "BG", "BF", "BI", "KH", "CM", "CA", "CV", "KY", "CF", "TD", "CL", "CN",
        "CX", "CC", "CO", "KM", "CG", "CD", "CK", "CR", "CI", "HR", "CU", "CY", "CZ", "DK", "DJ",
        "DM", "DO", "EC", "EG", "SV", "GQ", "ER", "EE", "ET", "FK", "FO", "FJ", "FI", "FR", "GF",
        "PF", "TF", "GA", "GM", "GE", "DE", "GH", "GI", "GR", "GL", "GD", "GP", "GU", "GT", "GG",
        "GN", "GW", "GY", "HT", "HM", "VA", "HN", "HK", "HU", "IS", "IN", "ID", "IR", "IQ", "IE",
        "IM", "IL", "IT", "JM", "JP", "JE", "JO", "KZ", "KE", "XK", "KI", "KP", "KR", "KW", "KG",
        "LA", "LV", "LB", "LS", "LR", "LY", "LI", "LT", "LU", "MO", "MK", "MG", "MW", "MY", "MV",
        "ML", "MT", "MH", "MQ", "MR", "MU", "YT", "MX", "FM", "MD", "MC", "MN", "ME", "MS", "MA",
        "MZ", "MM", "NA", "NR", "NP", "NL", "AN", "NC", "NZ", "NI", "NE", "NG", "NU", "NF", "MP",
        "NO", "OM", "PK", "PW", "PS", "PA", "PG", "PY", "PE", "PH", "PN", "PL", "PT", "PR", "QA",
        "RE", "RO", "RU", "RW", "BL", "SH", "KN", "LC", "MF", "PM", "VC", "WS", "SM", "ST", "SA",
        "SN", "RS", "SC", "SL", "SG", "SK", "SI", "SB", "SO", "ZA", "GS", "ES", "LK", "SD", "SR",
        "SJ", "SZ", "SE", "CH", "SY", "TW", "TJ", "TZ", "TH", "TL", "TG", "TK", "TO", "TT", "TN",
        "TR", "TM", "TC", "TV", "UG", "UA", "AE", "GB", "US", "UM", "UY", "UZ", "VU", "VE", "VN",
        "VG", "VI", "WF", "EH", "YE", "ZM", "ZW", "EU", "AP", "A2", "A1"
    );

    /**
     * Countries with states
     *
     * @var array
     */
    static protected $countriesWithStates = array
    (
        'AU',
        'CA',
        'US'
    );

    /**
     * Australia states
     *
     * @var array
     */
    static protected $auStates = array
    (
        'ACT'   => 'Australian Capital Territory',
        'NSW'   => 'New South Wales',
        'NT'    => 'Northern Territory',
        'QLD'   => 'Queensland',
        'SA'    => 'South Australia',
        'TAS'   => 'Tasmania',
        'VIC'   => 'Victoria',
        'WA'    => 'Western Australia'
    );

    /**
     * Canada states
     *
     * @var array
     */
    static protected $caStates = array
    (
        'AB'    => 'Alberta',
        'BC'    => 'British Columbia',
        'MB'    => 'Manitoba',
        'NB'    => 'New Brunswick',
        'NL'    => 'Newfoundland',
        'NS'    => 'Nova Scotia',
        'NT'    => 'Northwest Territory',
        'NU'    => 'Nunavut',
        'ON'    => 'Ontario',
        'PE'    => 'Prince Edward Island',
        'QC'    => 'Quebec',
        'SK'    => 'Saskatchewan',
        'YT'    => 'Yukon'
    );

    /**
     * USA states
     *
     * @var array
     */
    static protected $usStates = array
    (
        'AK'    => 'Alaska',
        'AL'    => 'Alabama',
        'AR'    => 'Arkansas',
        'AS'    => 'American Samoa',
        'AZ'    => 'Arizona',
        'CA'    => 'California',
        'CO'    => 'Colorado',
        'CT'    => 'Connecticut',
        'DC'    => 'D.C.',
        'DE'    => 'Delaware',
        'FL'    => 'Florida',
        'GA'    => 'Georgia',
        'GU'    => 'Guam',
        'HI'    => 'Hawaii',
        'IA'    => 'Iowa',
        'ID'    => 'Idaho',
        'IL'    => 'Illinois',
        'IN'    => 'Indiana',
        'KS'    => 'Kansas',
        'KY'    => 'Kentucky',
        'LA'    => 'Louisiana',
        'MA'    => 'Massachusetts',
        'MD'    => 'Maryland',
        'ME'    => 'Maine',
        'MI'    => 'Michigan',
        'MN'    => 'Minnesota',
        'MO'    => 'Missouri',
        'MS'    => 'Mississippi',
        'MT'    => 'Montana',
        'NC'    => 'North Carolina',
        'ND'    => 'North Dakota',
        'NE'    => 'Nebraska',
        'NH'    => 'New Hampshire',
        'NJ'    => 'New Jersey',
        'NM'    => 'New Mexico',
        'NV'    => 'Nevada',
        'NY'    => 'New York',
        'OH'    => 'Ohio',
        'OK'    => 'Oklahoma',
        'OR'    => 'Oregon',
        'PA'    => 'Pennsylvania',
        'PR'    => 'Puerto Rico',
        'RI'    => 'Rhode Island',
        'SC'    => 'South Carolina',
        'SD'    => 'South Dakota',
        'TN'    => 'Tennessee',
        'TX'    => 'Texas',
        'UT'    => 'Utah',
        'VA'    => 'Virginia',
        'VI'    => 'Virgin Islands',
        'VT'    => 'Vermont',
        'WA'    => 'Washington',
        'WI'    => 'Wisconsin',
        'WV'    => 'West Virginia',
        'WY'    => 'Wyoming'
    );

    /**
     * All known states, indexed by state code.
     * For lazy load use static::getStatesIndexedByCode()
     *
     * @see getStatesIndexedByCode()
     *
     * @var array
     */
    static protected $statesIndexedByCode = array();

    /**
     * All known states, indexed by state name.
     * For lazy load use static::getStatesIndexedByName()
     *
     * @see getStatesIndexedByName()
     *
     * @var array
     */
    static protected $statesIndexedByName = array();

    /**
     * True, if finder has country by code
     *
     * @param       string      $countryCode        Country code
     *
     * @return      boolean
     */
    static public function hasCountryByCode($countryCode)
    {
        return in_array($countryCode, static::$countryCodes);
    }

    /**
     * True, if country has states
     *
     * @param       string      $countryCode        Country code
     */
    static public function hasStates($countryCode)
    {
        return in_array($countryCode, static::$countriesWithStates);
    }

    /**
     * True, if finder has state by code
     *
     * @param       string      $stateCode      State code
     *
     * @return      boolean
     */
    static public function hasStateByCode($stateCode)
    {
        return array_key_exists($stateCode, static::getStatesIndexedByCode());
    }

    /**
     * True, if finder has state by name
     *
     * @param       string      $stateName      State name
     *
     * @return      boolean
     */
    static public function hasStateByName($stateName)
    {
        return array_key_exists($stateName, static::getStatesIndexedByName());
    }

    /**
     * Get state code by state name
     *
     * @param       string      $stateName      State name
     *
     * @return      string                      State code
     *
     * @throws      RuntimeException            Finder has no state with given name
     */
    static public function getStateCode($stateName)
    {
        if (!static::hasStateByName($stateName))
        {
            throw new RuntimeException("Unknown state name '{$stateName}'");
        }

        $statesIndexedByName = static::getStatesIndexedByName();

        return $statesIndexedByName[$stateName];
    }

    /**
     * Get all states, indexed by state code
     *
     * @return      array
     */
    static protected function getStatesIndexedByCode()
    {
        if (empty(static::$statesIndexedByCode))
        {
            static::$statesIndexedByCode = array_merge
            (
                static::$auStates,
                static::$caStates,
                static::$usStates
            );
        }

        return static::$statesIndexedByCode;
    }

    /**
     * Get all states, indexed by state name
     *
     * @return      array
     */
    static protected function getStatesIndexedByName()
    {
        if (empty(static::$statesIndexedByName))
        {
            static::$statesIndexedByName = array_flip(static::getStatesIndexedByCode());
        }

        return static::$statesIndexedByName;
    }
}
