<?php

namespace Overdose\CustomerPasswordReHash\Cron;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Customer\Model\ResourceModel\Visitor\CollectionFactory as VisitorCollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Overdose\CustomerPasswordReHash\Model\PasswordVerifier;

class SendMailToAdmin
{
    const KEY_OD_AMOUNT_MONTHS = 'rehash_passwd/general/od_amount_months';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var CollectionFactory
     */
    protected $customerCollection;

    /**
     * @var PasswordVerifier
     */
    protected $passwordVerifier;

    /**
     * @var VisitorCollectionFactory
     */
    protected $visitorCollectionFactory;

    /**
     * @var TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var StateInterface
     */
    protected $inlineTranslation;

    /**
     * SendMailToAdmin constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param CollectionFactory $customerCollection
     * @param PasswordVerifier $passwordVerifier
     * @param VisitorCollectionFactory $visitorCollectionFactory
     * @param TimezoneInterface $_localeDate
     * @param TransportBuilder $transportBuilder
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        CollectionFactory $customerCollection,
        PasswordVerifier $passwordVerifier,
        VisitorCollectionFactory $visitorCollectionFactory,
        TimezoneInterface $_localeDate,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->customerCollection = $customerCollection;
        $this->passwordVerifier = $passwordVerifier;
        $this->visitorCollectionFactory = $visitorCollectionFactory;
        $this->_localeDate = $_localeDate;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
    }

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        //get amount of months from admin panel
        $months = $this->scopeConfig->getValue(
            $this::KEY_OD_AMOUNT_MONTHS,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );

        $collection = $this->customerCollection->create();
        $customerCollection = $collection->addAttributeToSelect('*')->getItems();

        /** @var Customer $customer */
        foreach ($customerCollection as $customer) {
            if ($customer->getPasswordHash()) {
                $validatedHash = $this->validateHash($customer->getPasswordHash());
                if (!$validatedHash) {//if not rehashed check last visit
                    $checked = $this->checkLastVizited($months, $customer->getId());

                    if ($checked) {
                        $this->sentMail();
                    }
                }
            }
        }
        return $this;
    }

    /**
     * @param $hash
     * @return bool
     */
    public function validateHash($hash)
    {
        if ($this->passwordVerifier->isBcrypt($hash) || $this->passwordVerifier->isSha256($hash)) {
            return false;
        }
        return true;
    }

    /**
     * @param $months
     * @param $customerId
     * @return bool
     */
    public function checkLastVizited($months, $customerId)
    {
        $currentTime = $this->_localeDate->date();
        $modifiedTime = $currentTime->modify("-$months month")
            ->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        $visitorCollection = $this->visitorCollectionFactory->create();
        $visitorCollection->addFieldToSelect('*')->addFieldToFilter('customer_id', ['eq' => $customerId]);
        $lastVisitTime = $visitorCollection->getFirstItem()->getLastVisitAt();

        if (isset($lastVisitTime)) {
            return $modifiedTime > $lastVisitTime ? true : false;
        }
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sentMail()
    {
        $this->inlineTranslation->suspend();
        $sender = [
            'name' => 'Overdose_CustomerPasswordReHash',
            'email' => 'owner@example.com'
        ];

        $sentToEmail = $this->scopeConfig
            ->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $sentToName = $this->scopeConfig
            ->getValue('trans_email/ident_general/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $transport = $this->transportBuilder
            ->setTemplateIdentifier('od_send_message_admin_template')
            ->setTemplateOptions(
                [
                    'area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ]
            )
            ->setTemplateVars([
                'data'=> 'All passwords were rehashed! You can to disable module "Overdose_CustomerPasswordReHash".'
            ])
            ->setFromByScope($sender)
            ->addTo($sentToEmail, $sentToName)
            //->addTo('owner@example.com','owner')
            ->getTransport();

        $transport->sendMessage();

        $this->inlineTranslation->resume();
    }
}
