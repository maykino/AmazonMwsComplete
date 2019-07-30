<?php

namespace CaponicaAmazonMwsComplete\ClientPack;

use CaponicaAmazonMwsComplete\ClientPool\MwsClientPoolConfig;
use CaponicaAmazonMwsComplete\AmazonClient\MwsOrderClient;


class MwsOrderClientPack extends MwsOrderClient {
    const PARAM_MERCHANT                            = 'Merchant';
    const PARAM_SELLER_ID                           = 'SellerId';
    const PARAM_MARKETPLACE_ID_LIST                 = 'MarketplaceIdList';
    const PARAM_MARKETPLACE_ID                      = 'MarketplaceId';
    const PARAM_MWS_AUTH_TOKEN                      = 'MWSAuthToken';
    const PARAM_CREATED_AFTER                       = 'CreatedAfter';
    const PARAM_CREATED_BEFORE                      = 'CreatedBefore';
    const PARAM_LAST_UPDATED_AFTER                  = 'LastUpdatedAfter';

    const PARAM_REPORT_REQUEST_ID_LIST              = 'ReportRequestIdList';
    const PARAM_ORDER_STATUS_LIST                   = 'OrderStatus';
    const PARAM_AMAZON_ORDER_ID                     = 'AmazonOrderId';
    const PARAM_NEXT_TOKEN                          = 'NextToken';
    const STATUS_PENDING                            = 'Pending';
    const STATUS_UNSHIPPED                          = 'Unshipped';
    const STATUS_PARTIALLY_SHIPPED                  = 'PartiallyShipped';
    const STATUS_SHIPPED                            = 'Shipped';
    const STATUS_CANCELED                           = 'Canceled';
    const STATUS_UNFULFILLABLE                      = 'Unfulfillable';
    const STATUS_PENDING_AVAILABILITY               = 'PendingAvailability';
    const PARAM_FULFILLMENT_CHANNEL                 = 'FulfillmentChannel';
    const PARAM_FULFILLMENT_CHANNEL_AFN             = 'AFN';
    const PARAM_FULFILLMENT_CHANNEL_MFN             = 'MFN';


    /** @var string $marketplaceId      The MWS MarketplaceID string used in API connections */
    protected $marketplaceId;
    /** @var string $sellerId           The MWS SellerID string used in API connections */
    protected $sellerId;

    public function __construct(MwsClientPoolConfig $poolConfig) {

        $this->marketplaceId    = $poolConfig->getMarketplaceId($poolConfig->getAmazonSite());
        $this->sellerId         = $poolConfig->getSellerId();
        $this->mwsAuthToken     = $poolConfig->getMwsAuthToken();

        parent::__construct(
            $poolConfig->getAccessKey(),
            $poolConfig->getSecretKey(),
            $poolConfig->getApplicationName(),
            $poolConfig->getApplicationVersion(),
            $poolConfig->getConfigForOrder($this->getServiceUrlSuffix())
        );
    }

    private function getServiceUrlSuffix() {
        return '/Orders/';
    }
    // ##################################################
    // #      basic wrappers for API calls go here      #
    // ##################################################    

    public function callListOrdersRequest($createdAfter='', $lastUpdatedAfter='', $fulfillmentChannel='', $orderStatus) {

        if (!empty($createdAfter)) {
            $parameters[self::PARAM_CREATED_AFTER] = $createdAfter;
        }
        if (!empty($lastUpdatedAfter)) {
            $parameters[self::PARAM_LAST_UPDATED_AFTER] = $lastUpdatedAfter;
        }
        if (!empty($fulfillmentChannel)) {
            $parameters[self::PARAM_FULFILLMENT_CHANNEL] = $fulfillmentChannel;
        }
        $parameters[self::PARAM_MWS_AUTH_TOKEN] = $this->mwsAuthToken;
        $parameters[self::PARAM_SELLER_ID] = $this->sellerId;
        $parameters[self::PARAM_MERCHANT] = $this->sellerId;
        $parameters[self::PARAM_MARKETPLACE_ID_LIST] = array('Id' => $this->marketplaceId);
        $parameters[self::PARAM_ORDER_STATUS_LIST] = $orderStatus;

        

        return $this->listOrders(
            array_merge([self::PARAM_MARKETPLACE_ID  => $this->marketplaceId], $parameters)
        );
    }

    public function callListOrdersRequestByNextToken($nextToken) {

        return $this->listOrdersByNextToken([
            self::PARAM_NEXT_TOKEN          => $nextToken,
            self::PARAM_SELLER_ID            => $this->sellerId,
        ]);
    }

    public function calllistOrderItems($amazonOrderId)
    {

        $parameters = [
            self::PARAM_MARKETPLACE_ID  => $this->marketplaceId,
            self::PARAM_SELLER_ID => $this->sellerId,
            self::PARAM_MERCHANT  => $this->sellerId,
            self::PARAM_MARKETPLACE_ID_LIST => array('Id' => $this->marketplaceId),
            self::PARAM_AMAZON_ORDER_ID => $amazonOrderId,
            self::PARAM_MWS_AUTH_TOKEN => $this->mwsAuthToken
            ];

        return $this->listOrderItems($parameters);
    }
    public function callGetOrder($amazonOrderId) {
        $parameters = [
            self::PARAM_MARKETPLACE_ID  => $this->marketplaceId,
            self::PARAM_SELLER_ID => $this->sellerId,
            self::PARAM_MERCHANT  => $this->sellerId,
            self::PARAM_MARKETPLACE_ID_LIST => array('Id' => $this->marketplaceId),
            self::PARAM_AMAZON_ORDER_ID => $amazonOrderId,
            self::PARAM_MWS_AUTH_TOKEN => $this->mwsAuthToken
        ];

        return $this->getOrder($parameters);
    }

}