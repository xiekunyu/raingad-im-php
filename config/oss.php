<?php
return [
    'accessKeyId'=>env('oss.accesskeyid', ''),
    'accessKeySecret'=>env('oss.accesskeysecret', ''),
    'endpoint'=>env('oss.endpoint', ''),
    'bucket'=>env('oss.bucket', ''),
    'ossUrl'=>env('oss.ossurl', '')
];