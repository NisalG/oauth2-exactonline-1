<?php

namespace Picqer\OAuth2\Client\Provider;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;


/**
 * Class ExactOnlineUser
 * @package Stephangroen\OAuth2\Client\Provider
 */
class ExactOnlineUser implements ResourceOwnerInterface
{

    /**
     * @var array
     */
    protected $data;


    /**
     * @param array $response
     */
    public function __construct(array $response)
    {
        $this->data = $response;
    }


    /**
     * @return null|string
     */
    public function getUserId()
    {
        return $this->getField('UserID');
    }


    /**
     * @return null|string
     */
    public function getCurrentDivision()
    {
        return $this->getField('CurrentDivision');
    }


    /**
     * @return null|string
     */
    public function getFullName()
    {
        return $this->getField('FullName');
    }


    /**
     * @return null|string
     */
    public function getPictureUrl()
    {
        return $this->getField('PictureUrl');
    }


    /**
     * @return null|string
     */
    public function getUserName()
    {
        return $this->getField('UserName');
    }


    /**
     * @return null|string
     */
    public function getLanguageCode()
    {
        return $this->getField('LanguageCode');
    }


    /**
     * @return null|string
     */
    public function getEmail()
    {
        return $this->getField('Email');
    }


    /**
     * @return null|string
     */
    public function getTitle()
    {
        return $this->getField('Title');
    }


    /**
     * @return null|string
     */
    public function getInitials()
    {
        return $this->getField('Initials');
    }


    /**
     * @return null|string
     */
    public function getFirstName()
    {
        return $this->getField('FirstName');
    }


    /**
     * @return null|string
     */
    public function getMiddleName()
    {
        return $this->getField('MiddleName');
    }


    /**
     * @return null|string
     */
    public function getLastName()
    {
        return $this->getField('LastName');
    }


    /**
     * @return null|string
     */
    public function getGender()
    {
        return $this->getField('Gender');
    }


    /**
     * @return null|string
     */
    public function getLanguage()
    {
        return $this->getField('Language');
    }


    /**
     * @return null|string
     */
    public function getPhone()
    {
        return $this->getField('Phone');
    }


    /**
     * @return string|null
     */
    public function getPhoneExtension()
    {
        return $this->getField('PhoneExtension');
    }


    /**
     * @return null|string
     */
    public function getMobile()
    {
        return $this->getField('Mobile');
    }


    /**
     * Returns a user field
     *
     * @param $key
     *
     * @return string|null
     */
    private function getField($key)
    {
        return isset( $this->data[$key] ) ? $this->data[$key] : null;
    }

    /**
     * Get the identifier of the authorized resource owner.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->getUserId();
    }
}