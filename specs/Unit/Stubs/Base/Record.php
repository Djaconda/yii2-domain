<?php

namespace PHPKitchen\Domain\Specs\Unit\Stubs\Base;

/**
 * Represents
 *
 * @package tests\stubs
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class Record extends \PHPKitchen\Domain\DB\Record {
    protected $saveResult = true;
    protected $deleteResult = true;
    protected $emulatedErrors = [];

    public function attributes() {
        return [
            'id',
            'fieldOne',
            'fieldTwo',
        ];
    }

    public function save($runValidation = true, $attributeNames = null) {
        return $this->saveResult;
    }

    public function delete() {
        return $this->deleteResult;
    }

    public function getErrors($attribute = null) {
        return $this->emulatedErrors;
    }

    public function emulateSuccessSaveResult() {
        $this->saveResult = true;

        return $this;
    }

    public function emulateFailedSaveResult($errors = []) {
        $this->saveResult = false;
        $this->emulatedErrors = $errors;

        return $this;
    }

    public function emulateSuccessDeleteResult() {
        $this->deleteResult = true;

        return $this;
    }

    public function emulateFailedDeleteResult($errors = []) {
        $this->saveResult = false;
        $this->emulatedErrors = $errors;

        return $this;
    }

    public static function primaryKey() {
        return ['id'];
    }
}
