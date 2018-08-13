<?php

namespace yidas\google\apiHelper\services;

use Exception;
use Google_Service_PeopleService;
use Google_Service_PeopleService_Person;
use Google_Service_PeopleService_Name;
use Google_Service_PeopleService_EmailAddress;
use Google_Service_PeopleService_PhoneNumber;

/**
 * People Service Helper
 * 
 * @author  Nick Tsai <myintaer@gmail.com>
 * @since   1.0.0 
 * @example Exception
 *  try {} catch (Google_Exception $e) {}
 *  try {} catch (Google_Service_Exception $e) {}
 *  
 */
class People extends \yidas\google\apiHelper\AbstractService
{
    /**
     * Current Service class name
     *
     * @var string $serviceClass
     */
    protected static $serviceClass = 'Google_Service_PeopleService';

    /**
     * Google_Service_PeopleService_Person
     *
     * @var object Google_Service_PeopleService_Person
     */
    protected static $person;

    /**
     * The current resource name of the contact
     *
     * @var object The resource name of the contact
     */
    protected static $resourceName;

    /**
     * Google People Person method attribute map
     * 
     * Only needs for non-exist helper method, plural person method or simple attribute
     * 
     * @todo Special input attribute overwriting
     */
    protected static $personAttrMap = [
        'setAddresses' => '\Google_Service_PeopleService_Address',
        'setAgeRange' => '\Google_Service_PeopleService_AgeRangeType',
        'setAgeRanges' => '\Google_Service_PeopleService_AgeRangeType',
        'setBiographies' => '\Google_Service_PeopleService_Biography',
        // 'setBirthdays' => '\Google_Service_PeopleService_Birthday',
        'setBraggingRights' => '\Google_Service_PeopleService_BraggingRights',
        'setCoverPhotos' => '\Google_Service_PeopleService_CoverPhoto',
        'setEmailAddresses' => '\Google_Service_PeopleService_EmailAddress',
        'setEvents' => '\Google_Service_PeopleService_Event',
        'setGenders' => '\Google_Service_PeopleService_Gender',
        'setImClients' => '\Google_Service_PeopleService_ImClient',
        'setInterests' => '\Google_Service_PeopleService_Interest',
        'setLocales' => '\Google_Service_PeopleService_Locale',
        'setMemberships' => '\Google_Service_PeopleService_Membership',
        'setNicknames' => '\Google_Service_PeopleService_Nickname',
        'setOccupations' => '\Google_Service_PeopleService_Occupation',
        'setOrganizations' => '\Google_Service_PeopleService_Organization',
        'setPhoneNumbers' => '\Google_Service_PeopleService_PhoneNumber',
        'setPhotos' => '\Google_Service_PeopleService_Photo',
        'setRelations' => '\Google_Service_PeopleService_Relation',
        'setRelationshipInterests' => '\Google_Service_PeopleService_RelationshipInterest',
        'setRelationshipStatuses' => '\Google_Service_PeopleService_RelationshipStatus',
        'setResidences' => '\Google_Service_PeopleService_Residence',
        'setSipAddresses' => '\Google_Service_PeopleService_SipAddress',
        'setSkills' => '\Google_Service_PeopleService_Skill',
        'setTaglines' => '\Google_Service_PeopleService_Tagline',
        'setUrls' => '\Google_Service_PeopleService_Url',
        ];

    /**
     * Person's fields
     *
     * @var array
     */
    protected static $personFields = [
        'addresses',
        'ageRanges',
        'biographies',
        'birthdays',
        'braggingRights',
        'coverPhotos',
        'emailAddresses',
        'events',
        'genders',
        'imClients',
        'interests',
        'locales',
        'memberships',
        'metadata',
        'names',
        'nicknames',
        'occupations',
        'organizations',
        'phoneNumbers',
        'photos',
        'relations',
        'relationshipInterests',
        'relationshipStatuses',
        'residences',
        'skills',
        'taglines',
        'urls',
    ];

    /**
     * New a Google_Service_PeopleService_Person
     *
     * @return self
     */
    public static function newPerson()
    {
        self::$person = new Google_Service_PeopleService_Person;       

        return new self;
    }

    /**
     * Get Google_Service_PeopleService_Person
     *
     * @return Google_Service_PeopleService_Person
     */
    public static function getPerson()
    {
        if (!self::$person) {

            self::newPerson();
        }

        return self::$person;
    }

    /**
     * find a contact to cache
     *
     * @param string $resourceName The resource name of the contact.
     * @param array $optParams
     * @return self
     */
    public static function findByResource($resourceName, $optParams=[])
    {
        $optParams = ($optParams) ? $optParams : [
            'personFields' => self::$personFields,
        ];
        
        self::$person = self::getService()->people->get($resourceName, $optParams);
        
        self::$resourceName = $resourceName;
        
        return new self;
    }

    /**
     * list People Connections
     *
     * @param array $optParams
     * @return object Google listPeopleConnections()
     */
    public static function listPeopleConnections($optParams=[])
    {
        $optParams = ($optParams) ? $optParams : [
            'pageSize' => 0,
            'personFields' => self::$personFields,
        ];

        return self::getService()->people_connections->listPeopleConnections('people/me', $optParams);
    }

    /**
     * Get simple contact data with parser
     *
     * @return array Contacts
     */
    public static function getSimpleContacts()
    {
        $contactObj = self::listPeopleConnections();

        // Parser
        $contacts = [];
            
        if (count($contactObj->getConnections()) != 0) {
            
            foreach ($contactObj->getConnections() as $person) {

                // var_dump($person);exit;
                $data = [];
                // Resource name
                $data['id'] = $person->getResourceName();
                $data['name'] = isset($person->getNames()[0]) 
                    ? $person->getNames()[0]->getDisplayName() 
                    : null;
                $data['email'] = isset($person->getEmailAddresses()[0]) 
                    ? $person->getEmailAddresses()[0]->getValue() 
                    : null;
                $data['phone'] = isset($person->getPhoneNumbers()[0]) 
                    ? $person->getPhoneNumbers()[0]->getValue() 
                    : null;

                $contacts[] = $data;
            }
        }
        
        return $contacts;
    }
    
    /**
     * Create a People Contact
     *
     * @return Google_Service_PeopleService_Person
     */
    public static function createContact()
    {
        $person = self::getPerson();

        // New person check
        if (isset($person->resourceName)) {
            throw new Exception("You should use newPeron() before create", 500);
        }
        
        return self::getService()->people->createContact($person);
    }

    /**
     * Update a People Contact
     *
     * @param array $optParams Optional parameters.
     * @return Google_Service_PeopleService_PeopleEmpty
     */
    public static function updateContact($optParams=null)
    {
        self::checkFind();
        
        // Default opt helper
        $optParams = (is_null($optParams))
            ? ['updatePersonFields' => self::$personFields] 
            : $optParams;
        
        return self::getService()->people->updateContact(self::$resourceName, self::getPerson(), $optParams);
    }

    /**
     * Delete a People Contact
     * (people.deleteContact)
     *
     * @param string $resourceName The resource name of the contact to delete.
     * @param array $optParams Optional parameters.
     * @return Google_Service_PeopleService_PeopleEmpty
     */
    public static function deleteContact($resourceName=null, $optParams=[])
    {
        if (!$resourceName) {
            
            self::checkFind();

            $resourceName = self::$resourceName;
        }

        return self::getService()->people->deleteContact($resourceName, $optParams);
    }
    
    /**
     * Set Names
     *
     * @param string|array Simple name or name options
     * @return self
     */
    public static function setNames($input='')
    {
        // Input type process
        $input = (is_string($input)) ? ['givenName' => $input] : $input;
        // Default options
        $default = [
            'givenName' => '',
            'middleName' => '',
            'familyName' => '',
        ];
        // Merged options
        $input = array_merge($default, $input);
        
        // New a current object from PeopleService
        $object = new Google_Service_PeopleService_Name;
        // First name
        $object->setGivenName($input['givenName']);
        // Middle name
        $object->setMiddleName($input['middleName']);
        // Last name
        $object->setFamilyName($input['familyName']);

        self::getPerson()->setNames($object);

        return new self;
    }

    /**
     * Alias of __callStatic
     */
    public function __call(string $name, array $arguments)
    {
        return self::__callStatic($name, $arguments);
    }

    /**
     * Smart call for setting Person attribute
     * 
     * This magic call only for simple People attribute with `setValue()`
     *
     * @param string $name
     * @param array $arguments
     * @return void
     */
    public static function __callStatic(string $name, array $arguments)
    {
        // Attribute class mapping
        $class = isset(self::$personAttrMap[$name]) ? self::$personAttrMap[$name] : '';
        if (!class_exists($class)) {
            throw new Exception("Method {$name}() does not exists referred to {$class}.", 500);
        }

        // Parameter for Attribute class
        $value = isset($arguments[0]) ? $arguments[0] : null;

        // New a current object from PeopleService
        $object = new $class;
        // Set value
        $object->setValue($value);
        
        // Call Person alias method
        $result = self::getPerson()->$name($object);

        return new self;
    }

    /**
     * List all contact groups owned by the authenticated user. Members of the
     * contact groups are not populated. (contactGroups.listContactGroups)
     *
     * @param array $optParams Optional parameters.
     *
     * @opt_param int pageSize The maximum number of resources to return.
     * @opt_param string syncToken A sync token, returned by a previous call to
     * `contactgroups.list`. Only resources changed since the sync token was created
     * will be returned.
     * @opt_param string pageToken The next_page_token value returned from a
     * previous call to [ListContactGroups](/people/api/rest/v1/contactgroups/list).
     * Requests the next page of resources.
     * @return array Data which is part of Google_Service_PeopleService_ListContactGroupsResponse
     */
    public static function listContactGroups($optParams=[])
    {
        $contactGroup = self::getService()->contactGroups->listContactGroups();

        if (!$contactGroup) {
            return $contactGroup;
        }

        return json_decode(json_encode($contactGroup->contactGroups), true);
    }

    /**
     * Check current person
     *
     * @return void
     */
    protected static function checkFind()
    {
        if (!self::$person) {

            throw new Exception("Person not found", 404);  
        } 
        elseif (!self::$resourceName) {

            throw new Exception("ResourceName is empty", 500);
        }
    }
}
