<?php
namespace AliOpen\CloudApi;

use AliOpen\Core\RpcAcsRequest;

/**
 * Request of DescribeApisBySignature
 * @method string getSignatureId()
 * @method string getPageNumber()
 * @method string getSecurityToken()
 * @method string getPageSize()
 */
class ApisBySignatureDescribeRequest extends RpcAcsRequest
{
    /**
     * @var string
     */
    protected $method = 'POST';

    /**
     * Class constructor.
     */
    public function __construct()
    {
        parent::__construct('CloudAPI', '2016-07-14', 'DescribeApisBySignature', 'apigateway');
    }

    /**
     * @param string $signatureId
     * @return $this
     */
    public function setSignatureId($signatureId)
    {
        $this->requestParameters['SignatureId'] = $signatureId;
        $this->queryParameters['SignatureId'] = $signatureId;

        return $this;
    }

    /**
     * @param string $pageNumber
     * @return $this
     */
    public function setPageNumber($pageNumber)
    {
        $this->requestParameters['PageNumber'] = $pageNumber;
        $this->queryParameters['PageNumber'] = $pageNumber;

        return $this;
    }

    /**
     * @param string $securityToken
     * @return $this
     */
    public function setSecurityToken($securityToken)
    {
        $this->requestParameters['SecurityToken'] = $securityToken;
        $this->queryParameters['SecurityToken'] = $securityToken;

        return $this;
    }

    /**
     * @param string $pageSize
     * @return $this
     */
    public function setPageSize($pageSize)
    {
        $this->requestParameters['PageSize'] = $pageSize;
        $this->queryParameters['PageSize'] = $pageSize;

        return $this;
    }
}
