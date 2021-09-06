<?php

declare(strict_types=1);

namespace FromHome\Payment\ValueObject;

use Psl\Type;

final class Customer
{
    private string $firstName;

    private string $lastName;

    private string $email;

    private string $phone;

    private string $address;

    private string $city;

    private string $postalCode;

    private string $countryCode;

    private array $input;

    /**
     * @param array{
     *  firstName: string,
     *  lastName?: string,
     *  email?: string,
     *  phone?: string,
     *  address?: string,
     *  city?: string,
     *  postalCode?: string,
     *  countryCode?: string
     * } $input
     */
    public function __construct(array $input)
    {
        $input = Type\shape([
            'firstName' => Type\non_empty_string(),
            'lastName' => Type\optional(Type\union(Type\null(), Type\non_empty_string())),
            'email' => Type\optional(Type\union(Type\null(), Type\non_empty_string())),
            'phone' => Type\optional(Type\union(Type\null(), Type\non_empty_string())),
            'address' => Type\optional(Type\union(Type\null(), Type\non_empty_string())),
            'city' => Type\optional(Type\union(Type\null(), Type\non_empty_string())),
            'postalCode' => Type\optional(Type\union(Type\null(), Type\non_empty_string())),
            'countryCode' => Type\optional(Type\union(Type\null(), Type\non_empty_string())),
        ], true)->coerce($input);

        $this->firstName = $input['firstName'];
        $this->lastName = $input['lastName'] ?? '';
        $this->email = $input['email'] ?? '';
        $this->phone = $input['phone'] ?? '';
        $this->address = $input['address'] ?? '';
        $this->city = $input['city'] ?? '';
        $this->postalCode = $input['postalCode'] ?? '';
        $this->countryCode = $input['countryCode'] ?? '';

        $this->input = $input;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getInput(): array
    {
        return $this->input;
    }

    public function getName(): string
    {
        return \sprintf('%s %s', $this->getFirstName(), $this->getLastName());
    }
}
