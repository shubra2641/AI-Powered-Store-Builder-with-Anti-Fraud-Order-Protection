<?php

namespace App\Services;

enum DS_BulkDeleteResult: string
{
    case SUCCESS = 'success';
    case PARTIAL = 'partial';
    case NONE_SELF = 'none_self';
    case NONE_DEFAULT = 'none_default';
}
