<?php

namespace PHPKitchen\Domain\Contracts;

/**
 * Represents {@link DomainEntity} data source.
 *
 * @package PHPKitchen\Domain
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
interface EntityDataSource {
    /**
     * Saves the current record.
     *
     * This method will call [[insert()]] when [[isNewRecord]] is true, or [[update()]]
     * when [[isNewRecord]] is false.
     *
     * For example, to save a customer record:
     *
     * ```php
     * $customer = new Customer; // or $customer = Customer::findOne($id);
     * $customer->name = $name;
     * $customer->email = $email;
     * $customer->save();
     * ```
     * before saving the record. Defaults to `true`. If the validation fails, the record
     * will not be saved to the database and this method will return `false`.
     *
     * @param array|null $attributeNames list of attribute names that need to be saved. Defaults to null,
     * meaning all attributes that are loaded from DB will be saved.
     *
     * @return boolean whether the saving succeeded (i.e. no validation errors occurred).
     */
    public function validateAndSave(?array $attributeNames = null): bool;

    /**
     * Saves the current record.
     *
     * This method will call [[insert()]] when [[isNewRecord]] is true, or [[update()]]
     * when [[isNewRecord]] is false.
     *
     * For example, to save a customer record:
     *
     * ```php
     * $customer = new Customer; // or $customer = Customer::findOne($id);
     * $customer->name = $name;
     * $customer->email = $email;
     * $customer->save();
     * ```
     * before saving the record. Defaults to `true`. If the validation fails, the record
     * will not be saved to the database and this method will return `false`.
     *
     * @param array|null $attributeNames list of attribute names that need to be saved. Defaults to null,
     * meaning all attributes that are loaded from DB will be saved.
     *
     * @return boolean whether the saving succeeded (i.e. no validation errors occurred).
     */
    public function saveWithoutValidation(?array $attributeNames = null): bool;

    /**
     * Deletes the table row corresponding to this active record.
     *
     * @return integer|false the number of rows deleted, or false if the deletion is unsuccessful for some reason.
     * Note that it is possible the number of rows deleted is 0, even though the deletion execution is successful.
     */
    public function deleteRecord();

    /**
     * Returns the named attribute value.
     * If this record is the result of a query and the attribute is not loaded,
     * null will be returned.
     *
     * @param string $name the attribute name
     *
     * @return mixed the attribute value. Null if the attribute is not set or does not exist.
     */
    public function getAttribute(string $name);
}
