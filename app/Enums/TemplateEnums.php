<?php

namespace App\Enums;
use BenSampo\Enum\Enum;

final class TemplateEnums extends Enum
{
    public const TEMPLATE_CHANNEL_ZNZ = 'ZNS';
    public const TEMPLATE_CHANNEL_CUSTOM_OA = 'CUSTOM_OA';
    
    public const TEMPLATE_STATUS_ENABLE = 'ENABLE';
    public const TEMPLATE_STATUS_DISABLE = 'DISABLE';
    public const TEMPLATE_STATUS_REJECT = 'REJECT';
    public const TEMPLATE_STATUS_PENDING_REVIEW = 'PENDING_REVIEW';

    public const TEMPLATE_QUALITY_HIGH      = 'HIGH';
    public const TEMPLATE_QUALITY_MEDIUM    = 'MEDIUM';
    public const TEMPLATE_QUALITY_LOW       = 'LOW';
    public const TEMPLATE_QUALITY_UNDEFINED = 'UNDEFINED';
    
    public const TEMPLATE_TAG_TRANSACTION   = 'TRANSACTION';
    public const TEMPLATE_TAG_CUSTOMER_CARE = 'CUSTOMER_CARE';
    public const TEMPLATE_TAG_PROMOTION     = 'PROMOTION';
}
