<?php
/**
 * Copyright © Overdose Digital. All rights reserved.
 * See LICENSE_OVERDOSE.txt for license details.
 */

namespace Overdose\CustomerPasswordReHash\Plugin\Customer\Model;

use Magento\Customer\Model\Authentication;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\ResourceModel\Customer as CustomerResourceModel;
use Magento\Framework\Encryption\EncryptorInterface as Encryptor;
use Magento\Framework\Exception\NoSuchEntityException;
use Overdose\CustomerPasswordReHash\Model\PasswordVerifier;

/**
 * Plugin for Authentication
 */
class AuthenticationPlugin
{
    /**
     * @var CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var CustomerResourceModel
     */
    private $customerResourceModel;

    /**
     * @var PasswordVerifier
     */
    private $passwordVerifier;

    /**
     * @var Encryptor
     */
    private $encryptor;

    /**
     * @param CustomerRegistry $customerRegistry
     * @param CustomerResourceModel $customerResourceModel
     * @param PasswordVerifier $passwordVerifier
     * @param Encryptor $encryptor
     */
    public function __construct(
        CustomerRegistry $customerRegistry,
        CustomerResourceModel $customerResourceModel,
        PasswordVerifier $passwordVerifier,
        Encryptor $encryptor
    ) {
        $this->customerRegistry = $customerRegistry;
        $this->customerResourceModel = $customerResourceModel;
        $this->passwordVerifier = $passwordVerifier;
        $this->encryptor = $encryptor;
    }

    /**
     * Replace customer password hash in case it is Bcrypt algorithm
     *
     * @param Authentication $subject
     * @param int $customerId
     * @param string $password
     * @return void
     * @throws NoSuchEntityException
     */
    public function beforeAuthenticate(
        Authentication $subject,
        int $customerId,
        string $password
    ): void {
        $customerSecure = $this->customerRegistry->retrieveSecureData($customerId);
        $hash = $customerSecure->getPasswordHash();
        // M1 Community || M1 Enterprise
        if (($this->passwordVerifier->isBcrypt($hash) && $this->passwordVerifier->verifyBcrypt($password, $hash))
            || ($this->passwordVerifier->isSha256($hash) && $this->passwordVerifier->verifySha256($hash, $password))
        ) {
            $this->customerRegistry->remove($customerId);
            $hash = $this->encryptor->getHash($password, true);
            $this->customerResourceModel->getConnection()->update(
                $this->customerResourceModel->getTable('customer_entity'),
                [
                    'password_hash' => $hash
                ],
                $this->customerResourceModel->getConnection()->quoteInto('entity_id = ?', $customerId)
            );
        }
    }
}
