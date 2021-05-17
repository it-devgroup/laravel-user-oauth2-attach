<?php

namespace ItDevgroup\LaravelUserOAuth2Attach\Data;

/**
 * Class UserData
 * @package ItDevgroup\LaravelUserOAuth2Attach\Data
 */
class UserData
{
    /**
     * @var string|null
     */
    private ?string $id = null;
    /**
     * @var string|null
     */
    private ?string $email = null;
    /**
     * @var string|null
     */
    private ?string $phone = null;
    /**
     * @var string|null
     */
    private ?string $firstName = null;
    /**
     * @var string|null
     */
    private ?string $lastName = null;
    /**
     * @var string|null
     */
    private ?string $gender = null;
    /**
     * @var string|null
     */
    private ?string $link = null;
    /**
     * @var array|null
     */
    private ?array $properties = null;

    /**
     * UserOAuth2AttachUserData constructor.
     * @param string|null $id
     * @param string|null $email
     * @param string|null $phone
     * @param string|null $firstName
     * @param string|null $lastName
     * @param string|null $gender
     * @param string|null $link
     * @param array|null $properties
     */
    public function __construct(
        ?string $id,
        ?string $email,
        ?string $phone,
        ?string $firstName,
        ?string $lastName,
        ?string $gender,
        ?string $link,
        ?array $properties
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->phone = $phone;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->gender = $gender;
        $this->link = $link;
        $this->properties = $properties;
    }

    /**
     * @param string|null $id
     * @param string|null $email
     * @param string|null $phone
     * @param string|null $firstName
     * @param string|null $lastName
     * @param string|null $gender
     * @param string|null $link
     * @param array|null $properties
     * @return self
     */
    public static function register(
        ?string $id,
        ?string $email,
        ?string $phone,
        ?string $firstName,
        ?string $lastName,
        ?string $gender,
        ?string $link,
        ?array $properties
    ): self {
        return new self(
            $id,
            $email,
            $phone,
            $firstName,
            $lastName,
            $gender,
            $link,
            $properties
        );
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string|null $id
     */
    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     */
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     */
    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     */
    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string|null
     */
    public function getGender(): ?string
    {
        return $this->gender;
    }

    /**
     * @param string|null $gender
     */
    public function setGender(?string $gender): void
    {
        $this->gender = $gender;
    }

    /**
     * @return string|null
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * @param string|null $link
     */
    public function setLink(?string $link): void
    {
        $this->link = $link;
    }

    /**
     * @return array|null
     */
    public function getProperties(): ?array
    {
        return $this->properties;
    }

    /**
     * @param array|null $properties
     */
    public function setProperties(?array $properties): void
    {
        $this->properties = $properties;
    }
}
