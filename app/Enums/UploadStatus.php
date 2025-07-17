<?php

namespace App\Enums;

enum UploadStatus: string
{
    case Pending = 'Pending';
    case Processing = 'Processing';
    case Completed = 'Completed';
    case Failed = 'Failed';
}
