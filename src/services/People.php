<?php

namespace yidas\google\apiHelper\services;

use Google_Service_PeopleService;
use Google_Service_PeopleService_Person;
use Google_Service_PeopleService_Name;
use Google_Service_PeopleService_EmailAddress;
use Google_Service_PeopleService_PhoneNumber;

/**
 * People Service Helper
 * 
 * @author  Nick Tsai <myintaer@gmail.com>
 * @since   0.1.0 
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
     * Get Google_Service_PeopleService_Person
     *
     * @return Google_Service_PeopleService_Person
     */
    public function getPerson()
    {
        if (!self::$person) {

            self::$person = new Google_Service_PeopleService_Person;
        }

        return self::$person;
    }

    /**
     * find a contact to cache
     *
     * @param string $resourceName The resource name of the contact.
     * @return self
     */
    public function findByResource($resourceName)
    {
        self::$person = self::getService()->people->get($resourceName);
        
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
        return self::getService()->people->createContact(self::getPerson());
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
     * Set EmailAddresses
     *
     * @param string|array Simple emailAddress or options
     * @return self
     */
    public static function setEmailAddresses($input='')
    {
        // Input type process
        $input = (is_string($input)) ? ['value' => $input] : $input;
        // Default options
        $default = [
            'value' => '',
        ];
        // Merged options
        $input = array_merge($default, $input);

        // New a current object from PeopleService
        $object = new Google_Service_PeopleService_EmailAddress;
        // Value
        $object->setValue($input['value']);

        self::getPerson()->setEmailAddresses($object);

        return new self;
    }

    /**
     * Set PhoneNumbers
     *
     * @param string|array Simple phoneNumbers or options
     * @return self
     */
    public static function setPhoneNumbers($input='')
    {
        // Input type process
        $input = (is_string($input)) ? ['value' => $input] : $input;
        // Default options
        $default = [
            'value' => '',
        ];
        // Merged options
        $input = array_merge($default, $input);

        // New a current object from PeopleService
        $object = new Google_Service_PeopleService_PhoneNumber;
        // Value
        $object->setValue($input['value']);

        self::getPerson()->setPhoneNumbers($object);

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
    public function listContactGroups($optParams=[])
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
